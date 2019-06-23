/*
Navicat MySQL Data Transfer

Source Server         : MySql
Source Server Version : 50711
Source Host           : localhost:3306
Source Database       : me

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2019-06-23 11:21:42
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
AUTO_INCREMENT=4

;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
BEGIN;
INSERT INTO `auth_assignment` VALUES ('1', '1', '1'), ('2', '2', '2'), ('3', '2', '3');
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
AUTO_INCREMENT=6

;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
BEGIN;
INSERT INTO `auth_item` VALUES ('1', 'admin', '1'), ('2', 'user', '1'), ('3', 'userManagment', '2'), ('4', 'profile', '2'), ('5', 'dashboard', '2');
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
AUTO_INCREMENT=5

;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
BEGIN;
INSERT INTO `auth_item_child` VALUES ('1', '1', '2'), ('2', '1', '3'), ('3', '2', '4'), ('4', '2', '5');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`fullname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`password`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL ,
`avatar`  varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_persian_ci
AUTO_INCREMENT=4

;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'حسین نجفی', 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'default.png'), ('2', 'superadmin3', 'superadmin1', '2a43bf7ab34cd6bf5401343115eaf325', 'default.png'), ('3', 'superadmin2', 'superadmin2', '2a43bf7ab34cd6bf5401343115eaf325', 'default.png');
COMMIT;

-- ----------------------------
-- Auto increment value for auth_assignment
-- ----------------------------
ALTER TABLE `auth_assignment` AUTO_INCREMENT=4;

-- ----------------------------
-- Auto increment value for auth_item
-- ----------------------------
ALTER TABLE `auth_item` AUTO_INCREMENT=6;

-- ----------------------------
-- Auto increment value for auth_item_child
-- ----------------------------
ALTER TABLE `auth_item_child` AUTO_INCREMENT=5;

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
ALTER TABLE `users` AUTO_INCREMENT=4;
