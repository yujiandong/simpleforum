ALTER TABLE `simple_topic` ADD COLUMN `access_auth` tinyint(1) unsigned NOT NULL default 0 AFTER `closed`;
ALTER TABLE `simple_topic` ADD COLUMN `good` smallint(6) unsigned NOT NULL default 0 AFTER `favorite_count`;
OPTIMIZE TABLE `simple_topic`;

ALTER TABLE `simple_comment` ADD COLUMN `good` smallint(6) unsigned NOT NULL default 0 AFTER `invisible`;
OPTIMIZE TABLE `simple_comment`;

DROP TABLE IF EXISTS simple_plugin;
CREATE TABLE simple_plugin (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `status` tinyint(1) unsigned NOT NULL default 0,
  `pid` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `author` varchar(20) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `config` text NOT NULL,
  `settings` text NOT NULL,
  `events` text NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY `pid`(`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `simple_plugin` (`id`, `status`, `pid`, `name`, `description`, `author`, `url`, `version`, `config`, `settings`, `events`) VALUES
(1, 0, 'WysibbEditor', 'Wysibb编辑器(BBcode)', 'Wysibb编辑器(BBcode)', 'SimpleForum', 'http://simpleforum.org', '1.0', '', '', ''),
(2, 0, 'SmdEditor', 'Simple Markdown编辑器', 'Simple Markdown编辑器', 'SimpleForum', 'http://simpleforum.org', '1.0', '', '', '');

INSERT INTO simple_setting(`sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `description`, `option`) VALUES
(6,'extend', '会员头像', 'select','avatar_style','text', 'img-rounded', '', '{"img-circle":"圆形","img-rounded":"圆角方形"}'),
(2,'auth', '第三方登录设定', 'textarea','auth_setting', 'text','[]', '', '');

update simple_setting set `value`='WysibbEditor', `option`='{"WysibbEditor":"Wysibb编辑器(BBCode)","SmdEditor":"SimpleMarkdown编辑器"}'  where `key`='editor' and `value`='wysibb';
update simple_setting set `value`='SmdEditor', `option`='{"WysibbEditor":"Wysibb编辑器(BBCode)","SmdEditor":"SimpleMarkdown编辑器"}'  where `key`='editor' and `value`='smd';
update simple_setting set `value`='', `option`='[]'  where `key`='upload_remote';
delete from simple_setting where `block` in ('auth.qq', 'auth.weibo');
delete from simple_setting where `key` in ('upload_remote_info', 'upload_remote_url');
