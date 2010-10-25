<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

// config
$_course_privilege = FALSE;
$_admin_privilege = AT_ADMIN_PRIV_ADMIN;
$_cron_interval = 10;


// directory
$directory = AT_CONTENT_DIR.'adobe_connect';
if (!is_dir($directory) && !@mkdir($directory)) {
    $msg->addError(array('MODULE_INSTALL', '<li>'.$directory.' does not exists'));
//} else if (!is_writable($directory) && @chmod($directory, 0666)) {
} else if (!is_writable($directory) && @chmod($directory, 0777)) {
    $msg->addError(array('MODULE_INSTALL', '<li>'.$directory.' is not writable'));
}


// db
$sqlfilepath = dirname(__FILE__).'/module.sql';
if (!$msg->containsErrors() && file_exists($sqlfilepath)) {

    require_once(AT_INCLUDE_PATH.'classes/sqlutility.class.php');
    $sqlUtility = & new SqlUtility();
    $sqlUtility->queryFromFile($sqlfilepath, TABLE_PREFIX);
}


?>
