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
	?></h2>

<?php

if (!defined('AC_PATH')) {
	print_infos(AT_INFOS_NO_ACOLLAB);
}else{

	echo '<br /><h3>ACollab '._AT('tools').'</h3><br />';

?>
<a href="<?php echo AC_PATH; ?>"><?php echo  _AT('acollab_own_window'); ?></a><br />

<script language="javascript">
function check_location(){
<!--
	if(frames['0'].window.document.forms[0].login){
		location.replace("<?php echo $_base_href; ?>login.php");
	}
	if(frames['0'].window.document.forms[0].jump &&  !frames['0'].window.document.forms[0].p){
		location.replace('<?php echo $_base_href; ?>index.php?enable=PREF_MAIN_MENU');
	}
}
-->
</script>

<iframe onload="check_location();" src ="
<?php
if($_GET['p'] != ''){
	$page = urldecode($_GET['p'].'?disable=PREF_MAIN_MENU');
}else {
	$page = 'index.php?disable=PREF_MAIN_MENU';
}
	echo AC_PATH . $page;
?>
" style="border:thin solid blue;scrolling: no;align:right;" height="640" width="90%" id="acollab_frame" title="<?php echo _AT('acollab_frame').$_SERVER['PHP_SELF']; ?> name="acollab_frame">
</iframe>

<?php
}
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
