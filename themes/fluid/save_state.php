<?php

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'/vitals.inc.php');

if ($_POST['left'] == 'side-menu') {
	$_SESSION['prefs']['PREF_MENU']='left';
} else {
	$_SESSION['prefs']['PREF_MENU']='right';
}

save_prefs();

?>