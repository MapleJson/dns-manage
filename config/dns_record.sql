/*
 Navicat Premium Data Transfer

 Source Server         : 内网服务器
 Source Server Type    : MySQL
 Source Server Version : 80033 (8.0.33-0ubuntu0.22.04.2)
 Source Host           : 192.168.8.4:3306
 Source Schema         : dns_record

 Target Server Type    : MySQL
 Target Server Version : 80033 (8.0.33-0ubuntu0.22.04.2)
 File Encoding         : 65001

 Date: 28/05/2023 18:32:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
    `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
    `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
    `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码盐',
    `loginfailure` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
    `logintime` int UNSIGNED NULL DEFAULT NULL COMMENT '登录时间',
    `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    `token` varchar(59) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
    `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `username`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '管理员表';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (1, 'admin', 'Admin', '7fd7809fd939c49dccfe71cffc73fee2', 'd019bc', 0, 1685267646, 1492186163, 1685267646, '97f2fe6cdb814d05a1b7e769cb0e1ace', 1);

-- ----------------------------
-- Table structure for domains
-- ----------------------------
DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '域名',
    `site_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '站点id',
    `zone_identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '域名的域ID',
    `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '域名表';

-- ----------------------------
-- Records of domains
-- ----------------------------

-- ----------------------------
-- Table structure for records
-- ----------------------------
DROP TABLE IF EXISTS `records`;
CREATE TABLE `records`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `domain_id` int UNSIGNED NOT NULL COMMENT '域名id',
    `site_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '站点id',
    `type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '解析类型 A CNAME',
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录名称',
    `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录内容 A记录是ip CNAME记录是域名',
    `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '记录ID',
    `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '域名对应解析记录表';

-- ----------------------------
-- Records of records
-- ----------------------------

-- ----------------------------
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `server_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '服务器名称',
    `public_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外网ip',
    `private_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内网ip',
    `type` tinyint UNSIGNED  NOT NULL DEFAULT 1 COMMENT '服务器类型 1后端 2节点',
    `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '服务器表';

-- ----------------------------
-- Records of servers
-- ----------------------------

-- ----------------------------
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `site_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点名称',
    `private_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点自定义域名,供host解析用',
    `server_id` int UNSIGNED NOT NULL COMMENT '后端服务器id',
    `front_server_id` int UNSIGNED NOT NULL COMMENT '节点服务器id',
    `a_domain_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT 'A记录域名id',
    `back_a_record_id` int UNSIGNED NOT NULL DEFAULT 0  COMMENT '后台A记录DNS id',
    `front_a_record_id` int unsigned NOT NULL DEFAULT '0' COMMENT '前台A记录DNS id',
    `base_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点本地路径',
    `origin_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点远程路径',
    `backend_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '后台域名',
    `web_domains` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '站点域名列表,空格分割的字符串',
    `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
    `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '服务器表';

-- ----------------------------
-- Records of servers
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
