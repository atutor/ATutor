<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/basiclti/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<div id="helloworld">
	This is a page of the Hello World module that requires a login session, but might contain a tool that is not a course tool :)
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
