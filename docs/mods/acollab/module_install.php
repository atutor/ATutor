<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>