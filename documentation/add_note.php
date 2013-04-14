<?php
require(dirname(__FILE__) .'/common/vitals.inc.php');
function my_add_null_slashes( $string ) {
    return ( $string );
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes = 'my_add_null_slashes';
} else {
	$addslashes = 'mysql_real_escape_string';
}


if (isset($_POST['submit'])) {
	$_POST['email'] = str_replace('@', ' at ', $_POST['email']);
	$_POST['email'] = str_replace('.', ' dot ', $_POST['email']);
	$_POST['email'] = str_replace('<', '&lt;', $_POST['email']);

	$_POST['note']  = str_replace('<', '&lt;', $_POST['note']);

	$_POST['email']   = $addslashes($_POST['email']);
	$_POST['note']    = $addslashes($_POST['note']);
	$_POST['section'] = $addslashes($_POST['section']);
	$_POST['page']    = $addslashes($_POST['page']);

	// all this stuff has to go into some common vitals type file.

	$enable_user_notes = false;

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
		$sql = "INSERT INTO ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes VALUES (NULL, NOW(), '$_POST[section]', '$_POST[page]', 0, '$_POST[email]', '$_POST[note]')";
		mysql_query($sql, $db);
		header('Location: '.$_POST['section']. '/' . $_POST['page'].'?noted');
		exit;
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php get_text('add_note'); ?></title>
	<link rel="stylesheet" href="common/styles.css" type="text/css" />
<style type="text/css">
div.input-form div.row {
	margin-bottom: 10px;
}
</style>
</head>
<body>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="section" value="<?php echo $section; ?>" />
<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['p']); ?>" />

<div class="input-form">
	<div class="row">
		<p><?php get_text('add_note_blurb'); ?></p>
	</div>

	<div class="row">
		<label for="email"><?php get_text('email_name'); ?>:</label><br />
		<input type="text" name="email" value="" id="email" size="40" />
	</div>

	<div class="row">
		<label for="note"><?php get_text('your_note');?>:</label><br />
		<textarea name="note" id="note" cols="50" rows="20"></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php get_text('add_note'); ?>" />
	</div>

</form>

</body>
</html>