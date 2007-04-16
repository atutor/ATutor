<?php

$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<div id="helloworld">
	This is a public page from the Hello World module, that does not require a login session to view.  :)
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>