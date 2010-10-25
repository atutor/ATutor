# sql file for hello world module



CREATE TABLE certify (
  certify_id mediumint(8) unsigned NOT NULL auto_increment,
  course_id mediumint(8) unsigned NOT NULL,
  title varchar(60) NOT NULL,
  description varchar(600) NOT NULL,
  PRIMARY KEY  (certify_id)
) ;


CREATE TABLE certify_tests (
  certify_id mediumint(8) unsigned NOT NULL,
  test_id mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (certify_id, test_id)
) ;


INSERT INTO `language_text` VALUES 
('en', '_module','Certify','Certify',NOW(),''),
('en', '_module','certify_certificates','Certificates',NOW(),''),
('en', '_module','certify_certificate','Add new certificate',NOW(),''),
('en', '_module','certify_file','Certificate template',NOW(),''),
('en', '_module','certify_tests','Tests in this certificate',NOW(),''),
('en', '_module','certify_title','Certificate name',NOW(),''),
('en', '_module','certify_description','Description',NOW(),''),
('en', '_module','certify_status','Status',NOW(),''),
('en', '_module','certify_download_certificate','Download certificate',NOW(),''),
('en', '_module','certify_add_new','Add new certificate',NOW(),''),
('en', '_module','certify_add_certificate','Add new certificate',NOW(),''),
('en', '_module','certify_student_status','Student status',NOW(),''), 
('en', '_module','certify_edit_tests','Edit tests',NOW(),''), 
('en', '_msgs', 'AT_INFOS_CERTIFY_NO_CERTIFICATES', 'There are no certificates yet, let us <a href="mods/certify/certify_add_new.php">add one</a>!', NOW(), '');