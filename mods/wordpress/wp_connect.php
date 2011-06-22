<?php
// Wordpress database connection setup for atutor_wordpress integration module
require('wp_config.php');
global $db_wp;

if (AT_INCLUDE_PATH !== 'NULL') {
	$db_wp= @mysql_connect(WP_DB_HOST.':'.WP_DB_PORT, WP_DB_USER, WP_DB_PWD);	

	if (!$db_wp) {
		/* AT_ERROR_NO_DB_CONNECT */
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db. Set database information in the module\'s wp_config.php file.', E_USER_ERROR);
		exit;
	}
	if (!@mysql_select_db(WP_DB_NAME, $db_wp)) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#DB connection established, but database "'.WP_DB_NAME.'" cannot be selected.',
						E_USER_ERROR);
		exit;
	}

}
?>