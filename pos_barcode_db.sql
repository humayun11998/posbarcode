/*
Navicat MySQL Data Transfer

Source Server         : home
Source Server Version : 80030
Source Host           : localhost:3306
Source Database       : pos_barcode_db

Target Server Type    : MYSQL
Target Server Version : 80030
File Encoding         : 65001

Date: 2024-08-26 08:06:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_category`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_category`;
CREATE TABLE `tbl_category` (
  `catid` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_category
-- ----------------------------
INSERT INTO `tbl_category` VALUES ('1', 'Fashion');
INSERT INTO `tbl_category` VALUES ('2', 'Health Care');
INSERT INTO `tbl_category` VALUES ('3', 'Electronics');
INSERT INTO `tbl_category` VALUES ('4', 'Baby &amp; Kids');
INSERT INTO `tbl_category` VALUES ('5', 'Cosmetics');
INSERT INTO `tbl_category` VALUES ('6', 'Watches');
INSERT INTO `tbl_category` VALUES ('7', 'Soap');
INSERT INTO `tbl_category` VALUES ('8', 'Mobile');

-- ----------------------------
-- Table structure for `tbl_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_invoice`;
CREATE TABLE `tbl_invoice` (
  `invoice_id` int NOT NULL AUTO_INCREMENT,
  `order_date` datetime DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `sgst` float DEFAULT NULL,
  `cgst` float DEFAULT NULL,
  `total` double DEFAULT NULL,
  `payment_type` tinytext,
  `due` double DEFAULT NULL,
  `paid` double DEFAULT NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_invoice
-- ----------------------------
INSERT INTO `tbl_invoice` VALUES ('5', '2024-08-26 00:00:00', '13200', '2', '2.9', '2.9', '13701.6', 'Cash', '0', '13701.6');
INSERT INTO `tbl_invoice` VALUES ('6', '2024-08-26 00:00:00', '400', '2', '2.9', '2.9', '415.2', 'Cash', '0', '415.2');

-- ----------------------------
-- Table structure for `tbl_invoice_details`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_invoice_details`;
CREATE TABLE `tbl_invoice_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `barcode` varchar(200) DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `saleprice` double DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_invoice_details
-- ----------------------------
INSERT INTO `tbl_invoice_details` VALUES ('10', '5', '7122418', '7', '7', '11', '200', '2200', '2024-08-26 00:00:00');
INSERT INTO `tbl_invoice_details` VALUES ('11', '5', '8122613', '8', '8', '11', '1000', '11000', '2024-08-26 00:00:00');
INSERT INTO `tbl_invoice_details` VALUES ('12', '6', '7122418', '7', '7', '2', '200', '400', '2024-08-26 00:00:00');

-- ----------------------------
-- Table structure for `tbl_product`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_product`;
CREATE TABLE `tbl_product` (
  `pid` int NOT NULL AUTO_INCREMENT,
  `barcode` varchar(1000) DEFAULT NULL,
  `product` varchar(200) DEFAULT NULL,
  `category` varchar(200) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `purchaseprice` float DEFAULT NULL,
  `saleprice` float DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_product
-- ----------------------------
INSERT INTO `tbl_product` VALUES ('7', '7122418', 'Dettol', 'Soap', 'Dettol', '987', '100', '200', '66cb84e2b4df6.png');
INSERT INTO `tbl_product` VALUES ('8', '8122613', 'Watch', 'Watches', 'Watch', '989', '500', '1000', '66cb855560996.jpg');

-- ----------------------------
-- Table structure for `tbl_taxdis`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_taxdis`;
CREATE TABLE `tbl_taxdis` (
  `taxdis_id` int NOT NULL AUTO_INCREMENT,
  `sgst` float DEFAULT NULL,
  `cgst` float DEFAULT NULL,
  `discount` float DEFAULT NULL,
  PRIMARY KEY (`taxdis_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_taxdis
-- ----------------------------
INSERT INTO `tbl_taxdis` VALUES ('1', '2.9', '2.9', '2');
INSERT INTO `tbl_taxdis` VALUES ('2', '5.1', '5.1', '10');

-- ----------------------------
-- Table structure for `tbl_user`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
  `userid` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `useremail` varchar(200) DEFAULT NULL,
  `userpassword` varchar(200) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of tbl_user
-- ----------------------------
INSERT INTO `tbl_user` VALUES ('1', 'Admin', 'admin@gmail.com', '12345', 'Admin');
INSERT INTO `tbl_user` VALUES ('2', 'user', 'user@gmail.com', '123', 'User');
INSERT INTO `tbl_user` VALUES ('9', 'Humayun Ahmed', 'humayun@gmail.com', '123456', 'User');
INSERT INTO `tbl_user` VALUES ('10', 'aass', 'admin11221@gmail.com', '121', 'Admin');
