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
if (!defined('AT_INCLUDE_PATH')) { exit; }

// this should be removed from template version
global $db;
global $_base_path;
?>
<tr>
	<td colspan="2" class="topbar" valign="middle"><a href="<?php
	echo $_SERVER['REQUEST_URI'];
	?>#content" accesskey="c"><img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_top'); ?>: ALT-c" /></a><a href="<?php echo $_my_uri;

if(($_SESSION['prefs'][PREF_MAIN_MENU] !='' && ( $_SESSION['prefs'][PREF_MENU] == 1) || ($_SESSION['prefs'][PREF_LOCAL] == 1)) && !$_GET['menu_jump'] && $_GET['disable'] != PREF_MAIN_MENU && $_SESSION['course_id'] != 0){
	echo '#menu';
	if($_GET['collapse']){
		echo $_GET['collapse'];
	}else if ($_GET['cid'] && !$_GET['disable'] && !$_GET['expand']){
		echo $_GET['cid'];
	}else if ($_GET['expand']){
		echo $_GET['expand'];
	}else{
		echo $_SESSION['s_cid'];
	}
}else if($_GET['menu_jump']){
	echo SEP.'menu_jump='.$_GET['menu_jump'].'#menu_jump'.$_GET['menu_jump'];
}else{
	echo '#menu';
}

echo '" accesskey="m">';

echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_menu').' Alt-m" /></a>';
if ($_SESSION['course_id'] != 0) {
	echo '<a href="'.substr($_my_uri, 0, strlen($_my_uri)-1).'#navigation" accesskey="y">';
	echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_mainnav').' ALT-y" /></a>';
	echo '<a href="'.$_base_path.'help/accessibility.php#content">';
	echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" border="0" alt="'._AT('goto_accessibility').'" /></a>';
}

