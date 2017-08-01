<?php

/**
 * Created by PhpStorm.
 * User: ITJaye
 * 修改了多个sql语句 调整了相关的逻辑 增加了用户数据表相关操作
 * Date: 2017/8/2
 */
class Csessioninfo_Service
{

    public function __construct()
    {
        require_once('system/db/mysql_db.php');
    }

    public function insert_csessioninfo($params)
    {

        $insert_sql = 'insert into cSessionInfo set uuid = "'.$params['uuid'].'",skey = "' . $params['skey'] . '",create_time = "' . $params['create_time'] . '",last_visit_time = "' . $params['last_visit_time'] . '",open_id = "' . $params['openid'] . '",session_key="' . $params['session_key'] . '",unionId="'.$params['unionId'].'"';
        $mysql_insert = new mysql_db();
        return $mysql_insert->query_db($insert_sql);
    }



    public function update_csessioninfo_time($params)
    {
        $update_sql = 'update cSessionInfo set last_visit_time = "' . $params['last_visit_time'] . '" where uuid = "' . $params['uuid'].'"';
        $mysql_update = new mysql_db();
        return $mysql_update->query_db($update_sql);
    }


    public function update_csessioninfo($params)
    {
        $update_sql = 'update cSessionInfo set session_key= "'.$params['session_key'].'",create_time = "'.$params['create_time'].'" ,last_visit_time = "' . $params['last_visit_time']  .'" ,unionId = "' . $params['unionId'] .'",skey = "' . $params['skey'] .'" where uuid = "' . $params['uuid'].'"';
        $mysql_update = new mysql_db();
        return $mysql_update->query_db($update_sql);
    }

    public function update_csessioninfo_user_info($params)
    {
        $update_sql = 'update cSessionInfo set user_info = "' . $params['user_info'] . '" where uuid = "' . $params['uuid'].'"';
        $mysql_update = new mysql_db();
        return $mysql_update->query_db($update_sql);
    }
    //tb_user  `userID`, `userName`, `openId`, `nickName`, `city`, `province`, `country`, `avatarUrl`, `gender`, `appid`, `userCreateDate`, `unionId` ,`userUpdateDate`
    //先创建用户表  新用户注册 给用户一个临时名字 和默认头像
    public function insert_tb_user_user_info($params)
    {
        $nickName = '用户'.substr($params['openid'], -6);
        $insert_sql = 'insert into tb_user set openId = "'.$params['openid'].'",unionId="'.$params['unionId'].'",nickName="'.$nickName.'"';
        $mysql_insert = new mysql_db();
        return $mysql_insert->query_db($insert_sql);
    }
    //wx.login 获取用户的userInfo 
    public function get_user_info_from_tb_user($open_id)
    {
        $select_sql = 'select * from tb_user where openId = "' . $open_id . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $arr_result = array();
            while ($row = mysqli_fetch_array($result)) {
                $arr_result['openId'] = $row['openId'];
                $arr_result['nickName'] = $row['nickName'];
                $arr_result['city'] = $row['city'];
                $arr_result['province'] = $row['province'];
                $arr_result['country'] = $row['country'];
                $arr_result['avatarUrl'] = $row['avatarUrl'];
                $arr_result['gender'] = $row['gender'];
                $arr_result['userID'] = $row['userID'];
                $arr_result['unionId'] = $row['unionId'];
                $arr_result['appid'] = $row['appid'];
                $arr_result['userUpdateDate'] = $row['userUpdateDate'];
            }
            return $arr_result;
        } else {
            return false;
        }
    }
    public function select_csessioninfo($params)
    {
        $select_sql = 'select * from cSessionInfo where uuid = "' . $params['uuid'] . '" and skey = "' . $params['skey'] . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $arr_result = array();
            while ($row = mysqli_fetch_array($result)) {
                $arr_result['id'] = $row['id'];
                $arr_result['uuid'] = $row['uuid'];
                $arr_result['skey'] = $row['skey'];
                $arr_result['create_time'] = $row['create_time'];
                $arr_result['last_visit_time'] = $row['last_visit_time'];
                $arr_result['open_id'] = $row['open_id'];
                $arr_result['session_key'] = $row['session_key'];
                $arr_result['user_info'] = $row['user_info'];
            }
            return $arr_result;
        } else {
            return false;
        }
    }


    public function get_id_user_info_csessioninfo($open_id)
    {
        $select_sql = 'select uuid,user_info from cSessionInfo where open_id = "' . $open_id . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $data = array();
            while ($row = mysqli_fetch_array($result)) {
                $data['id'] = $row['uuid'];
                $data['userInfo'] = $row['user_info'];
            }
            return $data;
        } else {
            return false;
        }
    }

    public function check_session_for_auth($params){
        $result = $this->select_csessioninfo($params);
        if(!empty($result) && $result !== false && count($result) != 0){
            $now_time = time();
            $create_time = strtotime($result['create_time']);
            $last_visit_time = strtotime($result['last_visit_time']);
            if(($now_time-$create_time)/86400>$params['login_duration']) {
                return false;
            }else if(($now_time-$last_visit_time)>$params['session_duration']){
                return false;
            }else{
                $params['last_visit_time'] = date('Y-m-d H:i:s',$now_time);
                $this->update_csessioninfo_time($params);
                return $result['user_info'];
            }
        }else{
            return false;
        }
    }


    public function change_csessioninfo($params)
    {
        $userData = $this->get_id_user_info_csessioninfo($params['openid']);

        if ($userData) {
            $uuid = $userData['id'];
            $params['uuid'] = $uuid;
            if ($this->update_csessioninfo($params))
                return $uuid;
            else
                return false;
        } else {
            $chk = $this->get_user_info_from_tb_user($params['openid']);
            if(!$chk){
                $this->insert_tb_user_user_info($params);//同时插入用户表
            }
            return $this->insert_csessioninfo($params);
        }
    }
}