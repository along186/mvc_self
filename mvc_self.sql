/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : mvc_self

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-07-02 16:30:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for self_user_info
-- ----------------------------
DROP TABLE IF EXISTS `self_user_info`;
CREATE TABLE `self_user_info` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_name` varchar(50) NOT NULL,
  `u_email` varchar(50) NOT NULL,
  `u_sex` tinyint(1) NOT NULL,
  `u_age` smallint(4) NOT NULL,
  `u_pwd` char(32) NOT NULL,
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of self_user_info
-- ----------------------------
INSERT INTO `self_user_info` VALUES ('1', 'liufeilong', '541159078@qq.com', '1', '28', '5d29d7f097d71d45f4d357b05e6c8434');
INSERT INTO `self_user_info` VALUES ('2', 'zhangjingjing', '1360359156@qq.com', '2', '27', '5d29d7f097d71d45f4d357b05e6c8434');
INSERT INTO `self_user_info` VALUES ('3', 'liusihan', 'liusihan@qq.com', '2', '0', '5d29d7f097d71d45f4d357b05e6c8434');
