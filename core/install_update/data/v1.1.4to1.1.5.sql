ALTER TABLE `simple_user` ADD COLUMN `score` mediumint(8) unsigned NOT NULL default 0 AFTER `role`;
ALTER TABLE `simple_user` ADD COLUMN `comment` char(20) NOT NULL default '';
update `simple_user` set `score`=1000;
OPTIMIZE TABLE `simple_user`;

ALTER TABLE `simple_token` ADD COLUMN `created_at` int(10) unsigned NOT NULL AFTER `id`;
ALTER TABLE `simple_token` ADD COLUMN `updated_at` int(10) unsigned NOT NULL AFTER `created_at`;
ALTER TABLE `simple_token` CHANGE COLUMN `token` `token` VARCHAR(50) COLLATE utf8_bin;
OPTIMIZE TABLE `simple_token`;

ALTER TABLE `simple_notice` CHANGE COLUMN `topic_id` `topic_id`  mediumint(8) unsigned NOT NULL default 0;
ALTER TABLE `simple_notice` ADD COLUMN `msg` varchar(255) NOT NULL default '';
OPTIMIZE TABLE `simple_notice`;

INSERT INTO `simple_setting`(`sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `description`, `option`) VALUES
(13,'manage', '会员组', 'textarea','groups', 'text', '1500 普通会员\n3000 铜牌会员\n5000 银牌会员\n8000 金牌会员\n15000 铂金会员\n30000 钻石会员','一行一个组，格式为:最大积分 用户组名"', '');

update `simple_setting` set `option`='["0(开启注册)","1(关闭注册)","2(只开放邀请码注册)"]' where `key` = 'close_register';
update `simple_setting` set `value`='sf-mobile' where `key` = 'theme_mobile';
update `simple_setting` set `type`='password' where `key` = 'mailer_password';
OPTIMIZE TABLE `simple_setting`;

ALTER TABLE `simple_history` ADD COLUMN `type` tinyint(1) unsigned NOT NULL default 0 AFTER `user_id`;
DROP INDEX user_id ON `simple_history`;
CREATE INDEX user_type_id ON `simple_history`(`user_id`, `type`, `id`);
insert into `simple_history`(`user_id`, `type`, `action`, `action_time`, `ext`) select id, 1, 30, UNIX_TIMESTAMP(now()), '{"score":"1000","cost":"1000"}' from `simple_user` order by id asc;
OPTIMIZE TABLE `simple_history`;
