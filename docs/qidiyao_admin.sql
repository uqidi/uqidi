-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-07-27 02:15:51
-- 服务器版本： 5.6.23
-- PHP Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `qidiyao_admin`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qd_admin`
--

INSERT INTO `qd_admin` (`id`, `username`, `password`, `realname`, `email`, `roleid`, `status`, `num`, `loginip`, `logintime`) VALUES
(1, 'superman', 'fb9ceebe516b0d6dbf111861aa4aee9bb49d2f', 'superman', 'tao.wang1@youku.com', 1, 1, 52, '127.0.0.1', '2016-07-27 02:07:44');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(43, '基本设置', 41, 'admin', 'SetUp', 'basic', '', '', 1, 1),
(69, '邮件模版', 41, 'admin', 'SetUp', 'mailtpl', '', '', 3, 1),
(44, '邮件设置', 41, 'admin', 'SetUp', 'mail', '', '', 2, 1);

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
-- AUTO_INCREMENT for table `qd_menu`
--
ALTER TABLE `qd_menu`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT for table `qd_role`
--
ALTER TABLE `qd_role`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;