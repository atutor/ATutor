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


	if ($_SESSION['is_guest'] || !$_SESSION['member_id']) {
		exit;
	}

if(ereg("Mozilla" ,$HTTP_USER_AGENT) && ereg("4.", $BROWSER["Version"])){
	$help[]= AT_HELP_NETSCAPE4;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  lang="<?php echo $_SESSION['lang']; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />

	<title><?php echo SITE_NAME; ?></title>
	<base href="<?php echo $_base_href; ?>" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $_base_href.'css/'.$_colours[$_SESSION['prefs'][PREF_STYLESHEET]]['FILE']; ?>.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $_base_href.'css/'.$_fonts[$_SESSION['prefs'][PREF_FONT]]['FILE']; ?>.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
		if (in_array($_SESSION['lang'], array('ar', 'fa', 'he'))) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo $onload; ?>>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
<?php require($_include_path.'html/user_bar.inc.php'); ?>
<tr><td colspan="2" class="row3" height="1"><img src="images/clr.gif" height="1" alt="" width="1" /></td></tr>
</table>
<table border="0" cellspacing="2" cellpadding="3" width="100%" summary="">
<tr>
	<td class="bodyline" valign="top" width="140"><a name="navigation"></a>
	* <a href="users/"><?php echo _AT('home'); ?></a><br />
	* <a href="users/edit.php"><?php echo _AT('edit_profile'); ?></a><br />
	* <a href="users/browse.php"><?php echo _AT('browse_courses'); ?></a><br />
	* <a href="logout.php"><?php echo _AT('logout'); ?></a><br />
	</td>
<td valign="top"><a name="content"></a>
<?php 

	if ($_GET['f']) {
		$f = intval($_GET['f']);
		if ($f > 0) {
			print_feedback($f);
		} else {
			/* it's probably an array */
			$f = unserialize(urldecode($_GET['f']));
			print_feedback($f);
		}

	}
print_feedback($feedback);

if (isset($errors)) {
	print_errors($errors);
	unset($errors);
}

print_infos($infos);
?>