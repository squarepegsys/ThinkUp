ALTER TABLE  tu_favorites CHANGE  post_id  post_id VARCHAR( 50 ) NOT NULL
COMMENT  'Post ID on a given network.';

ALTER TABLE  tu_hashtags_posts CHANGE  post_id  post_id VARCHAR( 50 ) NOT NULL
COMMENT  'Post ID on a given network.';

ALTER TABLE  tu_instances CHANGE  last_post_id  last_post_id VARCHAR( 50 ) NOT NULL
COMMENT  'Last network post ID fetched for this instance.';

ALTER TABLE  tu_links CHANGE  post_id  post_id VARCHAR( 50 ) NOT NULL
COMMENT  'ID of the post which this link appeared on a given network.';

ALTER TABLE  tu_mentions_posts CHANGE  post_id  post_id VARCHAR( 50 ) NOT NULL
COMMENT  'Post ID on a given network.';

ALTER TABLE  tu_places_posts CHANGE  post_id  post_id VARCHAR( 50 ) NOT NULL
COMMENT  'Post ID on a given network.';

