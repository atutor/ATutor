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
header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title><?php echo stripslashes(SITE_NAME); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<link rel="stylesheet" href="basic_styles.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo (isset($errors) ? '' : $onload); ?> >
<br />
<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
	<tr>
	<td colspan="6" align="center">
		<table bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" width="100%" style="background-image: url('<?php echo HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" summary="">
		<tr>
			<td width="30%"></td>
			<td width="0" height="80" nowrap="nowrap" align="right" valign="top"><br />			
			<?php
			if (HEADER_LOGO != '') {
				echo '<img src="'.HEADER_LOGO.'" border="0" alt="'.SITE_NAME.'" />&nbsp;';
			}
			?>
			<h4><?php echo stripslashes(SITE_NAME); ?>&nbsp;</h4></td>			
		</tr>
		<tr><td colspan="2">
		<table width="100%" cellpadding="0" cellspacing="0" summary="">
		<tr>
			<td class="cyan" align="right" valign="middle">			
<?php
		if ($page == HOME_URL && HOME_URL !='') {
			echo '<u>'._AT('home').'</u>';
			echo ' <span class="spacer">|</span> ';
		} else if (HOME_URL!='') {
			echo '<a class="cyan" href="'.HOME_URL.'">'._AT('home').'</a>';
			echo ' <span class="spacer">|</span> ';
		}		

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
	
	<table width="100%" summary="">
		<tr><td valign="top">
