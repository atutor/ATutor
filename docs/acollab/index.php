<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tools';
	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	if(!$_SESSION['valid_user']){
		header('Location: ../logout.php');
		exit;
	}
	$_section[0][0] = _AT('tools');
$_GET['disable']=PREF_MAIN_MENU;
	require(AT_INCLUDE_PATH.'header.inc.php');

	require(AT_INCLUDE_PATH.'html/feedback.inc.php');

?>
	<h2><?php 
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" class="menuimageh2" width="42" height="40" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo _AT('tools');
		}
	?>

<?php

if (defined('AC_PATH') && AC_PATH) {
	echo '<br /><h3>ACollab '._AT('tools').'</h3><br />';

?>
<a href="<?php echo AC_PATH; ?>"><?php echo  _AT('acollab_own_window'); ?></a><br />
<iframe src ="
<?php
if(!$_SESSION['valid_user']){
	header('Location: ../logout.php');
	exit;
}
if($_GET['p'] == 'acollab/bounce.php'){
	header('Location: ../index.php');
	exit;
}else if($_GET['p'] != ''){
	$page = urldecode($_GET['p'].'?disable=PREF_MAIN_MENU');
}else {
	$page = 'index.php?disable=PREF_MAIN_MENU';
}
if(strstr($_SERVER['PHP_SELF'], 'sign_in')){

	header('Location: ../index.php');
	exit;
}

	echo AC_PATH . $page;

?>
" style="border:thin solid blue;scrolling: no;align:right;" height="640" width="90%" title="<?php echo _AT('acollab_frame').$_SERVER['PHP_SELF']; ?>">

</iframe>
<?php
}


	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
