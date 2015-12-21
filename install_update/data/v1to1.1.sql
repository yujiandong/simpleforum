DROP TABLE IF EXISTS simple_tag;
CREATE TABLE simple_tag (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `topic_count` smallint(6) unsigned NOT NULL default 0,
  PRIMARY KEY id(`id`),
  UNIQUE KEY name(`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS simple_tag_topic;
CREATE TABLE simple_tag_topic (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY id(`id`),
  UNIQUE KEY tag_topic(`tag_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO simple_setting(`sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `description`, `option`) VALUES
(7,'other', 'static目录自定义网址', 'text','alias_static','text', '', '自定义web/static目录的网址，可用于CDN。例：http://static.simpleforum.org', ''),
(8,'other', '头像目录自定义网址', 'text','alias_avatar','text', '', '自定义web/avatar目录的网址，可用于CDN。例：http://avatar.simpleforum.org', ''),
(9,'other', '附件目录自定义网址', 'text','alias_upload','text', '', '自定义web/upload目录的网址，可用于CDN。例：http://upload.simpleforum.org', ''),
(1,'upload', '头像上传', 'select','upload_avatar','text', 'local', '默认:上传到网站所在空间', '{"local":"上传到网站所在空间","remote":"上传到第三方空间"}'),
(2,'upload', '附件上传', 'select','upload_file','text', 'disable', '默认:网站空间', '{"disable":"关闭上传","local":"上传到网站所在空间","remote":"上传到第三方空间"}'),
(3,'upload', '附件上传条件(注册时间)', 'text','upload_file_regday','integer', '30', '默认：30天', ''),
(3,'upload', '附件上传条件(主题数)', 'text','upload_file_topicnum','integer', '20', '默认：20', ''),
(4,'upload', '第三方空间', 'select','upload_remote', 'text','', '', '{"qiniu":"七牛云","upyun":"又拍云"}'),
(5,'upload', '第三方空间信息', 'text','upload_remote_info', 'text','', '逗号分隔。又拍云：空间名,操作员,密码；<br />七牛：空间名,access key,secret key', ''),
(6,'upload', '第三方空间URL', 'text','upload_remote_url', 'text','', '', '');

ALTER TABLE `simple_topic` ADD COLUMN `alltop` tinyint(1) unsigned NOT NULL default 0 AFTER `reply_id`;
ALTER TABLE `simple_topic` ADD COLUMN `top` tinyint(1) unsigned NOT NULL default 0 AFTER `alltop`;

DROP INDEX replied_id ON simple_topic;
DROP INDEX node_replied_id ON simple_topic;
CREATE INDEX alllist ON simple_topic(`alltop`, `replied_at`, `id`);
CREATE INDEX nodelist ON simple_topic(`node_id`, `top`, `replied_at`, `id`);
OPTIMIZE TABLE simple_topic;
