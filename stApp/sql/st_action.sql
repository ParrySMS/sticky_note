/*
Navicat MySQL Data Transfer

Date: 2018-09-14 10:56:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_action
-- ----------------------------
DROP TABLE IF EXISTS `st_action`;
CREATE TABLE `st_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `agent` varchar(500) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `error_code` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `visible` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3782 DEFAULT CHARSET=utf8mb4;
