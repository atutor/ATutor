###############################################################
# Database upgrade SQL from ATutor 1.5.3.2 to ATutor 1.5.3.3
###############################################################

# convert DATETIME fields to TIMESTAMP
ALTER TABLE `admins` CHANGE `last_login` `last_login` TIMESTAMP NOT NULL;

