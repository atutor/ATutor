CREATE TABLE `jb_postings` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,  
  `employer_id` INTEGER UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `is_public` TINYINT(1) UNSIGNED NOT NULL,
  `closing_date` TIMESTAMP NOT NULL,
  `created_date` TIMESTAMP NOT NULL,
  `revised_date` TIMESTAMP NOT NULL,
  `approval_state` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_categories` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_posting_categories` (
  `posting_id` INTEGER UNSIGNED NOT NULL,
  `category_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`posting_id`, `category_id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_employers` (                                                                                                                                                                                                                                           
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `employer_name` VARCHAR(255) NOT NULL,
  `password` VARCHAR(40) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `company` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `website` VARCHAR(255) NOT NULL,
  `last_login` TIMESTAMP NOT NULL,
  `requested_date` TIMESTAMP NOT NULL,
  `approval_state` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_jobcart` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_id` INTEGER UNSIGNED NOT NULL,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`member_id`, `job_id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_posting_subscribes` (
  `member_id` INTEGER UNSIGNED,
  `job_id` INTEGER UNSIGNED,
  PRIMARY KEY (`member_id`, `job_id`)
)
ENGINE = MyISAM;


CREATE TABLE `jb_category_subscribes` (
  `member_id` INTEGER UNSIGNED,
  `category_id` INTEGER UNSIGNED,
  PRIMARY KEY (`member_id`, `category_id`)
)
ENGINE = MyISAM;


# Module Language
INSERT INTO `language_text` VALUES ('en', '_module','job_board','Job Board',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_search','Search',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_search_filter','Search Filters',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_search_filter_blub','Use the following filters to refine your search',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_subscribe','Subscribe to receive new job postings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_subscribe_blub','Check the categories below to get email notifications when there is a new job post.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_title','Job Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_preferences','Preferences',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer','Employer',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employers','Employers',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_categories','Categories',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_bookmarks','Bookmarks',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_archive','Archive',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_company_description','Company Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_post_description','Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_post_description_note','Description should include Job Overview, Responsibilities, Qualifications, Contact Information',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_posted_by','Job posted by:',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_closing_date','Closing Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_login','Employer sign-in',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_logout','Employer sign-out',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_registration','Employer Registration',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_any_categories','Any Categories',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_login','Employer Login',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_login_text','Login to manage or post new jobs.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_login_name','Employer Login Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_name','Employer Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_archive','Archive',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_home','Employer Home',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_edit_employer','Edit Employer',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_add_new_post','Add Job Posting',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_edit_post','Edit Post',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_view_post','View Job Posting',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_edit_profile','Edit Profile',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_is_public','Allow non-ATutor users to see this post',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_no_category','No category',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_remove_to_cart','Remove this bookmark',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_website','Website',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_confirmed','Confirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_unconfirmed','Unconfirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_approval_state','Approval State',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_approval_state_confirmed','Approved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_approval_state_unconfirmed','Disapproved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_required_posting_approval','New posting requires approval',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_admin_add_category_blub','To add new categories for the job posting, type the category name in the text field and click "Save".',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_admin_edit_categories_blub','The list below are the current categories within the system.  You may edit the item by clicking on them, or delete them by clicking on "Delete".',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_status_unconfirmed','Unconfirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_status_confirmed','Confirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_status_suspended','Suspended',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_email_confirmation_subject','Job Board Email Confirmation',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_email_confirmation_message','You have registered for a job board account on %1s.  Please finish the registration process by confirming your email address by using the following link: %2s.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_add_to_cart','Bookmark this post',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_remove_from_cart','Remove this bookmark',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_subscription_msg','Hi, \n\nA new job post titled %2s has been added to the ATutor Job Board %1s category.  The job post can be found at the following link, %3s.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_subscription_mail_subject','New Job Post in ATutor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_any_categories','Any categories',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_view_job_post','View a job post',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_approval_state_unconfirmed','Unconfirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_approval_state_confirmed','Confirmed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_employer_approval_state_suspended','Suspended',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_click_to_edit','Click to edit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_click_to_delete','Click to delete',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','jb_required_posting_approval','Does job post need to be approved before listing?',NOW(),'');

# Module Messages
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_POST_ADDED_SUCCESSFULLY','Job posted added successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_POST_PENDING','The job posting will appear when it has been approved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_POST_DELETED','Job Post deleted successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_POST_UPDATED_SUCCESS','Job Post updated successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_CATEGORY_ADDED_SUCCESSFULLY','Category added successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_CATEGORY_DELETED','Category deleted successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_JB_PROFILE_UPDATED','Profile was successfully updated.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_JB_MISSING_FIELDS','Email, username, company name, employer name cannot be empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_JB_EXISTING_INFO','Username or email has already been used.  Please choose another one. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_JB_CATEGORY_NAME_CANNOT_BE_EMPTY','Category name cannot be empty.',NOW(),'');

# Initial categories
INSERT INTO `jb_categories` (name) VALUES ('Auditing, Accounting');
INSERT INTO `jb_categories` (name) VALUES ('General Business, Administrative');
INSERT INTO `jb_categories` (name) VALUES ('HR, Business Service');
INSERT INTO `jb_categories` (name) VALUES ('Management');
INSERT INTO `jb_categories` (name) VALUES ('Engineering');
INSERT INTO `jb_categories` (name) VALUES ('Computer Programming');
INSERT INTO `jb_categories` (name) VALUES ('Visual Arts, Crafts and Design');
INSERT INTO `jb_categories` (name) VALUES ('General Science');
INSERT INTO `jb_categories` (name) VALUES ('General Health, Medical');
INSERT INTO `jb_categories` (name) VALUES ('Pharmacy and Nutrition');
INSERT INTO `jb_categories` (name) VALUES ('Teaching');
INSERT INTO `jb_categories` (name) VALUES ('Performing Arts');
INSERT INTO `jb_categories` (name) VALUES ('Psychology, Social Work, Counseling, Religion');
INSERT INTO `jb_categories` (name) VALUES ('Others');
