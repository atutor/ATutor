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
// $Id: header.tmpl.php,v 1.2 2004/04/12 16:29:55 heidi Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }

$microtime = microtime();
$microsecs = substr($microtime, 2, 8);
$secs = substr($microtime, 11);
$endTime = "$secs.$microsecs";
$t .= 'Timer: Vitals parsed in ';
$t .= sprintf("%.4f",($endTime - $startTime));
$t .= ' seconds.';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $tmpl_lang; ?>">
<head>
	<title><?php echo $tmpl_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $tmpl_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<link rel="stylesheet" href="<?php echo $_base_path; ?>stylesheet.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="shortcut icon" href="<?php echo $_base_path; ?>favicon.ico" type="image/x-icon" />
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo $tmpl_onload; ?> >

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $_base_path; ?>overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<?php debug($t); unset($t); ?>

<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
	<tr>
		<td colspan="6">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
			<!---user bar-->
				<?php require(AT_INCLUDE_PATH.'html/user_bar.inc.php'); ?>
			<!---end user bar-->
				<tr>
					<td colspan="2" class="row3" height="1"><img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" alt="" /></td>
				</tr>
				<tr> 
					<td align="center" class="row1">
						<h2><?php echo $tmpl_course_title; ?></h2>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="row3" height="1">
						<img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" alt="" />
					</td>
				</tr>

			<!---tool's bar-->
				<?php require(AT_INCLUDE_PATH.'html/tools_bar.inc.php'); ?>
			<!---end tool's bar-->

				<?php if ($tmpl_breadcrumbs): ?>
					<tr>
						<td valign="middle" class="breadcrumbs">
						<!---breadcrumbs-->
							<?php require(AT_INCLUDE_PATH.'html/breadcrumbs.inc.php'); ?>
						<!---end breadcrumbs-->
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="" id="content">
				<tr>
					<?php if ($tmpl_menu_open): ?>
						<td id="menu" width="25%" valign="top" rowspan="2" style="padding-top: 1px;">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
								<tr>
									<td class="cata" valign="top">
									<?php print_popup_help(AT_HELP_MAIN_MENU); ?>
									<a name="menu"></a>
									<a class="white" href="<?php echo $tmpl_close_menu_url; ?>" accesskey="6" title="<?php echo _AT('close_menu')?>: Alt-6"><?php echo _AT('close_menus'); ?></a>
									</td>
								</tr>
							</table>
							<?php print_pref_stack(); ?>
						</td>
					<?php endif; ?>
					<td width="3">
						<img src="<?php echo $_base_path?>images/clr.gif" width="3" height="3" alt="" />
					</td>
					<td valign="top" width="<?php echo $tmpl_width; ?>">
						<table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
						<tr>

							<?php if ($tmpl_menu_closed && $tmpl_menu_left): ?>
								<td width="25%" valign="top" class="hide">								
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
										<tr>
											<td class="cata" valign="top">
											<?php print_popup_help(AT_HELP_MAIN_MENU); ?>
											<a name="menu"></a><a class="white" href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
											</td>
										</tr>
									</table>
								</td>
							<?php endif;?>

							<td width="75%" valign="top"></td>

							<?php if ($tmpl_menu_closed && !$tmpl_menu_left): ?>
								<td width="25%" valign="top" class="hide">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
										<tr>
											<td class="cata" valign="top">
											<?php print_popup_help(AT_HELP_MAIN_MENU);?>
											<a name="menu"></a>
											<a class="white" href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
											</td>
										</tr>
									</table>
								</td>
							<?php endif;?>	
							
						</tr> 
						</table>

<a name="content"></a>

