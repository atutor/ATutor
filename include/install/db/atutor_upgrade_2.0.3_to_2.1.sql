# -------------- Multi-sites Support Starts -----------------
ALTER TABLE `themes` ADD customized tinyint(1) NOT NULL DEFAULT '1';

UPDATE `themes` SET customized = 0 WHERE dir_name in ('default', 'fluid', 'default_classic', 'blumin', 'greenmin', 'default15', 'default16', 'idi', 'mobile');

# -------------- Multi-sites Support Ends -----------------