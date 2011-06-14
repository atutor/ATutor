<?php
// MediaWiki database connection setup for atutor_mediawiki integration module
require('mw_config.php');
global $db_mw;

if (AT_INCLUDE_PATH !== 'NULL') {
	$db_mw= @mysql_connect(MW_DB_HOST.':'.MW_DB_PORT, MW_DB_USER, MW_DB_PWD);	

	if (!$db_mw) {
		/* AT_ERROR_NO_DB_CONNECT */
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db. Set database information in the module\'s mw_config.php file.', E_USER_ERROR);
		exit;
	}
	if (!@mysql_select_db(MW_DB_NAME, $db_mw)) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#DB connection established, but database "'.MW_DB_NAME.'" cannot be selected.',
						E_USER_ERROR);
		exit;
	}

}
?>