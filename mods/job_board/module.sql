CREATE TABLE `jb_postings` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,  
  `employer_id` INTEGER UNSIGNED NOT NULL,
  `categories` INTEGER UNSIGNED NOT NULL,
  `description` TEXT,
  `is_public` TINYINT(1) UNSIGNED NOT NULL,
  `closing_date` TIMESTAMP NOT NULL,
  `created_date` TIMESTAMP NOT NULL,
  `revised_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_categories` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `jb_employers` (                                                                                                                                                                                                                                           
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `company` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `last_login` TIMESTAMP NOT NULL,
  `requested_date` TIMESTAMP NOT NULL,
  `approval_state` TINYINT(1) UNSIGNED NOT NULL,
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

