###############################################################
# Database upgrade SQL from ATutor 1.5.4 to ATutor 1.5.5
###############################################################


## remove login field - #3032
ALTER TABLE `forums_threads` DROP `login`;
