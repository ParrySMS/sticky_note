/*
Navicat MySQL Data Transfer

Source Server         : lizhiNew
Source Server Version : 50628
Source Host           : 55c5f3742f54d.gz.cdb.myqcloud.com:13125
Source Database       : yihui

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2018-09-14 10:57:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_note
-- ----------------------------
DROP TABLE IF EXISTS `st_note`;
CREATE TABLE `st_note` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `text` varchar(1500) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0未完成 1已完成',
  `is_top` int(11) DEFAULT NULL COMMENT '0未置顶 1置顶',
  `commit_time` datetime DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `edit_time` datetime DEFAULT NULL,
  `total_edit` int(11) DEFAULT NULL,
  `visible` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4;
