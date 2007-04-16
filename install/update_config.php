<?php
exit;
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('contact_email', '".EMAIL."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('email_notification', '".(int) EMAIL_NOTIFY."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('allow_instructor_requests', '".(int) ALLOW_INSTRUCTOR_REQUESTS."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('auto_approve_instructors', '".(int) AUTO_APPROVE_INSTRUCTORS."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('max_file_size', '".(int) $MaxFileSize."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('max_course_size', '".(int) $MaxCourseSize."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('max_course_float', '".(int) $MaxCourseFloat."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('illegal_extentions', '".implode('|',$IllegalExtentions)."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('site_name', '".SITE_NAME."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('home_url', '".HOME_URL."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('default_language', '".DEFAULT_LANGUAGE."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('cache_dir', '".CACHE_DIR."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('enable_category_themes', '".(int) AT_ENABLE_CATEGORY_THEMES."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('course_backups', '".(int) AT_COURSE_BACKUPS."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('email_confirmation', '".(int) AT_EMAIL_CONFIRMATION."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('master_list', '".(int) AT_MASTER_LIST."')";
mysql_query($sql, $db);

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('enable_handbook_notes', '".(int) AT_ENABLE_HANDBOOK_NOTES."')";
mysql_query($sql, $db);

?>