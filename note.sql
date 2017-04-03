/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : note

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-04-01 13:11:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for actress
-- ----------------------------
DROP TABLE IF EXISTS `actress`;
CREATE TABLE `actress` (
  `act_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `act_rank` float NOT NULL,
  PRIMARY KEY (`act_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for av_movie
-- ----------------------------
DROP TABLE IF EXISTS `av_movie`;
CREATE TABLE `av_movie` (
  `av_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `av_name` char(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `av_rank` float unsigned zerofill NOT NULL,
  `av_acc_id` int(11) unsigned DEFAULT NULL,
  `av_pressDate` date NOT NULL,
  `av_finishDate` date DEFAULT NULL,
  PRIMARY KEY (`av_id`),
  KEY `act_id` (`av_acc_id`),
  CONSTRAINT `act_id` FOREIGN KEY (`av_acc_id`) REFERENCES `actress` (`act_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for av_series
-- ----------------------------
DROP TABLE IF EXISTS `av_series`;
CREATE TABLE `av_series` (
  `av_tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `av_topic` char(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `av_degree` float unsigned zerofill NOT NULL,
  `av_lastDate` date NOT NULL,
  PRIMARY KEY (`av_tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for movie
-- ----------------------------
DROP TABLE IF EXISTS `movie`;
CREATE TABLE `movie` (
  `m_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `m_chn_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `m_eng_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_rank` float NOT NULL DEFAULT '1',
  `m_comment` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_pressDate` date NOT NULL,
  `m_finishDate` date DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET FOREIGN_KEY_CHECKS=1;
