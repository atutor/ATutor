<?php
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (!isset($_config['cron_key']) || !$_config['cron_key']) {
	$_config['cron_key'] = strtoupper(substr(str_replace(array('l','o','0','i'), array(), md5(time())), 0, 6));
	
	$sql = "REPLACE INTO %sconfig VALUES ('cron_key', '%s')";
	queryDB($sql, array(TABLE_PREFIX, $_config['cron_key']));
	
}

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('admin/system_preferences/cron_config.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>