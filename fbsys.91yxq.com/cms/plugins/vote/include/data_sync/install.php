<?php

$table_header =& $db_config['table_pre'];
$install_sql = "CREATE TABLE `{$table_header}plugin_vote_ipaddress` (\r\n  `vote_ip` varchar(16) NOT NULL default '',\r\n  `vote_time` int(10) NOT NULL default '0',\r\n  `topic_id` int(10) NOT NULL default '0'\r\n) TYPE=MyISAM;\r\n\r\n\r\nCREATE TABLE `{$table_header}plugin_vote_option` (\r\n  `id` int(10) unsigned NOT NULL auto_increment,\r\n  `nclass` varchar(45) default NULL,\r\n  `anclassid` int(10) unsigned default NULL,\r\n  `sequence` int(10) unsigned default NULL,\r\n  `votenum` int(10) unsigned default NULL,\r\n  `createdate` int(10) unsigned default NULL,\r\n  `createid` varchar(50) binary default NULL,\r\n  `editdate` int(10) unsigned default NULL,\r\n  `editid` varchar(50) binary default NULL,\r\n  PRIMARY KEY  (`id`)\r\n) TYPE=MyISAM;\r\n\r\n\r\nCREATE TABLE `{$table_header}plugin_vote_title` (\r\n  `id` int(10) unsigned NOT NULL auto_increment,\r\n  `topic_name` varchar(45) default NULL,\r\n  `sequence` int(10) unsigned default NULL,\r\n  `createdate` int(10) unsigned default NULL,\r\n  `createid` varchar(50) binary default NULL,\r\n  `editdate` int(10) unsigned default NULL,\r\n  `editid` varchar(50) binary default NULL,\r\n  `choose` int(10) unsigned default NULL,\r\n  `cheat` varchar(10) default '24',\r\n  `voteTPL` varchar(250) NOT NULL default '',\r\n  `resultsTPL` varchar(250) NOT NULL default '',\r\n  PRIMARY KEY  (`id`)\r\n) TYPE=MyISAM;\r\n\r\nINSERT INTO `{$table_header}plugin_vote_title` VALUES (1, '你喜欢哪种类型的电脑游戏?', 0, 1113673971, 0x7a686977757368616e, 1113674287, 0x7a686977757368616e, 0, '24', '/plugins/vote/vote.html', '/plugins/vote/vote_results.html');\r\nINSERT INTO `{$table_header}plugin_vote_title` VALUES (2, '你对本站的整体印象如何?', 0, 1113674329, 0x7a686977757368616e, 1113674359, 0x7a686977757368616e, 1, '24', '/plugins/vote/vote.html', '/plugins/vote/vote_results.html');\r\n\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (1, '即时战略（魔兽）', 1, 9, 46, 1113674434, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (2, '角色扮演（传奇）', 1, 8, 105, 1113674443, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (3, '射击战斗（反恐）', 1, 7, 20, 1113674451, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (4, '比赛类（泡泡堂）', 1, 6, 50, 1113674459, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (5, '运气（拉霸）', 1, 5, 13, 1113674465, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (6, '休闲娱乐（上海麻将）', 1, 4, 8, 1113674473, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (7, '赛车', 1, 3, 7, 1113674488, 0x7a686977757368616e, 1113674616, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (8, '我对这个网站基本比较满意，但仍需改进！', 2, 0, 50, 1113674694, 0x7a686977757368616e, 1113674881, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (9, '说实话，不好意思！我不认为这是个好网站！', 2, 0, 0, 1113674710, 0x7a686977757368616e, 1113674881, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (10, '我对网站整体非常的满意', 2, 0, 10, 1113674719, 0x7a686977757368616e, 1113674881, 0x7a686977757368616e);\r\nINSERT INTO `{$table_header}plugin_vote_option` VALUES (11, '我队外观设计还很满意！但我对内容不太满意！', 2, 0, 20, 1113674727, 0x7a686977757368616e, 1113674881, 0x7a686977757368616e);";
$result = plugin_runquery( $install_sql );
?>