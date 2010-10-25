<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

// directory
$directory = AT_CONTENT_DIR.'adobe_connect';
if (is_dir($directory) && is_writable($directory)) {

    require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

    if (!clr_dir($directory)) {
        $msg->addError(array('MODULE_UNINSTALL', ' '.$directory.' can\'t be removed'));
    }

}

// db
$sqlfilepath = dirname(__FILE__).'/module.sql';
if (!$msg->containsErrors() && file_exists($sqlfilepath)) {

    require_once(AT_INCLUDE_PATH.'classes/sqlutility.class.php');
    $sqlUtility = & new SqlUtility();
    $sqlUtility->revertQueryFromFile($sqlfilepath, TABLE_PREFIX);
}

?>
