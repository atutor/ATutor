# sql file for mahara module

CREATE TABLE `mahara` (
  `at_login` varchar(20) NOT NULL default '',
  `username` varchar(30) character set latin1 collate latin1_general_ci NOT NULL,
  `password` varchar(40) character set latin1 collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`at_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `config` VALUES ('mahara', '');

INSERT INTO `language_text` VALUES ('en', '_module','mahara','ePortfolio',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mahara_location','Enter the full path to your Mahara installation:<br /><div style="font-style: italic;">Example on Windows: C:&#092;webroot&#092;mahara&#092;</div><div style="font-style: italic;">Example on Linux: &#047;usr&#047;local&#047;apache&#047;htdocs&#047;mahara&#047;</div>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mahara_new_win','Open Mahara in a New Window',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mahara_opened','Mahara Opened in a New Window',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_MAHARA_MINURL_ADD_SAVED','Location of Mahara was successfully saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_MAHARA_MINURL_ADD_EMPTY','You must enter a path to the location of your Mahara installation.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_MAHARA_ERROR_INSTALL','Mahara not installed! Please create config.php from config-dist.php',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_MAHARA_ERROR_PATH','Unable to detect Mahara installation. Please check the path and make sure Mahara is set up correctly.',NOW(),'');
