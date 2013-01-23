# -------------- Remedial Content -----------------
ALTER TABLE `tests` ADD remedial_content tinyint(1) unsigned DEFAULT '0';
ALTER TABLE `tests_questions` ADD remedial_content text;

