ALTER TABLE `simple_node` ADD COLUMN `access_auth` tinyint(1) unsigned NOT NULL default 0 AFTER `favorite_count`;

INSERT INTO simple_setting(`sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `description`, `option`) VALUES
(9,'manage', '开启自动链接', 'select','autolink', 'integer', '0','自动给帖子内容中的网址加上链接', '["0(关闭)","1(开启)"]'),
(10,'manage', '自动链接排除列表', 'textarea','autolink_filter', 'text', '','可设置主域名或二级域名(如youku.com,v.youku.com等)，一行一个网址', ''),
(11,'manage', '模板', 'text','theme', 'text', '','模板名请用字母数字横杠下划线命名，模板放在"themes/模板名/"目录下', ''),
(12,'manage', '移动模板', 'text','theme_mobile', 'text', '','移动设备（手机/平板）专用模板，模板名请用字母数字横杠下划线命名，放在"themes/移动模板名/"目录下', ''),
(10,'other', '发表主题间隔(秒)', 'text','topic_space', 'integer', '30','默认30', ''),
(11,'other', '发表回复间隔(秒)', 'text','comment_space', 'integer', '20','默认20', '');
