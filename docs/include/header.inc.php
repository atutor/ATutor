<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: header.inc.php,v 1.16 2004/03/30 15:43:47 joel Exp $

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
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>" lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title><?php echo stripslashes(SITE_NAME); ?> - <?php echo $_SESSION['course_title'];
	if ($cid != 0) {
		$myPath = $contentManager->getContentPath($cid);
		$num_path = count($myPath);
		for ($i =0; $i<$num_path; $i++) {
			echo ' - ';
			echo $myPath[$i]['title'];
		}
	} else if (is_array($_section) ) {
		$num_sections = count($_section);
		for($i = 0; $i < $num_sections; $i++) {
			echo ' - ';
			echo $_section[$i][0];
		}
	}
	?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
	<base href="<?php echo $_base_href; 
		if (!BACKWARDS_COMPATIBILITY || $content_base_href) {
			echo $course_base_href;
			if ($content_base_href) {
				echo $content_base_href;
			}
		}
		?>" />

	<link rel="stylesheet" href="<?php echo $_base_path; ?>stylesheet.css" type="text/css" />
	<?php
		if ($_SESSION['prefs'][PREF_OVERRIDE] && file_exists(AT_INCLUDE_PATH.'../content/'.$_SESSION['course_id'].'/stylesheet.css')) {
			echo '<link rel="stylesheet" href="'.$_base_path.'content/'.$_SESSION['course_id'].'/stylesheet.css" type="text/css" />'."\n";
		} else {
			/* colour theme */
			echo '<link rel="stylesheet" href="'.$_base_path.'css/'.$_colours[$_SESSION['prefs'][PREF_STYLESHEET]]['FILE'].'.css" type="text/css" />'."\n";

			if ($_SESSION['prefs'][PREF_FONT]) {
				/* font theme */
				echo '<link rel="stylesheet" href="'.$_base_path.'css/'.$_fonts[$_SESSION['prefs'][PREF_FONT]]['FILE'].'.css" type="text/css" />'."\n";
			}
		
		}
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
	<link rel="stylesheet" href="<?php echo $_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="shortcut icon" href="<?php echo $_base_path; ?>favicon.ico" type="image/x-icon" />
</head>
<body  <?php echo $onload; ?>>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $_base_path; ?>overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script><?php debug($t); unset($t); ?>

<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
	<tr>
	<td colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
<?php require(AT_INCLUDE_PATH.'html/user_bar.inc.php'); ?>
<tr>
	<td colspan="2" class="row3" height="1"><img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" alt="" /></td>
</tr>
<tr>
	<td align="center" class="row1"><?php
	// Check to see if this course has a header defined. If yes, insert it in
	// place of the default course title
	$sql_head="select header from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";
	if($result=mysql_query($sql_head, $db)){
		while($row=mysql_fetch_row($result)){
			if(strlen($row[0])>0){
				$custom_head = $row[0];
				if (BACKWARDS_COMPATIBILITY) {
					$custom_head = str_replace('CONTENT_DIR', $_base_path.'content/'.$_SESSION['course_id'], $custom_head);
				} else {
					$custom_head = str_replace('CONTENT_DIR/', '', $custom_head);
				}
			}
		}
	} 
	if(strlen($custom_head)>0){
		echo $custom_head;
	}else{
		echo '<h2>' . $_SESSION['course_title'] . '</h2>';
	} ?>
	</td>
</tr>
<tr><td colspan="2" class="row3" height="1"><img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" alt="" /></td></tr>
<tr>
	<td id="tools" valign="top" colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" summary="">
		<tr>
			<td width="20%" class="cat2" valign="top"><a name="navigation"></a><?php

			/* home */
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'index.php?g=14" accesskey="1" title="'._AT('home').' Alt-1"><img src="'.$_base_path.'images/home.gif" class="menuimage" border="0" alt="'._AT('home').'" /></a>'."\n";
			}
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'index.php?g=14" accesskey="1" title="'._AT('home').' Alt-1">'._AT('home').'</a>'."\n";
			}
			echo '</td>'."\n";

			/* tools */
			echo '<td width="20%" class="cat2b">'."\n";
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'tools/index.php?g=15" accesskey="2" title="'._AT('tools').' Alt-2"><img src="'.$_base_path.'images/tools.gif" class="menuimage"  border="0" alt="'._AT('tools').'" /></a>'."\n";
			}
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'tools/index.php?g=15" accesskey="2" title="'._AT('tools').' Alt-2">'._AT('tools').'</a>'."\n";
			}
			echo '</td>'."\n";

			/* resources */
			echo '<td width="20%" class="cat2c">'."\n";
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'resources/index.php?g=16" accesskey="3" title="'._AT('resources').' Alt-3"><img src="'.$_base_path.'images/resources.gif" class="menuimage" border="0" alt="'._AT('resources').'" /></a>'."\n";
			}
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'resources/index.php?g=16" accesskey="3" title="'._AT('resources').' Alt-3">'._AT('resources').'</a>'."\n";
			}
			echo '</td>'."\n";

			/* discussions */
			echo '<td width="20%" class="cat2d" style="white-space:nowrap;">';
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'discussions/index.php?g=17" accesskey="4" title="'._AT('discussions').' Alt-4"><img src="'.$_base_path.'images/discussions.gif" class="menuimage"  border="0" alt="'._AT('discussions').'" /></a>'."\n";
			}
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
				echo '<a class="white" href="'.$_base_path.'discussions/index.php?g=17" accesskey="4" title="'._AT('discussions').' Alt-4">'._AT('discussions').'</a>'."\n";
			}
			echo '</td>'."\n";

			/* help */
			echo '<td width="20%" class="cat2e">'."\n";
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'help/index.php?g=18" accesskey="5" title="'._AT('help').' Alt-5"><img src="'.$_base_path.'images/help.gif" class="menuimage" height="25" width="28" border="0" alt="'._AT('help').'" /></a>'."\n";
			}
			if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'help/index.php?g=18" accesskey="5" title="'._AT('help').' Alt-5">'._AT('help').'</a>'."\n";
			}
			echo '</td>'."\n";
			?>
			</tr>
		</table></td>
