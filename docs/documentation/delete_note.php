<?php
session_start();

if (!isset($_SESSION['handbook_admin']) || !$_SESSION['handbook_admin'] || !isset($_GET['id'])) {
	exit;
}

function my_add_null_slashes( $string ) {
    return ( $string );
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes = 'my_add_null_slashes';
} else {
	$addslashes = 'addslashes';
}


$_GET['id'] = intval($_GET['id']);

$config_location = '../include/config.inc.php';
if (is_file($config_location) && is_readable($config_location)) {
	require($config_location);
	if (defined('AT_ENABLE_HANDBOOK_NOTES') && AT_ENABLE_HANDBOOK_NOTES) {
		define('AT_HANDBOOK_DB_USER', DB_USER);

		define('AT_HANDBOOK_DB_PASSWORD', DB_PASSWORD);
		define('AT_HANDBOOK_DB_DATABASE', DB_NAME);

		define('AT_HANDBOOK_DB_PORT', DB_PORT);

		define('AT_HANDBOOK_DB_HOST', DB_HOST);

		define('AT_HANDBOOK_DB_TABLE_PREFIX', TABLE_PREFIX);

		define('AT_HANDBOOK_ENABLE', true);
	}
}
if (!defined('AT_HANDBOOK_ENABLE')) {
	// use local config file
	require('./config.inc.php');
}

if (defined('AT_HANDBOOK_ENABLE') && AT_HANDBOOK_ENABLE) {
	$db = @mysql_connect(AT_HANDBOOK_DB_HOST . ':' . AT_HANDBOOK_DB_PORT, AT_HANDBOOK_DB_USER, AT_HANDBOOK_DB_PASSWORD);
	if (@mysql_select_db(AT_HANDBOOK_DB_DATABASE, $db)) {
		$enable_user_notes = true;
	}
}

if ($enable_user_notes) {
	// insert into DB
	$sql = "DELETE FROM ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes WHERE note_id=$_GET[id]";
	mysql_query($sql, $db);
}

if (isset($_GET['p'])) {
	header('Location: '.key($_GET). '/' . $_GET['p']);
} else {
	header('Location: index_list.php');
}
exit;

?>