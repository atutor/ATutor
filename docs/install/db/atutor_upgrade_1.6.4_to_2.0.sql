

UPDATE `modules` SET `dir_name` = '_core/imscp' WHERE `modules`.`dir_name` = '_core/content_packaging' LIMIT 1 ;

INSERT INTO `modules` VALUES ('_core/modules', 2, 0, 8192, 0, 0);

# --------------------------------------------------------
# Adding feature of oauth client
# Table structure for table `oauth_client_servers`
# since 1.6.5

CREATE TABLE `oauth_client_servers` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_server` VARCHAR(255) NOT NULL default '',
  `consumer_key` TEXT NOT NULL ,
  `consumer_secret` TEXT NOT NULL ,
  `expire_threshold` INT NOT NULL default 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id` ),
  UNIQUE INDEX idx_consumer ( `oauth_server` )
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `oauth_client_tokens`
# since 1.6.5

CREATE TABLE `oauth_client_tokens` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL,
  `token` VARCHAR(50) NOT NULL default '',
  `token_type` VARCHAR(50) NOT NULL NOT NULL default '',
  `token_secret` TEXT NOT NULL,
  `member_id` mediumint(8) unsigned NOT NULL ,
  `assign_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id`, `token` )
) TYPE=MyISAM;

# END Adding feature of oauth client

# point tile.php to new location
UPDATE `courses` SET main_links=replace(main_links, 'tile.php', 'mods/_standard/tile_search/tile.php'), home_links=replace(home_links, 'tile.php', 'mods/_standard/tile_search/tile.php');
