/*
Navicat MySQL Data Transfer

Source Server         : lizhiNew
Source Server Version : 50628
Source Host           : 55c5f3742f54d.gz.cdb.myqcloud.com:13125
Source Database       : yihui

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2018-09-14 10:57:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_user
-- ----------------------------
DROP TABLE IF EXISTS `st_user`;
CREATE TABLE `st_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(500) DEFAULT NULL,
  `nickname` varchar(500) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `province` varchar(500) DEFAULT NULL,
  `city` varchar(500) DEFAULT NULL,
  `country` varchar(500) DEFAULT NULL,
  `headimgurl` varchar(500) DEFAULT NULL,
  `privilege` varchar(500) DEFAULT NULL,
  `unionid` varchar(500) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `visible` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
