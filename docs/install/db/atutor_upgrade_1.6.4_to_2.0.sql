

UPDATE `modules` SET `dir_name` = '_core/imscp' WHERE `modules`.`dir_name` = '_core/content_packaging' LIMIT 1 ;

INSERT INTO `modules` VALUES ('_core/modules', 2, 0, 8192, 0, 0);