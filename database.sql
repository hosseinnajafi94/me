/*
Navicat MySQL Data Transfer

Source Server         : MySql
Source Server Version : 50711
Source Host           : localhost:3306
Source Database       : me

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2019-06-18 21:21:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`item_id`  int(11) NOT NULL ,
`user_id`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`item_id`) REFERENCES `auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
INDEX `item_id` (`item_id`) USING BTREE ,
INDEX `user_id` (`user_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_persian_ci
AUTO_INCREMENT=2

;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
BEGIN;
INSERT INTO `auth_assignment` VALUES ('1', '1', '1');
COMMIT;

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`type`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_persian_ci
AUTO_INCREMENT=4

;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
BEGIN;
INSERT INTO `auth_item` VALUES ('1', 'admin', '1'), ('2', 'user', '1'), ('3', 'userManagment', '2');
COMMIT;

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`parent_id`  int(11) NOT NULL ,
`child_id`  int(11) NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`parent_id`) REFERENCES `auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`child_id`) REFERENCES `auth_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
INDEX `parent_id` (`parent_id`) USING BTREE ,
INDEX `child_id` (`child_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_persian_ci
AUTO_INCREMENT=3

;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
BEGIN;
INSERT INTO `auth_item_child` VALUES ('1', '1', '2'), ('2', '2', '3');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`password`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`fullname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`avatar`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_persian_ci
AUTO_INCREMENT=37

;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'حسین نجفی', 'default.png'), ('20', 'a', 'b', 'c', 'default.png'), ('21', '111', '222', '333', 'default.png'), ('22', '111', '222', '333', 'default.png'), ('23', '111', '222', '333', 'default.png'), ('24', '111', '222', '333', 'default.png'), ('25', '111', '222', '333', 'default.png'), ('27', 'asd', '1', '2', 'default.png'), ('28', 'asd', '1', '2', 'default.png'), ('29', 'asd1', 'a67995ad3ec084cb38d32725fd73d9a3', 'asd3', 'default.png'), ('30', 'asd1', 'asd2', '22', 'default.png'), ('31', '1', '1', '1', 'default.png'), ('32', '3', '3', '3', 'default.png'), ('33', 'sda', 'asd', 'asd', 'default.png'), ('34', '', '', '', 'default.png'), ('35', '111111', '222222', '333333', 'default.png'), ('36', '111111', '222222', '333333', 'default.png');
COMMIT;

-- ----------------------------
-- Auto increment value for auth_assignment
-- ----------------------------
ALTER TABLE `auth_assignment` AUTO_INCREMENT=2;

-- ----------------------------
-- Auto increment value for auth_item
-- ----------------------------
ALTER TABLE `auth_item` AUTO_INCREMENT=4;

-- ----------------------------
-- Auto increment value for auth_item_child
-- ----------------------------
ALTER TABLE `auth_item_child` AUTO_INCREMENT=3;

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
ALTER TABLE `users` AUTO_INCREMENT=37;
