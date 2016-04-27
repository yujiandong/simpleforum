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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `simple_node` ADD COLUMN `invisible` tinyint(1) unsigned NOT NULL AFTER `favorite_count`;
CREATE INDEX invisible ON simple_node(`invisible`, `id`);
OPTIMIZE TABLE simple_node;

DROP INDEX alllist ON simple_topic;
CREATE INDEX alllist ON simple_topic(`node_id`, `alltop`, `replied_at`, `id`);
DROP INDEX hottopics ON simple_topic;
CREATE INDEX hottopics ON simple_topic(`node_id`, `created_at`, `comment_count`, `replied_at`);
CREATE INDEX allcount ON simple_topic(`node_id`,`id`);
OPTIMIZE TABLE simple_topic;

update `simple_setting` set `option`='{"file":"file","apc":"apc","memcache":"memcache","memcached":"memcached"}' where `key` = 'cache_type';
