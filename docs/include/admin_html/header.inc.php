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
if (!defined('AT_INCLUDE_PATH')) { exit; }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>" lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
	<title><?php echo SITE_NAME; ?> - <?php echo _AT('administration'); ?></title>
	<base href="<?php echo $_base_href; ?>" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	<?php
		if (in_array($_SESSION['lang'], array('ar', 'fa', 'he'))) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo $onload; ?>><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div><script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
<tr>
	<td colspan="2" class="topbar" valign="middle">
	<strong><?php echo SITE_NAME; ?> <?php echo _AT('administration'); ?></strong> <?php
	if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 2) {
		echo '<a href="users/index.php" title="'._AT('logout').'" target="_top"><img src="images/logout.gif" border="0" height="14" width="15" alt="'._AT('logout').'" class="menuimage2" /></a>';
	}
	if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 1) {
		echo ' <a href="users/index.php">'._AT('logout').'</a>';
	}
	?>
	</td>
</tr>
<tr><td colspan="2" class="row3" height="1"><img src="images/clr.gif" height="1" width="1" alt="" /></td></tr>
</table>
<table border="0" cellspacing="2" cellpadding="3" width="100%" summary="">
<tr>
	<td class="bodyline" valign="top" width="140"><a name="navigation"></a>
	* <a href="admin/"><?php echo _AT('home'); ?></a><br />
	* <a href="admin/users.php"><?php echo _AT('users'); ?></a><br />
	* <a href="admin/courses.php"><?php echo _AT('courses'); ?></a><br />
	* <a href="admin/language.php"><?php echo _AT('language'); ?></a><br />
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

print_errors($errors);
if($warnings){
	print_warnings($warnings);
	echo '<p><a href="'.$PHP_SELF.'?current_cat='.$_GET['current_cat'].SEP.'delete=1'.SEP.'confirm=1">'._AT('yes_delete').'</a> | <a href="'.$PHP_SELF.'?cancel=1">'._AT('no_cancel').'</a></p>';
}
?>
