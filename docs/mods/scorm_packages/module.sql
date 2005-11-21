
# SCORM language

INSERT INTO `language_text` VALUES ('en', '_template','scorm_browse','Browse','2005-11-21 11:51:04',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_credit','Credit','2005-11-21 11:50:16',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_credit_mode','Credit Mode:','2005-11-21 11:45:49',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_credit_mode_info','This is the answer given to the learning objects when they ask whether the learner is taking this particular package for credit.','2005-11-21 11:45:20',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_lesson_mode','Lesson Mode:','2005-11-21 11:46:32',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_lesson_mode_info','This is the answer given to the learning objects when they ask whether the learner is just browsing or not.','2005-11-21 11:46:16',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_normal','Normal','2005-11-21 11:51:12',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_no_credit','No Credit','2005-11-21 11:50:32',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_packages','SCORM Packages','2005-09-22 11:39:12',''); 
INSERT INTO `language_text` VALUES ('en', '_template','scorm_sco_is_running','Running','2005-05-17 12:04:55',''); 
INSERT INTO `language_text` VALUES ('en', '_template','package_scorm_1_2_rte_loading','Loading SCORM-1.2 RTE<br />Please be patient ...','2005-05-17 12:09:47','');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_SCORM_ITEM_HREF_MISSING','The file you tried to import is not a scorm-1.2 package. The required href attribute is missing for some resource(s).','2005-05-17 12:04:27',''); 
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_SCORM_ITEM_SCORMTYPE_MISSING','The file you tried to import is not a scorm-1.2 package. The required adlcp:scormtype is missing for some resource(s).','2005-05-17 12:02:47',''); 
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_SCORM_SETTINGS_SAVED','The package settings have been saved.','2005-05-17 12:04:05','');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_WARNING_SCORM_ITEM_CLUSTER_HAS_OBJECT','Some node(s) has content attached to it. The next version of the SCORM specification does not allow nodes to have attached content.','2005-05-17 12:04:39','');


# ----------------
# SCORM RTE tables

CREATE TABLE `packages` (
      `package_id` mediumint(8) unsigned NOT NULL auto_increment,
      `source`     varchar(255) NOT NULL,
      `time`       datetime NOT NULL,
      `course_id`  mediumint(8) unsigned NOT NULL,
      `ptype`      varchar(63) NOT NULL,
      PRIMARY KEY (package_id)
) TYPE=MyISAM;

CREATE TABLE `scorm_1_2_org` (
      `org_id`     mediumint(8) unsigned NOT NULL auto_increment,
      `package_id` mediumint(8) unsigned NOT NULL,

      `title`         varchar(255) NOT NULL,
      `credit`        varchar(15)  not null default 'no-credit',
      `lesson_mode`   varchar(15)  not null default 'browse',

      PRIMARY KEY (org_id),
      KEY         (package_id)
) TYPE=MyISAM;

CREATE TABLE `scorm_1_2_item` (
      `item_id`    mediumint(8) unsigned NOT NULL auto_increment,
      `org_id`     mediumint(8) unsigned NOT NULL,
      `idx`             varchar(15)  NOT NULL,
      `title`           varchar(255),
      `href`            varchar(255),
      `scormtype`       varchar(15),
      `prerequisites`   varchar(255),
      `maxtimeallowed`  varchar(255),
      `timelimitaction` varchar(255),
      `datafromlms`     varchar(255),
      `masteryscore`    mediumint(8),

      PRIMARY KEY (item_id),
      KEY (org_id)
)TYPE=MyISAM;


CREATE TABLE `cmi` (
      `cmi_id`        mediumint(8) unsigned NOT NULL auto_increment,
      `item_id`       mediumint(8) unsigned NOT NULL,
      `member_id`     mediumint unsigned NOT NULL ,
      `lvalue`        varchar(63) NOT NULL,
      `rvalue`        blob,
       PRIMARY KEY (cmi_id),
      UNIQUE KEY (item_id, member_id,lvalue)
)TYPE=MyISAM;
