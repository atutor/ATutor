###############################################################
# Database upgrade SQL from ATutor 1.4.2 to ATutor 1.4.3
###############################################################

CREATE TABLE at_themes (
  title varchar(20) NOT NULL default '',
  version varchar(10) NOT NULL default '',
  dir_name varchar(50) NOT NULL default '',
  last_updated date NOT NULL default '0000-00-00',
  extra_info varchar(40) NOT NULL default '',
  status int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (title)
) TYPE=MyISAM;