<?php
/* this is an optional configuration file. */
/* it is used only when the ATutor configuration cannot be found, or if it */
/* can be found but AT_ENABLE_HANDBOOK_NOTES is set to false. */
/* */
/* use the settings found here to enable/disable user notes. */
/* user notes require a valid database connection and a pre-existing */
/* database table. */
/* the structure/schema of the database table is defined at the bottom */
/* of this file. use it to create the table before enabling user notes. */

define('AT_HANDBOOK_DB_HOST', 'localhost');

define('AT_HANDBOOK_DB_PORT', '3306');

define('AT_HANDBOOK_DB_USER', '');

define('AT_HANDBOOK_DB_PASSWORD', '');

define('AT_HANDBOOK_DB_DATABASE', 'atutor');

define('AT_HANDBOOK_DB_TABLE_PREFIX', 'AT_');

define('AT_HANDBOOK_ADMIN_USERNAME', '');
define('AT_HANDBOOK_ADMIN_PASSWORD', '');

define('AT_HANDBOOK_ENABLE', true);

/*
# Note: you will have to add the table prefix!!

CREATE TABLE `handbook_notes` (
`note_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
`date` DATETIME NOT NULL ,
`section` VARCHAR( 15 ) NOT NULL ,
`page` VARCHAR( 50 ) NOT NULL ,
`approved` TINYINT NOT NULL,
`email` VARCHAR( 50 ) NOT NULL ,
`note` TEXT NOT NULL ,
PRIMARY KEY ( `note_id` )
);
*/
?>