echo '<form method="post" action="'.$_base_path.'bounce.php" target="_top">';
		$pipe = "\n".' <span class="spacer">|</span> '."\n";

		echo '<small class="loginwhite">';
		echo _AT('login').': ';
		if ($_SESSION['valid_user'] === true) {
			echo '<strong>' , AT_print($_SESSION['login'], 'members.login') , '</strong> ';
			echo $pipe;
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 2) {
				echo '<a class="white" href="'.$_base_path.'logout.php?g=19" title="'._AT('logout').'" target="_top"><img src="'.$_base_path.'images/logout.gif" border="0" style="height:1.14em; width:1.26em" height="14" width="15" alt="'._AT('logout').'" class="menuimage2" /></a>';
			}
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 1) {
				echo ' <a class="white" href="'.$_base_path.'logout.php?g=19" target="_top">'._AT('logout').'</a>';
			}
		} else {
			echo ' <strong>'._AT('guest').'</strong>. ';
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 2) {
				echo '<a class="white" href="'.$_base_path.'login.php?course='.$_SESSION['course_id'].'" title="'._AT('login').'"><img src="'.$_base_path.'images/login.gif" border="0" style="height:1.14em; width:1.15em;" height="15" width="16" alt="'._AT('login').'" class="menuimage2" /></a>'."\n";
			}
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 1) {
				echo ' <a class="white" href="'.$_base_path.'login.php?course='.$_SESSION['course_id'].'">'._AT('login').'</a>';
			}
		}

		if ($_SESSION['course_id'] != 0) {
			echo $pipe;
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 2) {
				echo '<a class="white" href="'.$_base_path.'tools/sitemap/index.php?g=23" title="'._AT('sitemap').'"><img src="'.$_base_path.'images/toc.gif" style="height:1.2em; width:1.2em;" width="16" height="16" alt="'._AT('sitemap').'" border="0" class="menuimage2" /></a>';
			}
			if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 1) {
				echo ' <a class="white" href="'.$_base_path.'tools/sitemap/index.php?g=23">'._AT('sitemap').'</a> ';	
			}
		}

		if ($_SESSION['course_id'] != 0) {
			echo $pipe;
			if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'tools/preferences.php?g=20" title="'._AT('preferences').'"><img src="'.$_base_path.'images/prefs.gif" style="height:1.16em; width:1.26em;" width="16" height="14" alt="'._AT('preferences').'" border="0" class="menuimage2" /></a>';
			}
			if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'tools/preferences.php?g=20">'._AT('preferences').'</a> ';
			}
		}

		if ($_SESSION['valid_user']) {
			echo $pipe;
			$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND new=1";
			$result	= mysql_query($sql, $db);
			$row	= mysql_fetch_array($result);

			if ($_SESSION['course_id'] == 0) {
				$temp_path = 'users/';
			} else {
				$temp_path = $_base_path;
			}

			if ($row['cnt'] > 0) {
				if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 2) {
					echo '<a class="white" href="'.$temp_path.'inbox.php?g=21" title="'._AT('you_have_messages').'"><img src="'.$_base_path.'images/inbox2.gif" border="0" class="menuimage2" style="height:.9em; width:1.16em"  width="14" height="10" alt="'._AT('you_have_messages').'" /></a>';
				}
				if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 1) {
					echo ' <a class="white" href="'.$temp_path.'inbox.php?g=21" title="'._AT('you_have_messages').'"><strong> '._AT('inbox').' </strong></a>';
				}
			} else {
				if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 2) {
					echo '<a class="white" href="'.$temp_path.'inbox.php?g=21" title="'._AT('inbox').'"><img src="'.$_base_path.'images/inbox.gif" border="0" style="height:.9em; width:1.16em" class="menuimage2"  width="14" height="10" alt="'._AT('inbox').'" /></a>';
				}
				if ($_SESSION['prefs']['PREF_LOGIN_ICONS'] != 1) {
					echo ' <a class="white" href="'.$temp_path.'inbox.php?g=21">'._AT('inbox').'</a>';
				}
			}
		}

		if (show_pen()) {
			echo $pipe;
			if ($_SESSION['prefs']['PREF_EDIT'] == 0) {
				if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 2) {
					echo '<a class="white" href="'.$_my_uri.'enable='.PREF_EDIT.'" title="'._AT('enable_editor').'"><img src="'.$_base_path.'images/pen.gif" border="0" class="menuimage2" alt="'._AT('enable_editor').'" style="height:1.1em; width:1.26em"  height="14" width="16"/></a>';
				}
				if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 1) {
					echo ' <a class="white" href="'.$_my_uri.'enable='.PREF_EDIT.'">'._AT('enable_editor').'</a>';
				}
			} else {
				if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 2) {
					echo '<a class="white" href="'.$_my_uri.'disable='.PREF_EDIT.'" title="'._AT('disable_editor').'"><img src="'.$_base_path.'images/pen2.gif" border="0" class="menuimage2" alt="'._AT('disable_editor').'" style="height:1.1em; width:1.26em"  height="14" width="16"/></a>';
				}
				if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 1) {
					echo ' <a class="white" href="'.$_my_uri.'disable='.PREF_EDIT.'">'._AT('disable_editor').'</a>';
				}
			}
		}

		if ($_SESSION['valid_user']) {
			echo $pipe;
			/* show the list of courses with jump linke */
			echo "\n".'&nbsp;<label for="j" accesskey="j"></label><span style="white-space: nowrap;"><select name="course" class="dropdown" id="j" title="Jump:  ALT-j">'."\n";
			echo '<option value="0">'._AT('my_control_centre').'</option>';
			$sql	= "SELECT E.course_id FROM ".TABLE_PREFIX."course_enrollment E WHERE E.member_id=$_SESSION[member_id] AND E.approved='y'";
			$result = mysql_query($sql,$db);

			if ($row = mysql_fetch_assoc($result)) {
				echo '<optgroup label="'._AT('courses_below').'">';
				do {
					echo '<option value="'.$row['course_id'].'"';
					if ($_SESSION['course_id'] == $row['course_id']) {
						echo ' selected="selected"';
					}
					echo '>'.$system_courses[$row['course_id']]['title'];
					echo $row['title'];
					echo '</option>'."\n";
				} while ($row = mysql_fetch_assoc($result));
				echo '</optgroup>';
			}
			echo '</select>&nbsp;';
			echo '<input type="submit" name="jump" value="'._AT('jump').'" class="button2" /></span>&nbsp;';
			echo '<input type="hidden" name="g" value="22" />';

		} else {
			/* this user is a guest */
			echo $pipe;
			if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 2) {
				echo '<a class="white" href="'.$_base_path.'browse.php" title="'._AT('browse_courses').'"><img src="'.$_base_path.'images/browse.gif" border="0" alt="'._AT('browse_courses').'" class="menuimage2" style="height:1.1em; width:1.26em" height="14" width="16" /></a>';
			}
			if ($_SESSION['prefs'][PREF_LOGIN_ICONS] != 1) {
				echo ' <a class="white" href="'.$_base_path.'browse.php">'._AT('browse_courses').'</a>';
			}
		}
 
	?></small></form></td>
</tr>