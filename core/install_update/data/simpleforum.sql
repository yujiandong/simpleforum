DROP TABLE IF EXISTS simple_user;
CREATE TABLE simple_user (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default 8,
  `role` tinyint(1) unsigned NOT NULL default 0,
  `score` mediumint(8) unsigned NOT NULL default 0,
  `username` char(16) NOT NULL,
  `email` char(50) NOT NULL,
  `password_hash` char(80) NOT NULL,
  `auth_key` char(32) NOT NULL,
  `avatar` char(50) NOT NULL default 'avatar/0_{size}.png',
  `comment` char(20) NOT NULL default '',
  `name` varchar(40) NOT NULL default '',
  PRIMARY KEY id(`id`),
  UNIQUE KEY username(`username`),
  UNIQUE KEY email(`email`),
  KEY status_id(`status`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_user_info;
CREATE TABLE simple_user_info (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `last_login_at` int(10) unsigned NOT NULL,
  `last_login_ip` int(10) unsigned NOT NULL,
  `reg_ip` int(10) unsigned NOT NULL,
  `topic_count` mediumint(8) unsigned NOT NULL default 0,
  `comment_count` mediumint(8) unsigned NOT NULL default 0,
  `follower_count` smallint(6) unsigned NOT NULL default 0,
  `following_count` smallint(6) unsigned NOT NULL default 0,
  `favorite_node_count` smallint(6) unsigned NOT NULL default 0,
  `favorite_topic_count` smallint(6) unsigned NOT NULL default 0,
  `website` varchar(100) NOT NULL default '',
  `about` varchar(255) NOT NULL default '',
  PRIMARY KEY user_id(`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_token;
CREATE TABLE simple_token (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default 0,
  `user_id` mediumint(8) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL COLLATE utf8_bin,
  `ext` varchar(200) NOT NULL default '',
  PRIMARY KEY id(`id`),
  UNIQUE KEY token(`token`),
  KEY user_type_expires(`user_id`, `type`, `expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_siteinfo;
CREATE TABLE simple_siteinfo (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `nodes` smallint(6) unsigned NOT NULL default 0,
  `users` mediumint(8) unsigned NOT NULL default 0,
  `topics` mediumint(8) unsigned NOT NULL default 0,
  `comments` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY id(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO simple_siteinfo VALUES(1, 1, 0, 0, 0);

DROP TABLE IF EXISTS simple_node;
CREATE TABLE simple_node (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `topic_count` mediumint(8) unsigned NOT NULL default 0,
  `favorite_count` smallint(6) unsigned NOT NULL default 0,
  `access_auth` tinyint(1) unsigned NOT NULL default 0,
  `invisible` tinyint(1) unsigned NOT NULL default 0,
  `name` varchar(20) NOT NULL,
  `ename` varchar(20) NOT NULL,
  `about` varchar(255) NOT NULL default '',
  PRIMARY KEY id(`id`),
  UNIQUE KEY name(`name`),
  UNIQUE KEY ename(`ename`),
  KEY topic_id(`topic_count`, `id`),
  KEY invisible(`invisible`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO simple_node VALUES(1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 0, '默认分类', 'default', '');

DROP TABLE IF EXISTS simple_navi;
CREATE TABLE simple_navi (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default 0,
  `sortid` tinyint(1) unsigned NOT NULL default 50,
  `name` varchar(20) NOT NULL,
  `ename` varchar(20) NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY name(`type`, `name`),
  UNIQUE KEY ename(`type`, `ename`),
  KEY type_sort(`type`,`sortid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_navi_node;
CREATE TABLE simple_navi_node (
  `id` int(10) unsigned NOT NULL auto_increment,
  `navi_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL default 0,
  `sortid` tinyint(1) unsigned NOT NULL default 50,
  PRIMARY KEY id(`id`),
  UNIQUE KEY navi_node(`navi_id`,`node_id`),
  KEY navi_node_sort(`navi_id`,`node_id`,`sortid`),
  KEY navi_node_visible_sort(`navi_id`,`node_id`,`visible`,`sortid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_topic;
CREATE TABLE simple_topic (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `replied_at` int(10) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `reply_id` mediumint(8) unsigned NOT NULL default 0,
  `alltop` tinyint(1) unsigned NOT NULL default 0,
  `top` tinyint(1) unsigned NOT NULL default 0,
  `invisible` tinyint(1) unsigned NOT NULL default 0,
  `closed` tinyint(1) unsigned NOT NULL default 0,
  `access_auth` tinyint(1) unsigned NOT NULL default 0,
  `comment_closed` tinyint(1) unsigned NOT NULL default 0,
  `comment_count` mediumint(8) unsigned NOT NULL default 0,
  `favorite_count` smallint(6) unsigned NOT NULL default 0,
  `good` smallint(6) unsigned NOT NULL default 0,
  `views` mediumint(8) unsigned NOT NULL default 0,
  `title` char(120) NOT NULL,
  `tags` char(60) NOT NULL default '',
  PRIMARY KEY id(`id`),
  KEY alllist(`node_id`, `alltop`, `replied_at`, `id`),
  KEY nodelist(`node_id`, `top`, `replied_at`, `id`),
  KEY hottopics(`node_id`, `created_at`, `comment_count`, `replied_at`),
  KEY updated(`updated_at`),
  KEY node_updated(`node_id`,`updated_at`),
  KEY allcount(`node_id`, `id`),
  KEY user_id(`user_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_topic_content;
CREATE TABLE simple_topic_content (
  `topic_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content` text NOT NULL,
  PRIMARY KEY topic_id(`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_comment;
CREATE TABLE simple_comment (
  `id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  `position` mediumint(8) unsigned NOT NULL auto_increment,
  `invisible` tinyint(1) unsigned NOT NULL default 0,
  `good` smallint(6) unsigned NOT NULL default 0,
  `content` text NOT NULL,
  PRIMARY KEY topic_position(`topic_id`, `position`),
  UNIQUE KEY id(`id`),
  KEY user_id(`user_id`,`id`),
  KEY topic_updated(`topic_id`, `updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_commentid;
CREATE TABLE simple_commentid (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `simple_notice`;
CREATE TABLE `simple_notice` (
  `id` int(10) NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default 0,
  `target_id` mediumint(8) unsigned NOT NULL,
  `source_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL default 0,
  `position` mediumint(8) unsigned NOT NULL default 0,
  `notice_count` smallint(6) unsigned NOT NULL default 0,
  `msg` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY target_status_id(`target_id`,`status`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_tag;
CREATE TABLE simple_tag (
  `id` int(10) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `topic_count` smallint(6) unsigned NOT NULL default 0,
  PRIMARY KEY id(`id`),
  UNIQUE KEY name(`name`),
  KEY updated(`updated_at`, `id`),
  KEY topic_count(`topic_count`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_tag_topic;
CREATE TABLE simple_tag_topic (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY tag_topic(`tag_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_link;
CREATE TABLE simple_link (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `sortid` tinyint(1) unsigned NOT NULL default 50,
  `name` varchar(20) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY id(`id`),
  KEY sort_id(`sortid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO simple_link VALUES(1, 0, 'SimpleForum', 'http://simpleforum.org/');


DROP TABLE IF EXISTS simple_setting;
CREATE TABLE simple_setting (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `sortid` tinyint(1) unsigned NOT NULL default 50,
  `block` varchar(10) NOT NULL default '',
  `label` varchar(50) NOT NULL default '',
  `type` varchar(10) NOT NULL default 'text',
  `key` varchar(50) NOT NULL,
  `value_type` varchar(10) NOT NULL default 'text',
  `value` text NOT NULL,
  `option` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY `key`(`key`),
  KEY block_sort_id(`block`,`sortid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO simple_setting(`sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `description`, `option`) VALUES
(1,'info', 'Website name','text', 'site_name','text', 'Simple Forum', 'settings_desc_site_name', ''),
(2,'info', 'Slogan','text', 'slogan','text', 'Simple Forum', 'settings_desc_slogan', ''),
(3,'info', 'Description','textarea', 'description','text', 'Simple Forum', 'settings_desc_description', ''),
(4,'info', 'Admin email', 'text', 'admin_email','text', '', 'settings_desc_admin_email', ''),
(5,'info', 'Language', 'select', 'language','text', 'en-US', 'settings_desc_language', ''),
(6,'info', 'Timezone', 'select','timezone','text', 'UTC', 'settings_desc_timezone', ''),
(1,'manage', 'Maintenance', 'select','offline','integer', '0', 'settings_desc_offline', '["0(Online)","1(Offline)"]'),
(2,'manage', 'Maintenance message', 'textarea','offline_msg','text', 'Website is under maintenance', 'settings_desc_offline_msg', ''),
(3,'manage', 'Private mode', 'select','access_auth','integer', '0', 'settings_desc_public', '["0(Public)","1(Members only)"]'),
(4,'manage', 'Verify email', 'select','email_verify','integer', '0', 'settings_desc_email_verify', '["0(Off)","1(On)"]'),
(5,'manage', 'Admin approve', 'select','admin_verify','integer', '0', 'settings_desc_admin_verify', '["0(Off)","1(On)"]'),
(6,'manage', 'Close register', 'select','close_register', 'integer', '0','settings_desc_close_register', '["0(Open)","1(Close)","2(Invite to register)"]'),
(7,'manage', 'Username filter', 'text','username_filter', 'text', '','settings_desc_username_filter', ''),
(8,'manage', 'Name filter', 'text','name_filter', 'text', '','settings_desc_name_filter', ''),
(9,'manage', 'Enable captcha', 'select','captcha', 'text', '0','settings_desc_enable_captcha', '{"":"No Captcha"}'),
(10,'manage', 'Autolink', 'select','autolink', 'integer', '0','settings_desc_autolink', '["0(Off)","1(On)"]'),
(11,'manage', 'Autolink filter', 'textarea','autolink_filter', 'text', '','settings_desc_autolink_filter', ''),
(12,'manage', 'Theme', 'text','theme', 'text', '','settings_desc_theme', ''),
(13,'manage', 'Mobile theme', 'text','theme_mobile', 'text', 'sf-mobile','settings_desc_theme_mobile', ''),
(14,'manage', 'Groups', 'textarea','groups', 'text', '2000 Regular\n5000 Silver\n10000 Gold\n18000 Platinum\n30000 Diamond','settings_desc_groups', ''),
(1,'extend', 'Head settings<br />in head tag<br />meta, etc', 'textarea','head_meta', 'text', '','settings_desc_head_meta', ''),
(2,'extend', 'Bottom settings<br />analytics codes, etc', 'textarea','analytics_code', 'text', '','settings_desc_analytics_code', ''),
(3,'extend', 'Bottom links', 'textarea','footer_links','text', '', 'settings_desc_footer_links', ''),
(4,'extend', 'Editor', 'select','editor','text', 'WysibbEditor', 'settings_desc_editor', '{"WysibbEditor":"Wysibb(BBCode)"}'),
(5,'extend', 'Avatar style', 'select','avatar_style','text', 'img-rounded', '', '{"img-circle":"Circle","img-rounded":"Rounded"}'),
(1,'cache', 'Enable cache', 'select','cache_enabled','integer', '0', 'settings_desc_cache_enabled', '["0(Off)","1(On)"]'),
(2,'cache', 'Cache time(mins)', 'text','cache_time','integer', '10', 'default:10 mins', ''),
(3,'cache', 'Cache type', 'select','cache_type', 'text', 'file', 'defalut:file', '{"file":"file","apc":"apc","memcache":"memcache","memcached":"memcached"}'),
(4,'cache', 'Cache servers', 'textarea','cache_servers', 'text','', 'settings_desc_cache_servers', ''),
(1,'auth', 'Enable third-party login', 'select','auth_enabled', 'integer','0', '', '["0(Off)","1(On)"]'),
(2,'auth', 'Third-party setting', 'textarea','auth_setting', 'text','[]', '', ''),
(1,'other', 'Number of index topics', 'text', 'index_pagesize', 'integer', '20','default:20', ''),
(2,'other', 'Number of topics per page', 'text','list_pagesize', 'integer', '20','default:20', ''),
(3,'other', 'Number of comments per page', 'text','comment_pagesize', 'integer', '20','default:20', ''),
(4,'other', 'Number of tot topics', 'text','hot_topic_num', 'integer', '10','default:10', ''),
(5,'other', 'Number of hot nodes', 'text','hot_node_num', 'integer', '20','default:20', ''),
(6,'other', 'Edit timer(mins)', 'text','edit_space', 'integer', '30','settings_desc_edit_timer', ''),
(7,'other', 'Time interval of topic(secs)', 'text','topic_space', 'integer', '30','default:30', ''),
(8,'other', 'Time interval of comment(secs)', 'text','comment_space', 'integer', '20','default:20', ''),
(9,'other', 'Custom static url', 'text','alias_static','text', '', 'settings_desc_alias_static', ''),
(10,'other', 'Custom avatar url', 'text','alias_avatar','text', '', 'settings_desc_alias_avatar', ''),
(11,'other', 'Custom upload url', 'text','alias_upload','text', '', 'settings_desc_alias_upload', ''),
(1,'mailer', 'SMTP host', 'text','mailer_host', 'text', '','', ''),
(2,'mailer', 'SMTP port', 'text','mailer_port', 'integer', '','', ''),
(3,'mailer', 'SMTP encryption', 'text','mailer_encryption', 'text', '','settings_desc_mailer_encryption', ''),
(4,'mailer', 'SMTP username', 'text','mailer_username', 'text', '','settings_desc_mailer_username', ''),
(5,'mailer', 'SMTP password', 'password','mailer_password', 'text', '','settings_desc_mailer_password', ''),
(1,'upload', 'Avatar upload', 'select','upload_avatar','text', 'local', 'settings_desc_upload_avatar', '{"local":"Website storage","remote":"Cloud storage"}'),
(2,'upload', 'File upload', 'select','upload_file','text', 'disable', '', '{"disable":"Disable upload","local":"Website storage","remote":"Cloud storage"}'),
(3,'upload', 'File upload option<br />(days after register)', 'text','upload_file_regday','integer', '20', 'default:20', ''),
(3,'upload', 'File upload option<br />(number of topics)', 'text','upload_file_topicnum','integer', '20', 'default:20', ''),
(4,'upload', 'Cloud storage', 'select','upload_remote', 'text','', '', '[]');

DROP TABLE IF EXISTS `simple_favorite`;
CREATE TABLE `simple_favorite` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default 0,
  `source_id` mediumint(8) unsigned NOT NULL default 0,
  `target_id` mediumint(8) unsigned NOT NULL default 0,
  PRIMARY KEY  (`id`),
  UNIQUE KEY source_type_target(`source_id`, `type`, `target_id`),
  KEY type_target(`type`, `target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_auth;
CREATE TABLE simple_auth (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default 0,
  `source` varchar(20) NOT NULL,
  `source_id` varchar(100) NOT NULL,
  PRIMARY KEY id(id),
  UNIQUE KEY user_source(`user_id`, `source`),
  UNIQUE KEY source_source_id(`source`, `source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `simple_history`;
CREATE TABLE `simple_history` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default 0,
  `type` tinyint(1) unsigned NOT NULL default 0,
  `action` tinyint(1) unsigned NOT NULL,
  `action_time` int(10) unsigned NOT NULL,
  `target` int(10) unsigned NOT NULL default 0,
  `ext` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY user_type_id(`user_id`, `type`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_ad;
CREATE TABLE simple_ad (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `created_at` int(10) unsigned NOT NULL,
  `expires` date NOT NULL,
  `location` tinyint(1) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL default 0,
  `sortid` tinyint(1) unsigned NOT NULL default 50,
  `name` varchar(20) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY id(`id`),
  KEY adkey(`location`,`node_id`, `expires`, `sortid`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS simple_plugin;
CREATE TABLE simple_plugin (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `status` tinyint(1) unsigned NOT NULL default 0,
  `pid` varchar(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `author` varchar(40) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `config` text NOT NULL,
  `settings` text NOT NULL,
  `events` text NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY `pid`(`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `simple_plugin` (`id`, `status`, `pid`, `name`, `description`, `author`, `url`, `version`, `config`, `settings`, `events`) VALUES
(1, 0, 'WysibbEditor', 'Wysibb(BBcode)', 'Wysibb(BBcode)', 'SimpleForum', 'http://simpleforum.org', '1.0', '', '', '');
