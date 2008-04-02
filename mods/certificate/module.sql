# sql file for certificate module

CREATE TABLE `certificate` (
   `certificate_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `test_id` mediumint(8) unsigned NOT NULL ,
   `passscore` mediumint(9) ,
   `passpercent` mediumint(9) ,
   `organization` varchar(255),
   `enable_download` tinyint(4) unsigned ,
   `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
   PRIMARY KEY ( `certificate_id` )
);

CREATE TABLE `certificate_text` (
   `certificate_text_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `certificate_id` MEDIUMINT UNSIGNED NOT NULL,
   `field_name` VARCHAR(100) NOT NULL ,
   `field_value` VARCHAR(255) NOT NULL ,
   PRIMARY KEY ( `certificate_text_id` )
);

INSERT INTO `language_text` VALUES 
('en', '_module','certificate','Certificate',NOW(),''),
('en', '_template', 'create_certificate', 'Create Certificate', now(), ''),
('en', '_template', 'delete_certificate', 'Delete Certificate', now(), ''),
('en', '_template', 'edit_certificate', 'Edit Certificate', now(), ''),
('en', '_template', 'certificate_id', 'Certificate ID', now(), ''),
('en', '_template', 'test_title', 'Test Title', now(), ''),
('en', '_template', 'choose_test', 'Choose Test', now(), ''),
('en', '_template', 'certificate_template', 'Certificate Template', now(), ''),
('en', '_template', 'organization_name', 'Name of Organization', now(), ''),
('en', '_template', 'certificate_text', 'Certificate Text', now(), ''),
('en', '_template', 'field_name', 'Field Name', now(), ''),
('en', '_template', 'field_value', 'Field Value', now(), ''),
('en', '_template', 'enable_download_certificate', 'Enable Download Certificate', now(), ''),
('en', '_template', 'issue_certificate', 'Issue certificate if test is passed', now(), ''),
('en', '_template', 'define_pass_score', 'Please define pass score <a href="%s" target="_blank">here</a> and save again', now(), ''),
('en', '_template', 'require_acrobat', 'In order to %s certificate, you will require Adobe Acrobat Reader.<a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank"><img src="mods/certificate/get_adobe_reader.gif" style="border-width: 0px;"></a>', now(), ''),
('en', '_msgs', 'AT_CONFIRM_DELETE_CERTIFICATE', 'Are you sure you want to <strong>delete</strong> certificate for <strong>%s</strong>', now(), ''),
('en', '_template', 'certificate_tokens', '<strong>Certificate Tokens</strong><br>[TNAME]: Test Title<br>
[FNAME]: First Name<br>
[LNAME]: Last Name<br>
[CNAME]: Course Name<br>
[USERMAIL]: Email Address<br>
[USCORE]: Users test score<br>
[OSCORE]: Out of test score<br>
[PSCORE]: Test score in percentage<br>
[SYSDATE]: Date time of last attempt at passed test<br>
[USERID]: ATutor Member ID', now(), '')
;

INSERT INTO `language_text` VALUES 
('en', '_template', 'pass_score', 'Pass Score', now(), ''),
('en', '_template', 'percentage_score', 'percentage score', now(), ''),
('en', '_template', 'points_score', 'points score', now(), '')
;
