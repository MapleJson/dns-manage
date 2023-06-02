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

 Date: 02/06/2023 19:19:12
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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '管理员表';

INSERT INTO `dns_record`.`admin` (`id`, `username`, `nickname`, `password`, `salt`, `loginfailure`, `logintime`, `create_time`, `update_time`, `token`, `status`) VALUES (1, 'admin', 'Admin', '7fd7809fd939c49dccfe71cffc73fee2', 'd019bc', 0, 1685692402, 1492186163, 1685692402, '208ea539866e452bb56d5c7b94d7837b', 1);

-- ----------------------------
-- Table structure for deploy
-- ----------------------------
DROP TABLE IF EXISTS `deploy`;
CREATE TABLE `deploy`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `site_id` int UNSIGNED NOT NULL COMMENT '站点ID',
  `private_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '站点自定义域名,后端服务器必填，供host解析用,每台服务器不一样',
  `server_id` int UNSIGNED NOT NULL COMMENT '服务器id',
  `server_type` int UNSIGNED NOT NULL COMMENT '服务器类型 1后端 2节点',
  `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `server_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '服务器名称',
  `public_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外网ip',
  `private_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内网ip',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '服务器类型 1后端 2节点',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '服务器表';

-- ----------------------------
-- Table structure for shell
-- ----------------------------
DROP TABLE IF EXISTS `shell`;
CREATE TABLE `shell`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `site_id` int UNSIGNED NOT NULL COMMENT '站点ID',
  `shell` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '执行命令的内容',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '执行状态 1未执行 2已执行',
  `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- ----------------------------
-- Table structure for sites
-- ----------------------------
DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `site_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点名称',
  `flag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点唯一标识，只能英文开头,下划线,数字',
  `port` bigint UNSIGNED NOT NULL DEFAULT 81 COMMENT '站点端口',
  `a_domain_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT 'A记录域名id',
  `base_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点本地路径',
  `origin_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '站点远程路径',
  `backend_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '后台域名',
  `area` tinyint UNSIGNED NOT NULL COMMENT '地区 1尼日 2菲律宾 3印尼 4埃及 5越南 6泰国 7南非 8印度',
  `deployed` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否部署过，1 部署过 0未部署',
  `status` tinyint UNSIGNED NOT NULL COMMENT '状态 1开发中 2测试中 3已上线 4已下线',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `create_time` int UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `port`(`port` ASC) USING BTREE COMMENT '站点端口唯一'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '服务器表';

SET FOREIGN_KEY_CHECKS = 1;
