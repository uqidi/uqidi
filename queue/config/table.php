<?php
return array(
    'Order_Order' => array(
        'fields'    => array(
            "`id` int(10) unsigned NOT NULL AUTO_INCREMENT",
            "`otype` tinyint(2) unsigned NOT NULL DEFAULT '10'",
            "`uid` int(10) unsigned NOT NULL DEFAULT '0'",
            "`order_no` char(18) CHARACTER SET latin1 NOT NULL",
            "`money` decimal(12,2) NOT NULL DEFAULT '0.00'",
            "`at_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00'",
            "`ext` text NOT NULL",
            "`status` tinyint(2) unsigned NOT NULL DEFAULT '1'",
            "`ctime` int(10) unsigned NOT NULL DEFAULT '0'",
        ),
        'keys'      => array(
            "PRIMARY KEY (`id`)",
            "UNIQUE KEY `uid_type_order` (`uid`,`otype`,`order_no`) USING BTREE",
        ),
        'options'   => 'ENGINE=InnoDB DEFAULT CHARSET=utf8',
    ),
    'Game_Game' => array(
        'fields'    => array(
            "`id` int(10) unsigned NOT NULL AUTO_INCREMENT",
            "`uid` int(10) unsigned NOT NULL DEFAULT '0'",
            "`order_no` char(18) CHARACTER SET latin1 NOT NULL",
            "`ltype_id` tinyint(3) unsigned NOT NULL DEFAULT '0'",
            "`gtype_id` smallint(5) unsigned NOT NULL DEFAULT '0'",
            "`stype` tinyint(3) unsigned NOT NULL DEFAULT '0'",
            "`code_pos` tinyint(3) unsigned NOT NULL DEFAULT '0'",
            "`issue` bigint(11) unsigned NOT NULL DEFAULT '0'",
            "`times` decimal(8,2) unsigned NOT NULL DEFAULT '0.00'",
            "`mode` tinyint(3) unsigned NOT NULL DEFAULT '0'",
            "`money` decimal(12,2) unsigned NOT NULL DEFAULT '0.00'",
            "`win_state` tinyint(2) unsigned NOT NULL DEFAULT '0'",
            "`codes` mediumtext CHARACTER SET latin1 NOT NULL",
            "`win_code` varchar(15) CHARACTER SET latin1 NOT NULL",
            "`win_times` decimal(8,2) unsigned NOT NULL DEFAULT '0.00'",
            "`win_money` decimal(12,2) unsigned NOT NULL DEFAULT '0.00'",
            "`at_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00'",
            "`bonus` decimal(12,2) unsigned NOT NULL DEFAULT '0.00'",
            "`percent` decimal(3,1) unsigned NOT NULL DEFAULT '0.00'",
            "`status` tinyint(2) unsigned NOT NULL DEFAULT '1'",
            "`ctime` int(10) unsigned NOT NULL DEFAULT '0'",
        ),
        'keys'      => array(
            "PRIMARY KEY (`id`)",
            "KEY `uid` (`uid`)",
            "UNIQUE KEY `order_no` (`order_no`) USING BTREE",
        ),
        'options'   => 'ENGINE=InnoDB DEFAULT CHARSET=utf8',
    ),
);

