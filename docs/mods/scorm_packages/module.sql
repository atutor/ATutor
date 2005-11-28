
# SCORM language : some of it is still in svn and should be moved in here.

INSERT INTO `language_text` VALUES ('en', '_module','scorm_browse','Browse',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_credit','Credit',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_credit_mode','Credit Mode:',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_credit_mode_info','This is the answer given to the learning objects when they ask whether the learner is taking this particular package for credit.',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_lesson_mode','Lesson Mode:',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_lesson_mode_info','This is the answer given to the learning objects when they ask whether the learner is just browsing or not.',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_normal','Normal',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_no_credit','No Credit',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_packages','SCORM Packages',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','scorm_sco_is_running','Running',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','package_scorm_1_2_rte_loading','Loading SCORM-1.2 RTE<br />Please be patient ...',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','packages_auto_advance_info','The first time a Learning Object signalizes that you have completed it, the RTE can start the next Learning Object. You might find this convenient if you want to complete all Learning Objects in a package in a sequential manner.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','packages_show_rte_communication','Show RTE Communication',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','packages_show_rte_communication_info','You can monitor the communication between Learning Objects and the RTE. You may want to switch on this option for a while just for informational purposes.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','AT_ERROR_SCORM_ITEM_HREF_MISSING','The file you tried to import is not a scorm-1.2 package. The required href attribute is missing for some resource(s).',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','AT_ERROR_SCORM_ITEM_SCORMTYPE_MISSING','The file you tried to import is not a scorm-1.2 package. The required adlcp:scormtype is missing for some resource(s).',NOW(),''); 
INSERT INTO `language_text` VALUES ('en', '_module','AT_FEEDBACK_SCORM_SETTINGS_SAVED','The package settings have been saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','AT_WARNING_SCORM_ITEM_CLUSTER_HAS_OBJECT','Some node(s) has content attached to it. The next version of the SCORM specification does not allow nodes to have attached content.',NOW(),'');


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
