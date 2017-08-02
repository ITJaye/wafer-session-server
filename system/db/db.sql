create database cAuth;
use cAuth;

DROP TABLE IF EXISTS `cAppinfo`;
CREATE TABLE `cAppinfo` (
  `appid` varchar(200) COLLATE utf8_unicode_ci NOT NULL ,
  `secret` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `login_duration` int(11) DEFAULT '30',
  `session_duration` int(11) DEFAULT '2592000',
  `qcloud_appid` varchar(300) COLLATE utf8_unicode_ci DEFAULT 'appid_qcloud',
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0.0.0.0',
  PRIMARY KEY (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS `cSessioninfo`;
CREATE TABLE `cSessionInfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skey` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_time` datetime NOT NULL,
  `last_visit_time` datetime NOT NULL,
  `open_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_info` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unionId` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth` (`uuid`,`skey`),
  KEY `weixin` (`open_id`,`session_key`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会话管理用户信息';
