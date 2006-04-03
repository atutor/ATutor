<?php
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

// authenticate $_config['cron_key']
if (!isset($_config['cron_key']) || empty($_config['cron_key']) || ($_config['cron_key'] != $_GET['k'])) {
	// not authenticated
	exit;
}

$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('last_cron', '".time()."')";
mysql_query($sql, $db);

$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, AT_MODULE_TYPE_CORE + AT_MODULE_TYPE_STANDARD + AT_MODULE_TYPE_EXTRA);
$keys = array_keys($module_list);

foreach($keys as $dir_name) {
	$module =& $module_list[$dir_name];
	
	if (!$module->getCronInterval()) {
		continue;
	}

	$module->runCron();
}

//	run the mail queue last
if ($_config['enable_mail_queue']) {
	require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
	$mail = new ATutorMailer;
	$mail->SendQueue();
}
?>