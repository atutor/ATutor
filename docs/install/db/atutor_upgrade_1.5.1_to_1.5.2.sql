###############################################################
# Database upgrade SQL from ATutor 1.5 to ATutor 1.5.1
###############################################################

# modules

CREATE TABLE `modules` (  
`dir_name` VARCHAR( 50 ) NOT NULL ,  
`status` TINYINT NOT NULL ,  
`privilege` MEDIUMINT UNSIGNED NOT NULL ,  
PRIMARY KEY ( `dir_name` )  
);
