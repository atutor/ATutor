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
Header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title><?php echo SITE_NAME; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />

	<link rel="stylesheet" href="basic_styles.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo $onload; ?>>
<br />
<table width="96%" height="90%" align="center" cellpadding="0" cellspacing="0" class="bodyline">
	<tr>
	<td colspan="6" align="center">
		<table cellpadding="0" cellspacing="0" class="headerimg">
		<tr>
			<td width="30%"></td>
			<td width="0" height="80" nowrap align="right" valign="top"><br /><a href="http://www.atutor.ca"><img src="images/at-logo.v.3.gif" alt="ATutor - home" height="26" width="80" border="0" /></a><sup>&#174;</sup>&nbsp;
			<h4 bgcolor="white">Learning Content Management System&nbsp;</h4></td>			
		</tr>
		<tr><td colspan="2">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="cyan" align="right" valign="middle">			
<?php
		echo '';
		if ($page == 'about') {
			echo '<u>'._AT('about_us').'</u>';
		} else {
			echo '<a class="cyan" href="about.php">'._AT('about_us').'</a>';
		}

		echo ' <span class="spacer">|</span> ';

		if ($page == 'register') {
			echo '<u>'._AT('register').'</u>';
		} else {
			echo '<a class="cyan" href="registration.php">'._AT('register').'</a>';
		}

		echo ' <span class="spacer">|</span> ';

		if ($page == 'browse') {
			echo '<u>'._AT('browse_courses').'</u>';
		} else {
			echo '<a class="cyan" href="browse.php">'._AT('browse_courses').'</a>';
		}

		echo ' <span class="spacer">|</span> ';

		if ($page == 'login') { 
			echo '<u>'._AT('login').'</u>';
		} else {
			echo '<a class="cyan" href="login.php">'._AT('login').'</a>';
		}

		echo ' <span class="spacer">|</span> ';

		if ($page == 'password') { 
			echo '<u>'._AT('password_reminder').'</u> ';
		} else {
			echo '<a class="cyan" href="password_reminder.php">'._AT('password_reminder').'</a> ';
		}
?>
			
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
	</tr>

	<tr>
	<td valign="top" >
	
	<table width="100%">
		<tr><td valign="top">