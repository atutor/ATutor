<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/_standard/basiclti/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<div id="helloworld">
	Hello Student!! :)
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
