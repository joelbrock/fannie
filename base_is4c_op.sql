-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 15, 2011 at 07:20 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.6-2ubuntu4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `base_is4c_op`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE IF NOT EXISTS `batches` (
  `batchID` int(5) NOT NULL auto_increment,
  `startDate` date NOT NULL,
  `endDate` date default NULL,
  `batchName` varchar(80) default NULL,
  `batchType` int(3) default NULL,
  `discountType` int(2) default NULL,
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`batchID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `batches`
--


-- --------------------------------------------------------

--
-- Table structure for table `batchList`
--

CREATE TABLE IF NOT EXISTS `batchList` (
  `listID` int(6) NOT NULL auto_increment,
  `upc` varchar(13) default NULL,
  `batchID` int(5) default NULL,
  `salePrice` decimal(10,2) default NULL,
  `active` tinyint(1) default '0',
  PRIMARY KEY  (`listID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `batchList`
--


-- --------------------------------------------------------

--
-- Table structure for table `chargecode`
--

CREATE TABLE IF NOT EXISTS `chargecode` (
  `staffID` varchar(4) default NULL,
  `chargecode` varchar(6) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chargecode`
--


-- --------------------------------------------------------

--
-- Table structure for table `couponcodes`
--

CREATE TABLE IF NOT EXISTS `couponcodes` (
  `Code` varchar(4) default NULL,
  `Qty` int(11) default NULL,
  `Value` double default NULL,
  KEY `Code` (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `couponcodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `custdata`
--

CREATE TABLE IF NOT EXISTS `custdata` (
  `CardNo` int(8) NOT NULL,
  `personNum` tinyint(4) NOT NULL default '1',
  `LastName` varchar(30) default NULL,
  `FirstName` varchar(30) default NULL,
  `CashBack` double NOT NULL default '60',
  `Balance` double NOT NULL default '0',
  `Discount` smallint(6) default NULL,
  `MemDiscountLimit` double NOT NULL default '0',
  `ChargeOk` tinyint(4) NOT NULL default '1',
  `WriteChecks` tinyint(4) NOT NULL default '1',
  `StoreCoupons` tinyint(4) NOT NULL default '1',
  `Type` varchar(10) NOT NULL default 'pc',
  `memType` tinyint(4) default NULL,
  `staff` tinyint(4) NOT NULL default '0',
  `SSI` tinyint(4) NOT NULL default '0',
  `Purchases` double NOT NULL default '0',
  `NumberOfChecks` smallint(6) NOT NULL default '0',
  `memCoupons` int(11) NOT NULL default '1',
  `blueLine` varchar(50) default NULL,
  `Shown` tinyint(4) NOT NULL default '1',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `CardNo` (`CardNo`),
  KEY `staff` (`staff`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `custdata`
--


-- --------------------------------------------------------

--
-- Table structure for table `cust_pr_2010`
--

CREATE TABLE IF NOT EXISTS `cust_pr_2010` (
  `card_no` smallint(5) NOT NULL default '0',
  `points` double default NULL,
  `alloc` double default NULL,
  `paid` double default NULL,
  `ret` double default NULL,
  PRIMARY KEY  (`card_no`),
  KEY `paid` (`paid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cust_pr_2010`
--


-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `dept_no` smallint(6) default NULL,
  `dept_name` varchar(30) default NULL,
  `dept_tax` tinyint(4) default NULL,
  `dept_fs` tinyint(4) default NULL,
  `dept_limit` double default NULL,
  `dept_minimum` double default NULL,
  `dept_discount` tinyint(4) default NULL,
  `modified` datetime default NULL,
  `modifiedby` int(11) default NULL,
  KEY `dept_no` (`dept_no`),
  KEY `dept_name` (`dept_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departments`
--


-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `emp_no` smallint(6) default NULL,
  `CashierPassword` int(11) default NULL,
  `AdminPassword` int(11) default NULL,
  `FirstName` varchar(255) default NULL,
  `LastName` varchar(255) default NULL,
  `JobTitle` varchar(255) default NULL,
  `EmpActive` tinyint(4) default NULL,
  `frontendsecurity` smallint(6) default NULL,
  `backendsecurity` smallint(6) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employees`
--


-- --------------------------------------------------------

--
-- Table structure for table `globalvalues`
--

CREATE TABLE IF NOT EXISTS `globalvalues` (
  `CashierNo` int(11) default NULL,
  `Cashier` varchar(30) default NULL,
  `LoggedIn` tinyint(4) default NULL,
  `TransNo` int(11) default NULL,
  `TTLFlag` tinyint(4) default NULL,
  `FntlFlag` tinyint(4) default NULL,
  `TaxExempt` tinyint(4) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `globalvalues`
--


-- --------------------------------------------------------

--
-- Table structure for table `inx`
--

CREATE TABLE IF NOT EXISTS `inx` (
  `userID` int(4) default NULL,
  `command` int(2) default NULL,
  `proce` varchar(50) default NULL,
  `TID` int(6) default NULL,
  `CC` varchar(50) default NULL,
  `expdate` int(4) default NULL,
  `manaul` int(2) default NULL,
  `tracktwo` varchar(150) default NULL,
  `transno` int(8) default NULL,
  `present` int(2) default NULL,
  `amount` decimal(10,2) default NULL,
  `name` varchar(80) default NULL,
  `transdate` varchar(50) default NULL,
  `trans_id` int(3) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inx`
--


-- --------------------------------------------------------

--
-- Table structure for table `item_properties`
--

CREATE TABLE IF NOT EXISTS `item_properties` (
  `bit` bigint(20) NOT NULL,
  `name` varchar(256) default NULL,
  `notes` varchar(512) NOT NULL,
  PRIMARY KEY  (`bit`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_properties`
--


-- --------------------------------------------------------

--
-- Table structure for table `legacy_products`
--

CREATE TABLE IF NOT EXISTS `legacy_products` (
  `upc` bigint(13) unsigned zerofill default NULL,
  `description` varchar(30) default NULL,
  `normal_price` double default NULL,
  `pricemethod` smallint(6) default NULL,
  `groupprice` double default NULL,
  `quantity` smallint(6) default NULL,
  `special_price` double default NULL,
  `specialpricemethod` smallint(6) default NULL,
  `specialgroupprice` double default NULL,
  `specialquantity` smallint(6) default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `department` smallint(6) default NULL,
  `size` varchar(9) default NULL,
  `tax` smallint(6) default NULL,
  `foodstamp` tinyint(4) default NULL,
  `scale` tinyint(4) default NULL,
  `mixmatchcode` varchar(13) default NULL,
  `modified` datetime default NULL,
  `advertised` tinyint(4) default NULL,
  `tareweight` double default NULL,
  `discount` smallint(6) default NULL,
  `discounttype` tinyint(4) default NULL,
  `unitofmeasure` varchar(15) default NULL,
  `wicable` smallint(6) default NULL,
  `deposit` double default '0',
  `qttyEnforced` tinyint(4) default NULL,
  `inUse` tinyint(4) default NULL,
  `subdept` smallint(4) default NULL,
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `legacy_products`
--


-- --------------------------------------------------------

--
-- Table structure for table `likecodes`
--

CREATE TABLE IF NOT EXISTS `likecodes` (
  `likeCode` int(4) default NULL,
  `likeCodeDesc` varchar(50) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `likecodes`
--


-- --------------------------------------------------------

--
-- Stand-in structure for view `members`
--
CREATE TABLE IF NOT EXISTS `members` (
`id` int(11)
,`card_no` int(8)
,`first_name` varchar(30)
,`last_name` varchar(30)
,`cash_back` double
,`balance` double
,`discount` smallint(6)
,`mem_discount_limit` double
,`charge_ok` tinyint(4)
,`write_checks` tinyint(4)
,`store_coupons` tinyint(4)
,`mem_type` varchar(10)
,`mem_status` tinyint(4)
,`staff` tinyint(4)
,`purchases` double
,`number_of_checks` smallint(6)
,`blue_line` varchar(50)
,`shown` tinyint(4)
);
-- --------------------------------------------------------

--
-- Table structure for table `meminfo`
--

CREATE TABLE IF NOT EXISTS `meminfo` (
  `card_no` smallint(5) default NULL,
  `last_name` varchar(30) default NULL,
  `first_name` varchar(30) default NULL,
  `othlast_name` varchar(30) default NULL,
  `othfirst_name` varchar(30) default NULL,
  `street` varchar(30) default NULL,
  `city` varchar(20) default NULL,
  `state` varchar(2) default NULL,
  `zip` varchar(10) default NULL,
  `phone` varchar(30) default NULL,
  `email_1` varchar(30) default NULL,
  `email_2` varchar(30) default NULL,
  `ads_OK` tinyint(1) default '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meminfo`
--


-- --------------------------------------------------------

--
-- Table structure for table `memtype`
--

CREATE TABLE IF NOT EXISTS `memtype` (
  `memtype` tinyint(2) default NULL,
  `memDesc` varchar(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `memtype`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` varchar(20) default NULL,
  `message` varchar(60) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `newMembers`
--

CREATE TABLE IF NOT EXISTS `newMembers` (
  `CardNo` varchar(25) default NULL,
  `personNum` tinyint(4) NOT NULL default '1',
  `LastName` varchar(30) default NULL,
  `FirstName` varchar(30) default NULL,
  `CashBack` double NOT NULL default '60',
  `Balance` double NOT NULL default '0',
  `Discount` smallint(6) default NULL,
  `MemDiscountLimit` double NOT NULL default '0',
  `ChargeOk` tinyint(4) NOT NULL default '1',
  `WriteChecks` tinyint(4) NOT NULL default '1',
  `StoreCoupons` tinyint(4) NOT NULL default '1',
  `Type` varchar(10) NOT NULL default 'pc',
  `memType` tinyint(4) default NULL,
  `staff` tinyint(4) NOT NULL default '0',
  `SSI` tinyint(4) NOT NULL default '0',
  `Purchases` double NOT NULL default '0',
  `NumberOfChecks` smallint(6) NOT NULL default '0',
  `memCoupons` int(11) NOT NULL default '1',
  `blueLine` varchar(50) default NULL,
  `Shown` tinyint(4) NOT NULL default '1',
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `newMembers`
--


-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `upc` bigint(13) unsigned zerofill default NULL,
  `description` varchar(30) default NULL,
  `normal_price` double default NULL,
  `pricemethod` smallint(6) default NULL,
  `groupprice` double default NULL,
  `quantity` smallint(6) default NULL,
  `special_price` double default NULL,
  `specialpricemethod` smallint(6) default NULL,
  `specialgroupprice` double default NULL,
  `specialquantity` smallint(6) default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `department` smallint(6) default NULL,
  `size` varchar(9) default NULL,
  `tax` smallint(6) default NULL,
  `foodstamp` tinyint(4) default NULL,
  `scale` tinyint(4) default NULL,
  `mixmatchcode` varchar(13) default NULL,
  `modified` datetime default NULL,
  `advertised` tinyint(4) default NULL,
  `tareweight` double default NULL,
  `discount` smallint(6) default NULL,
  `discounttype` tinyint(4) default NULL,
  `unitofmeasure` varchar(15) default NULL,
  `wicable` smallint(6) default NULL,
  `deposit` double default '0',
  `qttyEnforced` tinyint(4) default NULL,
  `inUse` tinyint(4) default NULL,
  `subdept` smallint(4) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `upc` (`upc`),
  KEY `description` (`description`),
  KEY `normal_price` (`normal_price`),
  KEY `subdept` (`subdept`),
  KEY `department` (`department`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `products`
--


-- --------------------------------------------------------

--
-- Table structure for table `products_dev`
--

CREATE TABLE IF NOT EXISTS `products_dev` (
  `upc` bigint(13) unsigned zerofill default NULL,
  `description` varchar(30) default NULL,
  `normal_price` double default NULL,
  `pricemethod` smallint(6) default NULL,
  `groupprice` double default NULL,
  `quantity` smallint(6) default NULL,
  `special_price` double default NULL,
  `specialpricemethod` smallint(6) default NULL,
  `specialgroupprice` double default NULL,
  `specialquantity` smallint(6) default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `department` smallint(6) default NULL,
  `size` varchar(9) default NULL,
  `tax` smallint(6) default NULL,
  `foodstamp` tinyint(4) default NULL,
  `scale` tinyint(4) default NULL,
  `mixmatchcode` varchar(13) default NULL,
  `modified` datetime default NULL,
  `advertised` tinyint(4) default NULL,
  `tareweight` double default NULL,
  `discount` smallint(6) default NULL,
  `discounttype` tinyint(4) default NULL,
  `unitofmeasure` varchar(15) default NULL,
  `wicable` smallint(6) default NULL,
  `deposit` double default '0',
  `qttyEnforced` tinyint(4) default NULL,
  `inUse` tinyint(4) default NULL,
  `subdept` smallint(4) default NULL,
  `props` bigint(20) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `upc` (`upc`),
  KEY `description` (`description`),
  KEY `normal_price` (`normal_price`),
  KEY `subdept` (`subdept`),
  KEY `department` (`department`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `products_dev`
--


-- --------------------------------------------------------

--
-- Table structure for table `products_tmp`
--

CREATE TABLE IF NOT EXISTS `products_tmp` (
  `upc` bigint(13) unsigned zerofill default NULL,
  `description` varchar(30) default NULL,
  `normal_price` double default NULL,
  `pricemethod` smallint(6) default NULL,
  `groupprice` double default NULL,
  `quantity` smallint(6) default NULL,
  `special_price` double default NULL,
  `specialpricemethod` smallint(6) default NULL,
  `specialgroupprice` double default NULL,
  `specialquantity` smallint(6) default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `department` smallint(6) default NULL,
  `size` varchar(9) default NULL,
  `tax` smallint(6) default NULL,
  `foodstamp` tinyint(4) default NULL,
  `scale` tinyint(4) default NULL,
  `mixmatchcode` varchar(13) default NULL,
  `modified` datetime default NULL,
  `advertised` tinyint(4) default NULL,
  `tareweight` double default NULL,
  `discount` smallint(6) default NULL,
  `discounttype` tinyint(4) default NULL,
  `unitofmeasure` varchar(15) default NULL,
  `wicable` smallint(6) default NULL,
  `deposit` double default '0',
  `qttyEnforced` tinyint(4) default NULL,
  `inUse` tinyint(4) default NULL,
  `subdept` smallint(4) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `upc` (`upc`),
  KEY `description` (`description`),
  KEY `normal_price` (`normal_price`),
  KEY `subdept` (`subdept`),
  KEY `department` (`department`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `products_tmp`
--


-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE IF NOT EXISTS `product_details` (
  `brand` varchar(30) default NULL,
  `order_no` int(6) default NULL,
  `pack_size` varchar(25) default NULL,
  `upc` bigint(13) unsigned zerofill NOT NULL default '0000000000000',
  `units` int(3) default NULL,
  `cost` decimal(9,2) default NULL,
  `description` varchar(35) default NULL,
  `depart` varchar(15) default NULL,
  `distributor` varchar(30) NOT NULL,
  `product` varchar(255) default NULL,
  KEY `upc` (`upc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `prodUpdate`
--

CREATE TABLE IF NOT EXISTS `prodUpdate` (
  `upc` varchar(13) default NULL,
  `description` varchar(50) default NULL,
  `price` decimal(10,2) default NULL,
  `dept` int(6) default NULL,
  `tax` bit(1) default NULL,
  `fs` bit(1) default NULL,
  `scale` bit(1) default NULL,
  `likeCode` int(6) default NULL,
  `modified` date default NULL,
  `user` int(8) default NULL,
  `forceQty` bit(1) default NULL,
  `noDisc` bit(1) default NULL,
  `inUse` bit(1) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prodUpdate`
--


-- --------------------------------------------------------

--
-- Table structure for table `prod_subdepts`
--

CREATE TABLE IF NOT EXISTS `prod_subdepts` (
  `upc` bigint(13) unsigned zerofill default NULL,
  `description` varchar(30) default NULL,
  `department` tinyint(2) default NULL,
  `subdept` smallint(4) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prod_subdepts`
--


-- --------------------------------------------------------

--
-- Table structure for table `promomsgs`
--

CREATE TABLE IF NOT EXISTS `promomsgs` (
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `promoMsg` varchar(50) default NULL,
  `sequence` tinyint(4) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `promomsgs`
--


-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `staff_no` tinyint(2) default NULL,
  `staff_desc` varchar(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--


-- --------------------------------------------------------

--
-- Stand-in structure for view `subdeptindex`
--
CREATE TABLE IF NOT EXISTS `subdeptindex` (
`upc` bigint(13) unsigned zerofill
,`department` smallint(6)
,`dept_name` varchar(30)
,`subdept` smallint(4)
,`subdept_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Table structure for table `subdepts`
--

CREATE TABLE IF NOT EXISTS `subdepts` (
  `subdept_no` smallint(4) NOT NULL,
  `subdept_name` varchar(30) default NULL,
  `dept_ID` tinyint(2) default NULL,
  KEY `subdept_no` (`subdept_no`),
  KEY `subdept_name` (`subdept_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subdepts`
--


-- --------------------------------------------------------

--
-- Table structure for table `subdeptTotals`
--

CREATE TABLE IF NOT EXISTS `subdeptTotals` (
  `date` date NOT NULL,
  `dept_no` tinyint(2) NOT NULL,
  `dept_name` varchar(30) NOT NULL,
  `subdept_no` smallint(4) NOT NULL,
  `subdept_name` varchar(30) NOT NULL,
  `item_count` int(10) NOT NULL,
  `item_total` int(10) NOT NULL,
  `id` int(10) NOT NULL auto_increment,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `subdeptTotals`
--


-- --------------------------------------------------------

--
-- Table structure for table `tenders`
--

CREATE TABLE IF NOT EXISTS `tenders` (
  `TenderID` smallint(6) default NULL,
  `TenderCode` varchar(255) default NULL,
  `TenderName` varchar(255) default NULL,
  `TenderType` varchar(255) default NULL,
  `ChangeMessage` varchar(255) default NULL,
  `MinAmount` double default NULL,
  `MaxAmount` double default NULL,
  `MaxRefund` double default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tenders`
--


-- --------------------------------------------------------

--
-- Table structure for table `UNFI`
--

CREATE TABLE IF NOT EXISTS `UNFI` (
  `brand` varchar(30) default NULL,
  `sku` int(6) default NULL,
  `size` varchar(25) default NULL,
  `upc` bigint(13) unsigned zerofill NOT NULL default '0000000000000',
  `units` int(3) default NULL,
  `cost` decimal(9,2) default NULL,
  `description` varchar(35) default NULL,
  `depart` varchar(15) default NULL,
  KEY `newindex` (`upc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `UNFI`
--


-- --------------------------------------------------------

--
-- Table structure for table `upclike`
--

CREATE TABLE IF NOT EXISTS `upclike` (
  `upc` varchar(13) default NULL,
  `likeCode` int(4) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `upclike`
--


-- --------------------------------------------------------

--
-- Stand-in structure for view `volunteerDiscounts`
--
CREATE TABLE IF NOT EXISTS `volunteerDiscounts` (
`CardNo` int(8)
,`hours` tinyint(4)
,`total` int(6)
,`id` int(11)
);
-- --------------------------------------------------------

--
-- Structure for view `members`
--
DROP TABLE IF EXISTS `members`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `base_is4c_op`.`members` AS select `base_is4c_op`.`custdata`.`id` AS `id`,`base_is4c_op`.`custdata`.`CardNo` AS `card_no`,`base_is4c_op`.`custdata`.`FirstName` AS `first_name`,`base_is4c_op`.`custdata`.`LastName` AS `last_name`,`base_is4c_op`.`custdata`.`CashBack` AS `cash_back`,`base_is4c_op`.`custdata`.`Balance` AS `balance`,`base_is4c_op`.`custdata`.`Discount` AS `discount`,`base_is4c_op`.`custdata`.`MemDiscountLimit` AS `mem_discount_limit`,`base_is4c_op`.`custdata`.`ChargeOk` AS `charge_ok`,`base_is4c_op`.`custdata`.`WriteChecks` AS `write_checks`,`base_is4c_op`.`custdata`.`StoreCoupons` AS `store_coupons`,`base_is4c_op`.`custdata`.`Type` AS `mem_type`,`base_is4c_op`.`custdata`.`memType` AS `mem_status`,`base_is4c_op`.`custdata`.`staff` AS `staff`,`base_is4c_op`.`custdata`.`Purchases` AS `purchases`,`base_is4c_op`.`custdata`.`NumberOfChecks` AS `number_of_checks`,`base_is4c_op`.`custdata`.`blueLine` AS `blue_line`,`base_is4c_op`.`custdata`.`Shown` AS `shown` from `base_is4c_op`.`custdata`;

-- --------------------------------------------------------

--
-- Structure for view `subdeptindex`
--
DROP TABLE IF EXISTS `subdeptindex`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `base_is4c_op`.`subdeptindex` AS select `p`.`upc` AS `upc`,`p`.`department` AS `department`,`d`.`dept_name` AS `dept_name`,`p`.`subdept` AS `subdept`,`s`.`subdept_name` AS `subdept_name` from ((`base_is4c_op`.`products` `p` join `base_is4c_op`.`departments` `d`) join `base_is4c_op`.`subdepts` `s`) where ((`p`.`department` = `d`.`dept_no`) and (`p`.`subdept` = `s`.`subdept_no`)) group by `p`.`upc`;

-- --------------------------------------------------------

--
-- Structure for view `volunteerDiscounts`
--
DROP TABLE IF EXISTS `volunteerDiscounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `base_is4c_op`.`volunteerDiscounts` AS select `base_is4c_op`.`custdata`.`CardNo` AS `CardNo`,`base_is4c_op`.`custdata`.`SSI` AS `hours`,(`base_is4c_op`.`custdata`.`SSI` * 20) AS `total`,`base_is4c_op`.`custdata`.`id` AS `id` from `base_is4c_op`.`custdata` where (`base_is4c_op`.`custdata`.`staff` = 3);
