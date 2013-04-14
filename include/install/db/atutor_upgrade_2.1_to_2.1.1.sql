# -------------- Remedial Content -----------------
ALTER TABLE `tests` ADD remedial_content tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `tests_questions` ADD remedial_content text;

# -------------- Fixing language_pages issues -----
ALTER TABLE `language_pages` MODIFY term VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE `language_pages` MODIFY page VARCHAR(255) NOT NULL DEFAULT '';