# sql file for mediawiki integration module


INSERT INTO `language_text` VALUES ('en', '_module','mediawiki','MediaWiki',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_text','A sample mediawiki text for detailed homepage.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_admin_login','Login to Administer MediaWiki',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_login_url','MediaWiki Base URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_login','MediaWiki Login',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_save','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_no_iframes','Your browser does not support iframes. Got to <a href="'.%s.'">MediaWiki Login</a>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','mediawiki_do_setup','Enter the URL to the MediaWiki based Web accessible directory (e.g, http://myserver.com/mediawiki/, including the trailing slash), to have MediaWiki appear here.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_MW_CONFIG_SAVED','MediaWiki configuration successfully saved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_MW_CONFIG_FAIL','MediaWiki configuration failed to save. ',NOW(),'');