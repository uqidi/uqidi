-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-07-27 01:05:23
-- 服务器版本： 5.6.23
-- PHP Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uqidi_admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `qd_admin`
--

CREATE TABLE IF NOT EXISTS `qd_admin` (
  `id` smallint(5) unsigned NOT NULL,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` char(38) NOT NULL DEFAULT '',
  `realname` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `roleid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `num` int(11) unsigned NOT NULL DEFAULT '0',
  `loginip` char(20) NOT NULL DEFAULT '',
  `logintime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_admin`
--

INSERT INTO `qd_admin` (`id`, `username`, `password`, `realname`, `email`, `roleid`, `status`, `num`, `loginip`, `logintime`) VALUES
(1, 'superman', 'fb9ceebe516b0d6dbf111861aa4aee9bb49d2f', 'superman', 'tao.wang1@youku.com', 1, 1, 49, '127.0.0.1', '2016-07-23 10:51:44');

-- --------------------------------------------------------

--
-- 表的结构 `qd_config`
--

CREATE TABLE IF NOT EXISTS `qd_config` (
  `code` varchar(30) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `utime` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_config`
--

INSERT INTO `qd_config` (`code`, `data`, `utime`, `ctime`) VALUES
('mail', '{"mail_server":"smtp.163.com","mail_port":"25","mail_user":"zhucemail003@163.com","mail_password":"wangtao","mail_from":"zhucemail003@163.com","mail_from_name":"zhucemail003","mail_reply":"zhucemail003@163.com","mail_reply_name":"zhucemail003"}', 1458441033, 1458441033),
('website', '{"site_name":"\\u6709\\u542f\\u8fea","site_title":"\\u6709\\u542f\\u8fea","keywords":"\\u6709\\u542f\\u8fea","description":"\\u6709\\u542f\\u8fea"}', 1466263751, 1466263751),
('basic', '{"site_name":"\\u6709\\u542f\\u8fea","site_title":"\\u6709\\u542f\\u8fea","keywords":"\\u6709\\u542f\\u8fea","description":"\\u6709\\u542f\\u8fea"}', 1466263741, 1466263741);

-- --------------------------------------------------------

--
-- 表的结构 `qd_log`
--

CREATE TABLE IF NOT EXISTS `qd_log` (
  `id` int(10) unsigned NOT NULL,
  `adminid` smallint(5) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL,
  `module` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `controller` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `action` varchar(15) CHARACTER SET latin1 NOT NULL,
  `log_info` text,
  `cip` char(15) CHARACTER SET latin1 NOT NULL,
  `create_time` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_log`
--

INSERT INTO `qd_log` (`id`, `adminid`, `username`, `module`, `controller`, `action`, `log_info`, `cip`, `create_time`) VALUES
(1, 1, 'superman', 'admin', 'Login', 'do_login', 'login', '127.0.0.1', '2016-06-18 23:11:31'),
(2, 1, 'superman', 'admin', 'SetUp', 'basic', 'basic', '127.0.0.1', '2016-06-18 23:29:01'),
(3, 1, 'superman', 'admin', 'SetUp', 'website', 'website', '127.0.0.1', '2016-06-18 23:29:11'),
(4, 1, 'superman', 'admin', 'SetUp', 'mailtpl_publish', 'upteBank', '127.0.0.1', '2016-06-18 23:59:36'),
(5, 1, 'superman', 'admin', 'SetUp', 'mailtpl_publish', 'upteBank', '127.0.0.1', '2016-06-19 00:00:39'),
(6, 1, 'superman', 'admin', 'SetUp', 'mailtpl_publish', 'upteBank', '127.0.0.1', '2016-06-19 00:00:50'),
(7, 1, 'superman', 'admin', 'Role', 'edit', '测试', '127.0.0.1', '2016-06-19 00:03:38'),
(8, 1, 'superman', 'admin', 'Role', 'edit', '产品', '127.0.0.1', '2016-06-19 00:03:43'),
(9, 1, 'superman', 'admin', 'Role', 'edit', '产品', '127.0.0.1', '2016-06-19 00:03:57'),
(10, 1, 'superman', 'admin', 'Menu', 'edit', '管理员模块 | 0 | admin | Main | init | fa-users | 0', '127.0.0.1', '2016-06-19 00:44:32'),
(11, 1, 'superman', 'admin', 'Menu', 'sort', 'sort', '127.0.0.1', '2016-06-19 00:44:37'),
(12, 1, 'superman', 'admin', 'Login', 'do_login', 'login', '127.0.0.1', '2016-06-19 12:44:01'),
(13, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 5 | 重庆 | ssccq | 1 | 1', '127.0.0.1', '2016-06-19 12:47:02'),
(14, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 5 | 江西 | sscjx | 3 | 1', '127.0.0.1', '2016-06-19 12:47:34'),
(15, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 5 | 新疆 | sscxj | 4 | 1', '127.0.0.1', '2016-06-19 12:48:17'),
(16, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 3 | 天津 | ssctj | 2 | 1', '127.0.0.1', '2016-06-19 12:48:48'),
(17, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 5 | 广东 | sscgd | 5 | 1', '127.0.0.1', '2016-06-19 12:49:25'),
(18, 1, 'superman', 'game', 'Ltype', 'edit', '5 | 天津 | ssctj | 2 | 1', '127.0.0.1', '2016-06-19 12:49:35'),
(19, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 5 | 上海 | sscsh | 6 | 1', '127.0.0.1', '2016-06-19 12:50:12'),
(20, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 3 | P3 | lowp3 | 9 | 1', '127.0.0.1', '2016-06-19 12:50:52'),
(21, 1, 'superman', 'game', 'Ltype', 'add', ' | 10 | 3 | 3D | low3d | 9 | 1', '127.0.0.1', '2016-06-19 12:51:24'),
(22, 1, 'superman', 'game', 'Ltype', 'edit', '3 | P3 | lowp3 | 7 | 1', '127.0.0.1', '2016-06-19 12:51:35'),
(23, 1, 'superman', 'game', 'Ltype', 'edit', '3 | 3D | low3d | 8 | 1', '127.0.0.1', '2016-06-19 12:51:40'),
(24, 1, 'superman', 'game', 'Ltype', 'publish', 'Game_ltype', '127.0.0.1', '2016-06-19 12:51:41'),
(25, 1, 'superman', 'game', 'Ltype', 'edit', '3 | 3D | low3d | 8 | 1', '127.0.0.1', '2016-06-19 12:52:15'),
(26, 1, 'superman', 'game', 'Ltype', 'edit', '3 | P3 | lowp3 | 7 | 1', '127.0.0.1', '2016-06-19 12:52:19'),
(27, 1, 'superman', 'game', 'Ltype', 'edit', '3 | 3D | low3d | 8 | 1', '127.0.0.1', '2016-06-19 12:52:31'),
(28, 1, 'superman', 'game', 'Ltype', 'edit', '3 | 3D | low3d | 8 | 1', '127.0.0.1', '2016-06-19 12:52:57'),
(29, 1, 'superman', 'game', 'Ltype', 'publish', 'Game_ltype', '127.0.0.1', '2016-06-19 12:53:48'),
(30, 1, 'superman', 'game', 'Gtype', 'add', ' | 五星 | direct_five | 170000.0 | 1000 | 1', '127.0.0.1', '2016-06-19 12:55:10'),
(31, 1, 'superman', 'game', 'Gtype', 'add', ' | 四星 | direct_four | 17000.0 | 100 | 1', '127.0.0.1', '2016-06-19 12:55:45'),
(32, 1, 'superman', 'game', 'Gtype', 'add', ' | 三星直选 | direct_three | 1700.0 | 10 | 1', '127.0.0.1', '2016-06-19 12:56:30'),
(33, 1, 'superman', 'game', 'Gtype', 'add', ' | 三星组三 | group_three | 570.0 | 3.20 | 1', '127.0.0.1', '2016-06-19 12:57:01'),
(34, 1, 'superman', 'game', 'Gtype', 'add', ' | 三星组六 | group_six | 280.0 | 1.72 | 1', '127.0.0.1', '2016-06-19 12:57:31'),
(35, 1, 'superman', 'game', 'Gtype', 'add', ' | 二星直选 | direct_two | 170.0 | 1 | 1', '127.0.0.1', '2016-06-19 12:57:57'),
(36, 1, 'superman', 'game', 'Gtype', 'add', ' | 二星组选 | group_two | 85.0 | 0.50 | 1', '127.0.0.1', '2016-06-19 12:58:28'),
(37, 1, 'superman', 'game', 'Gtype', 'add', ' | 定位胆 | orientation | 17.0 | 0.10 | 1', '127.0.0.1', '2016-06-19 12:58:54'),
(38, 1, 'superman', 'game', 'Gtype', 'add', ' | 不定位 | unlocated | 6.8 | 0 | 1', '127.0.0.1', '2016-06-19 12:59:29'),
(39, 1, 'superman', 'game', 'Gtype', 'add', ' | 三星混合组选 | group_mix | 570.0,280.0 | 3.20,1.72 | 1', '127.0.0.1', '2016-06-19 13:00:00'),
(40, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 五星直选 |  | 31 | ` | 1', '127.0.0.1', '2016-06-19 13:02:53'),
(41, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 四星直选前四 |  | 30 | 2 | 1', '127.0.0.1', '2016-06-19 13:03:15'),
(42, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 1 | 五星直选 |  | 31 | 1 | 1', '127.0.0.1', '2016-06-19 13:03:26'),
(43, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 2 | 四星直选前四 |  | 30 | 2 | 1', '127.0.0.1', '2016-06-19 13:03:42'),
(44, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 2 | 四星直选后四 |  | 15 | 3 | 1', '127.0.0.1', '2016-06-19 13:04:25'),
(45, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 三星直选前三 |  | 28 | 4 | 1', '127.0.0.1', '2016-06-19 13:04:45'),
(46, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 3 | 三星直选前三 |  | 28 | 4 | 1', '127.0.0.1', '2016-06-19 13:04:50'),
(47, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 三星直选中三 |  | 14 | 5 | 1', '127.0.0.1', '2016-06-19 13:19:28'),
(48, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 3 | 三星直选中三 |  | 14 | 5 | 1', '127.0.0.1', '2016-06-19 13:19:37'),
(49, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 三星直选后三 |  | 7 | 6 | 1', '127.0.0.1', '2016-06-19 13:20:01'),
(50, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 3 | 三星直选后三 |  | 7 | 6 | 1', '127.0.0.1', '2016-06-19 13:21:01'),
(51, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 3 | 三星直选任选 |  | 31 | 7 | 1', '127.0.0.1', '2016-06-19 13:21:32'),
(52, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 4 | 三星组三前三 |  | 28 |  | 1', '127.0.0.1', '2016-06-19 13:22:52'),
(53, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 4 | 三星组三前三 |  | 28 | 8 | 1', '127.0.0.1', '2016-06-19 13:23:00'),
(54, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 4 | 三星组三中三 |  | 14 |  | 1', '127.0.0.1', '2016-06-19 13:23:26'),
(55, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 4 | 三星组三中三 |  | 14 | 9 | 1', '127.0.0.1', '2016-06-19 13:23:34'),
(56, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 4 | 三星组三后三 |  | 7 |  | 1', '127.0.0.1', '2016-06-19 13:24:15'),
(57, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 4 | 三星组三后三 |  | 7 | 10 | 1', '127.0.0.1', '2016-06-19 13:24:20'),
(58, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 三星组三任选 |  | 31 | 11 | 1', '127.0.0.1', '2016-06-19 13:24:57'),
(59, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 4 | 三星组三任选 |  | 31 | 11 | 1', '127.0.0.1', '2016-06-19 13:25:06'),
(60, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 5 | 三星组六前三 |  | 28 | 13 | 1', '127.0.0.1', '2016-06-19 13:25:38'),
(61, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 5 | 三星组六前三 |  | 28 | 12 | 1', '127.0.0.1', '2016-06-19 13:25:46'),
(62, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 5 | 三星组六中三 |  | 14 | 13 | 1', '127.0.0.1', '2016-06-19 13:26:10'),
(63, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 5 | 三星组六后三 |  | 7 | 14 | 1', '127.0.0.1', '2016-06-19 13:26:30'),
(64, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 5 | 三星组六任选 |  | 31 | 15 | 1', '127.0.0.1', '2016-06-19 13:26:49'),
(65, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 6 | 二星直选前二 |  | 24 |  | 1', '127.0.0.1', '2016-06-19 13:29:11'),
(66, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 6 | 二星直选前二 |  | 24 | 16 | 1', '127.0.0.1', '2016-06-19 13:29:19'),
(67, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 6 | 二星直选后二 |  | 3 |  | 1', '127.0.0.1', '2016-06-19 13:29:49'),
(68, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 6 | 二星直选后二 |  | 3 | 17 | 1', '127.0.0.1', '2016-06-19 13:30:00'),
(69, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 二星直选任选 |  | 31 | 18 | 1', '127.0.0.1', '2016-06-19 13:30:32'),
(70, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 6 | 二星直选任选 |  | 31 | 18 | 1', '127.0.0.1', '2016-06-19 13:30:40'),
(71, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 7 | 二星组选前二 |  | 24 | 19 | 1', '127.0.0.1', '2016-06-19 13:30:59'),
(72, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 7 | 二星组选后二 |  | 3 | 20 | 1', '127.0.0.1', '2016-06-19 13:31:16'),
(73, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 7 | 二星组选任选 |  | 31 | 21 | 1', '127.0.0.1', '2016-06-19 13:31:40'),
(74, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 8 | 定位胆 |  | 31 | 22 | 1', '127.0.0.1', '2016-06-19 13:32:15'),
(75, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 9 | 不定位前三 |  | 28 | 23 | 1', '127.0.0.1', '2016-06-19 13:32:51'),
(76, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 不定位中三 |  | 14 | 24 | 1', '127.0.0.1', '2016-06-19 13:33:12'),
(77, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 9 | 不定位中三 |  | 14 | 24 | 1', '127.0.0.1', '2016-06-19 13:33:17'),
(78, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 9 | 不定位后三 |  | 7 | 25 | 1', '127.0.0.1', '2016-06-19 13:33:51'),
(79, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 10 | 三星混合组选前三 |  | 28 |  | 1', '127.0.0.1', '2016-06-19 13:34:34'),
(80, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 10 | 三星混合组选前三 |  | 28 | 26 | 1', '127.0.0.1', '2016-06-19 13:34:44'),
(81, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 1 | 三星混合组选中三 |  | 14 | 27 | 1', '127.0.0.1', '2016-06-19 13:34:59'),
(82, 1, 'superman', 'game', 'Lottery', 'edit', '1 | 10 | 三星混合组选中三 |  | 14 | 27 | 1', '127.0.0.1', '2016-06-19 13:35:08'),
(83, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 10 | 三星混合组选后三 |  | 7 | 28 | 1', '127.0.0.1', '2016-06-19 13:35:24'),
(84, 1, 'superman', 'game', 'Lottery', 'add', ' | 1 | 10 | 三星混合组任选 |  | 31 | 29 | 1', '127.0.0.1', '2016-06-19 13:35:49'),
(85, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 3 | 三星直选 |  | 7 | 1 | 1', '127.0.0.1', '2016-06-19 13:49:03'),
(86, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 4 | 三星组三 |  | 7 |  | 1', '127.0.0.1', '2016-06-19 13:49:19'),
(87, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 5 | 三星组六 |  | 7 | 3 | 1', '127.0.0.1', '2016-06-19 13:49:38'),
(88, 1, 'superman', 'game', 'Lottery', 'edit', '7 | 4 | 三星组三 |  | 7 | 2 | 1', '127.0.0.1', '2016-06-19 13:49:42'),
(89, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 10 | 三星混合组选 |  | 7 | 4 | 1', '127.0.0.1', '2016-06-19 13:50:03'),
(90, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 1 | 二星直选前二 |  | 6 | 5 | 1', '127.0.0.1', '2016-06-19 13:50:29'),
(91, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 1 | 二星直选后二 |  | 3 | 6 | 1', '127.0.0.1', '2016-06-19 13:50:46'),
(92, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 7 | 二星组选前二 |  | 6 | 7 | 1', '127.0.0.1', '2016-06-19 13:52:43'),
(93, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 7 | 二星组选后二 |  | 3 | 8 | 1', '127.0.0.1', '2016-06-19 13:53:02'),
(94, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 8 | 定位胆 |  | 7 |  | 1', '127.0.0.1', '2016-06-19 13:53:25'),
(95, 1, 'superman', 'game', 'Lottery', 'edit', '7 | 8 | 定位胆 |  | 7 | 9 | 1', '127.0.0.1', '2016-06-19 13:53:33'),
(96, 1, 'superman', 'game', 'Lottery', 'add', ' | 7 | 9 | 不定位 |  | 7 | 10 | 1', '127.0.0.1', '2016-06-19 13:53:51'),
(97, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 3 | 三星直选 |  | 7 | 1 | 1', '127.0.0.1', '2016-06-19 14:01:19'),
(98, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 4 | 三星组三 |  | 7 | 2 | 1', '127.0.0.1', '2016-06-19 14:01:50'),
(99, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 5 | 三星组六 |  | 7 | 3 | 1', '127.0.0.1', '2016-06-19 14:02:20'),
(100, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 10 | 三星混合组选 |  | 7 | 4 | 1', '127.0.0.1', '2016-06-19 14:03:19'),
(101, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 6 | 二星直选前二 |  | 6 | 5 | 1', '127.0.0.1', '2016-06-19 14:03:50'),
(102, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 6 | 二星直选后二 |  | 3 | 6 | 1', '127.0.0.1', '2016-06-19 14:04:53'),
(103, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 7 | 二星组选前二 |  | 6 | 7 | 1', '127.0.0.1', '2016-06-19 14:05:44'),
(104, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 7 | 二星组选后二 |  | 3 | 8 | 1', '127.0.0.1', '2016-06-19 14:06:07'),
(105, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 8 | 定位胆 |  | 7 | 9 | 1', '127.0.0.1', '2016-06-19 14:06:34'),
(106, 1, 'superman', 'game', 'Lottery', 'add', ' | 8 | 9 | 不定位 |  | 7 | 10 | 1', '127.0.0.1', '2016-06-19 14:06:51'),
(107, 1, 'superman', 'game', 'Lottery', 'publish', 'Game_Type', '127.0.0.1', '2016-06-19 14:10:36'),
(108, 1, 'superman', 'game', 'Lottery', 'publish', 'Game_Type', '127.0.0.1', '2016-06-19 14:11:17'),
(109, 1, 'superman', 'admin', 'Login', 'login', 'login | auto', '127.0.0.1', '2016-07-03 11:07:29'),
(110, 1, 'superman', 'game', 'Gtype', 'edit', '五星组选 | direct_five | 170000.0 | 1000 | 1', '127.0.0.1', '2016-07-03 12:09:58'),
(111, 1, 'superman', 'game', 'Gtype', 'edit', '五星直选 | direct_five | 170000.0 | 1000 | 1', '127.0.0.1', '2016-07-03 12:10:05'),
(112, 1, 'superman', 'game', 'Gtype', 'edit', '四星直选 | direct_four | 17000.0 | 100 | 1', '127.0.0.1', '2016-07-03 12:10:10'),
(113, 1, 'superman', 'game', 'Gtype', 'publish', 'Game_gtype', '127.0.0.1', '2016-07-03 21:47:09'),
(114, 1, 'superman', 'admin', 'SetUp', 'basic_publish', 'basic', '127.0.0.1', '2016-07-03 23:03:51'),
(115, 1, 'superman', 'admin', 'Login', 'login', 'login | auto', '127.0.0.1', '2016-07-10 18:48:18'),
(116, 1, 'superman', 'admin', 'Login', 'do_login', 'login', '127.0.0.1', '2016-07-23 10:51:44'),
(117, 1, 'superman', 'user', 'User', 'edit', 'Caesar', '127.0.0.1', '2016-07-23 10:55:17');

-- --------------------------------------------------------

--
-- 表的结构 `qd_mailtpl`
--

CREATE TABLE IF NOT EXISTS `qd_mailtpl` (
  `id` tinyint(3) unsigned NOT NULL,
  `code` char(8) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `is_html` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `expires` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(300) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `utime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_mailtpl`
--

INSERT INTO `qd_mailtpl` (`id`, `code`, `is_html`, `expires`, `subject`, `content`, `utime`) VALUES
(1, 'upteBank', 1, 3600, '修改银行信息', '<html><body>{$username}您好！<br /><br><br />我们收到了一个修改银行资料的请求。<br><br />此为您修改银行资料的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有申请修改银行资料请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0),
(2, 'findPswd', 1, 3600, '找回登录密码', '<html><body>{$username}，您好！<br /><br><br />我们收到了一个修改登陆密码的请求。<br><br />此为您找回登陆密码的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有申请修改登陆密码请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0),
(3, 'findBPwd', 1, 3600, '修改交易密码', '<html><body>{$username}，您好！<br /><br><br />我们收到了一个修改交易密码的请求。<br><br />此为您找回交易密码的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有申请修改交易密码请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0),
(4, 'actCheck', 1, 3600, '活动验证码', '<html><body>{$username}您好！<br /><br><br />我们收到了一个获取活动验证码的请求。<br><br />此为您参与活动的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有发送获取活动验证码的请求请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0),
(5, 'bindMail', 1, 3600, '绑定安全邮箱', '<html><body>{$username}，您好！<br /><br><br />我们收到了一个绑定安全邮箱的请求。<br><br />此为您绑定安全邮箱的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有申请绑定安全邮箱请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0),
(6, 'uptBMail', 1, 3600, '修改安全邮箱', '<html><body>{$username}，您好！<br /><br><br />我们收到了一个修改安全邮箱的请求。<br><br />此为您修改安全邮箱的验证码：<font color=\\\\"red\\\\">{$code}</font>。<br>请您在{$expires}小时内尽快完成操作，如果您没有申请修改安全邮箱请忽略此邮件，谢谢合作。<br><br /><br><br /></html></body>', 0);

-- --------------------------------------------------------

--
-- 表的结构 `qd_menu`
--

CREATE TABLE IF NOT EXISTS `qd_menu` (
  `id` smallint(5) unsigned NOT NULL,
  `name` char(60) NOT NULL DEFAULT '',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `module` varchar(15) NOT NULL DEFAULT '',
  `controller` varchar(15) NOT NULL DEFAULT '',
  `action` varchar(15) NOT NULL DEFAULT '',
  `icon` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(300) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `display` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_menu`
--

INSERT INTO `qd_menu` (`id`, `name`, `pid`, `module`, `controller`, `action`, `icon`, `description`, `listorder`, `display`) VALUES
(1, '管理员模块', 0, 'admin', 'Main', 'init', 'fa-users', '', 0, 1),
(3, '管理员管理', 1, 'admin', 'Admin', 'index', '', '', 0, 1),
(4, '菜单管理', 1, 'admin', 'Menu', 'index', '', '', 2, 1),
(5, '角色管理', 1, 'admin', 'Role', 'index', '', '', 1, 1),
(7, '添加菜单', 4, 'admin', 'Menu', 'add', '', '', 1, 1),
(30, '添加角色', 5, 'admin', 'Role', 'add', '', '', 1, 1),
(24, '删除菜单', 4, 'admin', 'Menu', 'delete', '', '', 3, 1),
(25, '预览菜单', 4, 'admin', 'Menu', 'show', '', '', 0, 1),
(31, '编辑角色', 5, 'admin', 'Role', 'edit', '', '', 2, 1),
(32, '编辑菜单', 4, 'admin', 'Menu', 'edit', '', '', 2, 1),
(33, '预览角色', 5, 'admin', 'Role', 'show', '', '', 0, 1),
(34, '删除角色', 5, 'admin', 'Role', 'delete', '', '', 3, 1),
(35, '预览管理员', 3, 'admin', 'Admin', 'show', '', '', 0, 1),
(36, '添加管理员', 3, 'admin', 'Admin', 'add', '', '', 1, 1),
(37, '编辑管理员', 3, 'admin', 'Admin', 'edit', '', '', 2, 1),
(38, '删除管理员', 3, 'admin', 'Admin', 'delete', '', '', 3, 1),
(40, '后台日志', 1, 'admin', 'Log', 'index', '', '', 3, 1),
(41, '设置模块', 0, 'admin', 'SetUp', 'init', 'linecons-cog', '', 1, 1),
(42, '网站设置', 41, 'admin', 'SetUp', 'website', '', '', 0, 1),
(2, '用户模块', 0, 'user', 'User', 'init', 'fa-user', '', 2, 1),
(6, '用户管理', 2, 'user', 'User', 'index', '', '', 0, 1),
(43, '基本设置', 41, 'admin', 'SetUp', 'basic', '', '', 1, 1),
(69, '邮件模版', 41, 'admin', 'SetUp', 'mailtpl', '', '', 3, 1),
(44, '邮件设置', 41, 'admin', 'SetUp', 'mail', '', '', 2, 1),
(50, '用户组管理', 2, 'user', 'Group', 'index', '', '', 1, 1),
(45, '用户银行', 2, 'user', 'Bank', 'index', '', '', 2, 1),
(58, '支付管理', 2, 'user', 'Pay', 'index', '', '', 3, 1),
(52, '显示基本资料', 6, 'user', 'User', 'show', '', '', 0, 1),
(53, '添加用户', 6, 'user', 'User', 'add', '', '', 1, 1),
(54, '编辑用户', 6, 'user', 'User', 'edit', '', '', 2, 1),
(55, '编辑信息', 6, 'user', 'User', 'editInfo', '', '', 3, 1),
(56, '编辑配置信息', 6, 'user', 'User', 'editConfig', '', '', 4, 1),
(57, '编辑银行信息', 6, 'user', 'User', 'editBank', '', '', 5, 1),
(66, '启动用户', 6, 'user', 'User', 'enable', '', '', 6, 1),
(67, '禁用用户', 6, 'user', 'User', 'disable', '', '', 7, 1),
(68, '删除用户', 6, 'user', 'User', 'delete', '', '', 8, 1),
(59, '显示用户组', 50, 'user', 'Group', 'show', '', '', 0, 1),
(60, '新增用户组', 50, 'user', 'Group', 'add', '', '', 1, 1),
(61, '编辑会员组', 50, 'user', 'Group', 'edit', '', '', 2, 1),
(46, '显示用户银行', 45, 'user', 'Bank', 'show', '', '', 0, 1),
(47, '添加用户银行', 45, 'user', 'Bank', 'add', '', '', 1, 1),
(48, '编辑用户银行', 45, 'user', 'Bank', 'edit', '', '', 2, 1),
(49, '删除用户银行', 45, 'user', 'Bank', 'delete', '', '', 3, 1),
(62, '显示支付信息', 58, 'user', 'Pay', 'show', '', '', 0, 1),
(63, '新增支付信息', 58, 'user', 'Pay', 'add', '', '', 1, 1),
(64, '编辑支付信息', 58, 'user', 'Pay', 'edit', '', '', 2, 1),
(51, '玩法模块', 0, 'game', 'Game', 'init', 'fa-gamepad', '', 3, 1),
(71, '彩票玩法', 51, 'game', 'Lottery', 'index', '', '', 0, 1),
(65, '彩票类型', 51, 'game', 'Ltype', 'index', '', '', 1, 1),
(70, '玩法类型', 51, 'game', 'Gtype', 'index', '', '', 2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `qd_role`
--

CREATE TABLE IF NOT EXISTS `qd_role` (
  `id` smallint(5) unsigned NOT NULL,
  `role_name` char(30) NOT NULL DEFAULT '',
  `priv` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_role`
--

INSERT INTO `qd_role` (`id`, `role_name`, `priv`, `status`) VALUES
(1, '超级管理员', 'all', 1),
(2, '开发', '1,3,5,4,7,25,24', 1),
(3, '运营', '1,4,25,7,32,24,2,6', 1),
(4, '产品', '2,6,52,53,54,55,56,57,66,67,68,50,59,60,61,45,46,47,48,49,58,62,63,64', 1),
(5, '测试', 'all', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `qd_admin`
--
ALTER TABLE `qd_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qd_config`
--
ALTER TABLE `qd_config`
  ADD PRIMARY KEY (`code`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `qd_log`
--
ALTER TABLE `qd_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qd_mailtpl`
--
ALTER TABLE `qd_mailtpl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qd_menu`
--
ALTER TABLE `qd_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `module` (`module`,`controller`,`action`),
  ADD KEY `listorder` (`listorder`);

--
-- Indexes for table `qd_role`
--
ALTER TABLE `qd_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `qd_admin`
--
ALTER TABLE `qd_admin`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `qd_log`
--
ALTER TABLE `qd_log`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `qd_mailtpl`
--
ALTER TABLE `qd_mailtpl`
  MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `qd_menu`
--
ALTER TABLE `qd_menu`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT for table `qd_role`
--
ALTER TABLE `qd_role`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
