<?php
require(dirname(__FILE__) .'/common/vitals.inc.php');

if (!isset($_SESSION['handbook_admin']) || !$_SESSION['handbook_admin'] || !isset($_GET['id'])) {
	exit;
}

function my_add_null_slashes( $string ) {
    return ( $string );
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes = 'my_add_null_slashes';
} else {
	$addslashes = 'mysql_real_escape_string';
}


$_GET['id'] = intval($_GET['id']);
$_GET['p'] = $addslashes($_GET['p']);

$config_location = '../include/config.inc.php';
if (is_file($config_location) && is_readable($config_location)) {
	require($config_location);
	$db = mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $db);

	// check atutor config table to see if handbook notes is enabled.
	$sql    = "SELECT value FROM ".TABLE_PREFIX."config WHERE name='user_notes'";
	$result = @mysql_query($sql, $db);
	if (($row = mysql_fetch_assoc($result)) && $row['value']) {
		define('AT_HANDBOOK_ENABLE', true);
		$enable_user_notes = true;
	}
	define('AT_HANDBOOK_DB_TABLE_PREFIX', TABLE_PREFIX);

	define('AT_HANDBOOK_ENABLE', true);
}
if (!defined('AT_HANDBOOK_ENABLE')) {
	// use local config file
	require('./config.inc.php');
}

if (!$db && defined('AT_HANDBOOK_ENABLE') && AT_HANDBOOK_ENABLE) {
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
	header('Location: '.$section. '/' . $_GET['p']);
} else {
	header('Location: index_list.php');
}
exit;

?>