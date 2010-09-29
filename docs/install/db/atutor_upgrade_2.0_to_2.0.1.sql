# set default mobile status
UPDATE `themes` SET `status` = 3 WHERE `dir_name` = 'mobile';

# Extent groups.modules to 256 chars from 100 chars
ALTER TABLE `groups` MODIFY `modules` varchar(255) NOT NULL default '';
