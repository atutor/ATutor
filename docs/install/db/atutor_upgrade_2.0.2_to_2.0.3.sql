# Explicitly set engine option on the tables that this option was initially missed out
ALTER TABLE `feeds` ENGINE = MyISAM;
ALTER TABLE `patches` ENGINE = MyISAM;
ALTER TABLE `patches_files` ENGINE = MyISAM;
ALTER TABLE `patches_files_actions` ENGINE = MyISAM;
ALTER TABLE `myown_patches` ENGINE = MyISAM;
ALTER TABLE `myown_patches_dependent` ENGINE = MyISAM;
ALTER TABLE `myown_patches_files` ENGINE = MyISAM;
ALTER TABLE `auto_enroll` ENGINE = MyISAM;
ALTER TABLE `auto_enroll_courses` ENGINE = MyISAM;
ALTER TABLE `grade_scales` ENGINE = MyISAM;
ALTER TABLE `grade_scales_detail` ENGINE = MyISAM;
ALTER TABLE `gradebook_tests` ENGINE = MyISAM;
ALTER TABLE `gradebook_detail` ENGINE = MyISAM;
ALTER TABLE `fha_student_tools` ENGINE = MyISAM;

# --------------------------------------------------------
# Replace (TEXT NOT NULL) with (TEXT)
ALTER TABLE `social_member_contact` MODIFY `con_address` TEXT;

ALTER TABLE `social_member_representation` MODIFY `rep_address` TEXT;

ALTER TABLE `oauth_client_servers` MODIFY `consumer_key` TEXT, MODIFY `consumer_secret` TEXT;

ALTER TABLE `oauth_client_tokens` MODIFY `token_secret` TEXT;

ALTER TABLE `pa_albums` MODIFY `description` TEXT;

ALTER TABLE `pa_album_comments` MODIFY `comment` TEXT;

ALTER TABLE `pa_photo_comments` MODIFY `comment` TEXT;
