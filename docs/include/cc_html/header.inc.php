<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

	if ($_SESSION['is_guest'] || !$_SESSION['member_id']) {
		exit;
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  lang="<?php echo $_SESSION['lang']; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />

	<title><?php echo SITE_NAME; ?></title>
	<base href="<?php echo $_base_href; ?>" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo $_base_href.'css/'.$_fonts[$_SESSION['prefs'][PREF_FONT]]['FILE']; ?>.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo (isset($onload) ? $online : ''); ?>>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
<?php require(AT_INCLUDE_PATH.'html/user_bar.inc.php'); ?>
<tr><td colspan="2" class="row3" height="1"><img src="images/clr.gif" height="1" width="1" alt="" /></td></tr>
</table>

<table width="98%" align="center" cellpadding="5" cellspacing="0">
	<tr>
		<td valign="bottom" nowrap="nowrap"><h2><?php echo _AT('control_centre');  ?></h2></td>
		<td valign="bottom"><h3><?php echo $title ?></h3></td>
	<tr>
	<td valign="top">
	<table width="100%" class="bodyline">
		<tr><td valign="top" class="cc_menu">
			<img src="images/home.jpg"> <a href="users/"><?php echo _AT('home'); ?></a><br />
			<img src="images/profile.jpg"> <a href="users/edit.php"><?php echo _AT('profile'); ?></a> <br />
			<img src="images/browse.gif"> <a href="users/browse.php"><?php echo _AT('browse_courses'); ?></a><br />
			<hr />
<?php 
$status=0;
$sql='SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
$result = mysql_query($sql,$db);
if ($row = mysql_fetch_assoc($result)) {
	$status = $row['status'];
}

if ($status==1) { ?>
			<img src="images/create.jpg"> <a href="users/create_course.php"><?php echo _AT('create_course'); ?></a><br />
			<hr />
<?php } ?>
			
			<img src="images/logout.gif"> <a href="logout.php"><?php echo _AT('logout'); ?></a> <br />
			</td>
		</tr>
	</table>
</td><td width="100%" valign="top">