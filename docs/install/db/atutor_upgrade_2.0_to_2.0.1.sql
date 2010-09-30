# set default mobile status
UPDATE `themes` SET `status` = 3 WHERE `dir_name` = 'mobile';

# Extent groups.modules to 256 chars from 100 chars
ALTER TABLE `groups` MODIFY `modules` varchar(255) NOT NULL default '';

# Add new language for patcher
INSERT INTO `language_text` (`language_code`, `variable`, `term`, `text`, `revised_date`, `context`) VALUES ('en', '_template', 'path_not_allowed', 'Cannot proceed! The listed file path(s) is not allowed:<br />', now(), 'patcher');
