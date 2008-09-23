/* Upgrade SQL for 1.6.1 to 1.6.2 */



/* Setup Table for Access4All */
CREATE TABLE `primary_resources` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `resource` text NOT NULL,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`primary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `primary_resources_types` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`primary_resource_id`,`type_id`)
) TYPE = MYISAM;

CREATE TABLE `resource_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` text NOT NULL,
  PRIMARY KEY  (`type_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `secondary_resource` text NOT NULL,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`secondary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources_types` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`secondary_resource_id`,`type_id`)
) TYPE = MYISAM;

INSERT INTO `resource_types` VALUES
(1, 'auditory'),
(2, 'sign_language'),
(3, 'textual'),
(4, 'visual');

INSERT INTO `config` (`name`, `value`) VALUES('encyclopedia', 'http://www.wikipedia.org');
INSERT INTO `config` (`name`, `value`) VALUES('dictionary', 'http://dictionary.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('thesaurus', 'http://thesaurus.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('atlas', 'http://maps.google.ca/');
INSERT INTO `config` (`name`, `value`) VALUES('calculator', 'http://www.calculateforfree.com/');
INSERT INTO `config` (`name`, `value`) VALUES('note_taking', 'http://www.aypwip.org/webnote/');
INSERT INTO `config` (`name`, `value`) VALUES('abacas', 'http://www.mandarintools.com/abacus.html');

/* End Access4All setup */


# Content Test Extension
# @author	Harris
# @date		Sep 08, 08 */
CREATE TABLE `content_tests_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `test_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `test_id`)
)
TYPE = MyISAM;

# Customized test messages associated with the content page
ALTER TABLE `atutor_161`.`at_content` ADD COLUMN `test_message` TEXT NOT NULL AFTER `use_customized_head`;
