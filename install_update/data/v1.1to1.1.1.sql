ALTER TABLE `simple_topic` ADD COLUMN `tags` char(60) NOT NULL default '' AFTER `title`;
ALTER TABLE `simple_tag` CHANGE COLUMN `name` `name` varchar(20) NOT NULL;
ALTER TABLE `simple_tag` ADD COLUMN `created_at` int(10) unsigned NOT NULL AFTER `id`;
ALTER TABLE `simple_tag` ADD COLUMN `updated_at` int(10) unsigned NOT NULL AFTER `created_at`;
update `simple_topic` topic set tags = (SELECT GROUP_CONCAT(tag.name) FROM `simple_tag` tag, `simple_tag_topic` tt where tag.id=tt.tag_id and tt.topic_id = topic.id) where topic.tags = '';
update `simple_tag` tag set created_at = (SELECT min(topic.created_at) FROM `simple_topic` topic, `simple_tag_topic` tt where tag.id=tt.tag_id and tt.topic_id = topic.id);
update `simple_tag` tag set updated_at = (SELECT max(topic.created_at) FROM `simple_topic` topic, `simple_tag_topic` tt where tag.id=tt.tag_id and tt.topic_id = topic.id);

CREATE INDEX updated ON simple_tag(`updated_at`, `id`);
CREATE INDEX topic_count ON simple_tag(`topic_count`, `id`);
OPTIMIZE TABLE simple_topic;
OPTIMIZE TABLE simple_tag;
