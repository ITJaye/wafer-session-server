Wafer 会话服务 支持微信小程序登陆及授权获取用户信息的新要求
===============

## 项目修改

1.增加了解密用户数据的函数
2.移除了旧版的解密文件 三个功能 用户登录 用户登录态验证 以及用户信息解密
3.修改了多个sql语句 调整了相关的逻辑 增加了用户数据表相关操作
4.mysqli支持 数据表增加union字段

    
## 新增数据表tb_user
//`userID`, `userName`, `openId`, `nickName`, `city`, `province`, `country`, `avatarUrl`, `gender`, `appid`, `userCreateDate`, `unionId`
userID int 自增  openId 唯一 avatarUrl 给个默认头像链接  其他都为默认NULL


## 具体结合其他两个项目来一起实现

微信小程序新的登陆方式修改为：
1.用户每次进入小程序，
通过wx.checkSession检查会话状态，
调用wx.login获取code发送到服务端，
服务端通过code换取用户的openId、unionId和session_key，
此时注册用户信息，给定默认的头像和昵称，
达到用户注册的目的，并给客户端返回用户的userInfo

2.小程序端需要用户头像和昵称的时候，
小程序端放置【获取用户头像昵称按钮】，
用户点击时通过wx.getUserInfo带上withCredentials:true 
获取 encryptedData 和 iv 上传到服务端进行信息解密，
解密后得到用户的userInfo，此时可以更新数据库中用户的信息。

原项目参考 [腾讯云会话服务](https://github.com/tencentyun/wafer-session-server)
新登陆授权要求 [小程序授权登陆新要求](https://developers.weixin.qq.com/blogdetail?action=get_post_info&lang=zh_CN&token=&docid=c45683ebfa39ce8fe71def0631fad26b)
原始项目 [Wafer](https://github.com/tencentyun/wafer)
