###############################################################
# Database upgrade SQL from ATutor 1.4.1 to ATutor 1.4.2
###############################################################

# Table structure for table `course_cats`
ALTER TABLE `course_cats` ADD `theme` VARCHAR( 30 ) NOT NULL ;