</tr>
<?php if ($_SESSION['prefs'][PREF_BREADCRUMBS]) { ?>
<tr>
	<td valign="middle" class="breadcrumbs"><?php require(AT_INCLUDE_PATH.'html/breadcrumbs.inc.php'); ?></td>
</tr>
<?php } ?>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="" id="content">
<tr><?php
	if ( ($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT)	) {
		/* the menu is open: */
		echo '<td id="menu" width="25%" valign="top" rowspan="2" style="padding-top: 1px;">';

		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
		echo '	<tr><td class="cata" valign="top">';
		print_popup_help(AT_HELP_MAIN_MENU);
		echo '<a name="menu"></a><a class="white" href="'.$_my_uri.'disable='.PREF_MAIN_MENU.'" accesskey="6" title="'._AT('close_menu').': Alt-6">';
		echo _AT('close_menus').'';
		echo '</a>';
		echo '</td></tr></table>';

		if (is_array($_SESSION['prefs'][PREF_STACK])) {
			foreach ($_SESSION['prefs'][PREF_STACK] as $stack_id) {
				echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" />';
				require(AT_INCLUDE_PATH.'html/dropdowns/'.$_stacks[$stack_id].'.inc.php');
			}
		}

		echo '</td>';
	}
	echo '<td width="3"><img src="'.$_base_path.'images/clr.gif" width="3" height="3" alt="" /></td>';
	if (($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) || ($_SESSION['prefs'][PREF_MAIN_MENU] == 0)) {
		echo '<td valign="top" width="100%">';
	} else {
		echo '<td valign="top" width="75%">';
	}
	?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
		<tr><?php

			if ( ($_SESSION['prefs'][PREF_MAIN_MENU] != 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) ) {
				echo '<td width="25%" valign="top" class="hide">';
				
				echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
				echo '<tr><td class="cata" valign="top">';

				print_popup_help(AT_HELP_MAIN_MENU);

				echo '<a name="menu"></a><a class="white" href="'.$_my_uri.($_SESSION['prefs'][PREF_MAIN_MENU] ? 'disable' : 'enable').'='.PREF_MAIN_MENU.$cid_url.'" accesskey="6" title="'._AT('open_menus').' ALT-6">'._AT('open_menus').'</a>';
				echo '</td></tr></table>';

				echo '</td>';
			}
		?>
			<td width="75%" valign="top"></td>
		<?php

			if ( ($_SESSION['prefs'][PREF_MAIN_MENU] != 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] != MENU_LEFT) ) {
				echo '<td width="25%" valign="top" class="hide">';

				echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
				echo '<tr><td class="cata" valign="top">';
				print_popup_help(AT_HELP_MAIN_MENU);
				echo '<a name="menu"></a><a class="white" href="'.$_my_uri.($_SESSION['prefs'][PREF_MAIN_MENU] ? 'disable' : 'enable').'='.PREF_MAIN_MENU.$cid_url.'" accesskey="6" title="'._AT('open_menus').' ALT-6">'._AT('open_menus').'</a>';
				echo '</td></tr></table>';

				echo '</td>';
			}
		?>
		</tr> 
		</table><a name="content"></a><?php

	$cid = intval($_GET['cid']);

	$next_prev_links = $contentManager->generateSequenceCrumbs($cid);

	if ($_SESSION['prefs'][PREF_SEQ] != BOTTOM) {
		echo '<div align="right" id="seqtop">' . $next_prev_links . '</div>';
	}

	if ($_GET['f']) {
		$f = intval($_GET['f']);
		if ($f > 0) {
			print_feedback($f);
		} else {
			/* it's probably an array */
			$f = unserialize(urldecode(stripslashes($_GET['f'])));
			print_feedback($f);
		}
	}

	if(ereg('Mozilla' ,$HTTP_USER_AGENT) && ereg('4.', $BROWSER['Version'])){
		$help[]= AT_HELP_NETSCAPE4;
	}

	if (isset($errors)) {
		print_errors($errors);
		unset($errors);
	}
	print_warnings($warnings);


	/**
	$microtime = microtime();
	$microsecs = substr($microtime, 2, 8);
	$secs = substr($microtime, 11);
	$endTime = "$secs.$microsecs";
	$t .= 'Timer: Header generated in ';
	$t .= sprintf("%.4f",($endTime - $startTime));
	$t .= ' seconds.';
	debug($t);
	unset($t);
	*/

?>
