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

define('AT_INCLUDE_PATH', '../../include/');

$CACHE_DEBUG=0;
require(AT_INCLUDE_PATH.'vitals.inc.php');

require('include/functions.inc.php');
$admin = getAdminSettings();

$_section[0][0] = _AC('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AC('chat');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" hspace="2" vspace="2" height="38" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/index.php?g=11">'._AC('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/chat-large.gif"  class="menuimageh3"  width="42" height="38" border="0" alt=""/>';
}
echo _AC('chat');
echo '</h3>';

?>
<p align="center"><a href="discussions/achat/chat.php?firstLoginFlag=1<?php echo SEP; ?>g=31" onfocus="this.className='highlight'" onblur="this.className=''"><b> <?php echo _AC('enter_chat');  ?></b></a>
<?php
if(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
	echo '&nbsp;<small>(<a href="discussions/achat/admin/chat.php">'._AC('chat_start_tran1').'</a>)</small>';
}
echo '</p>';
?>
<h4><?php echo _AC('transcripts');  ?></h4>
<?php

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'date';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'desc';
}

${'highlight_'.$col} = ' u';
	$tran_files = array();
	if (!@opendir(AT_CONTENT_DIR . 'chat/')){
		mkdir(AT_CONTENT_DIR . 'chat/', 0777);
	}
	if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/')) {
		while (($file = readdir($dir)) !== false) {
			if (substr($file, -strlen('.html')) == '.html') {
				$la	= stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$file);

				$file = str_replace('.html', '', $file);
				$tran_files[$file] = $la['ctime'];
			}
		}
	}else{
		@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'], 0777);
		@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/', 0776);
		@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/', 0776);
		@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/', 0776);
		@copy('admin.settings.default', AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings');
		@chmod (AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0777);

		if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/')) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, -strlen('.html')) == '.html') {
					$la	= stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$file);

					$file = str_replace('.html', '', $file);
					$tran_files[$file] = $la['ctime'];
				}

			}
		}else{
			echo "still nothing";

			}

	}

	if (count($tran_files) == 0) {
		echo '<p>'._AC('chat_none_found').'</p>';
	} else {
		echo '<table cellspacing="1" cellpadding="0" border="0" width="99%" align="center" summary="" class="bodyline">';
		echo '<tr>';
		echo '<th scope="col" class="cat" align="left"><small><a href="'.$_SERVER['PHP_SELF'].'?col=name'.SEP.'order=asc" class="nav'.$highlight_name.'" title="'._AC('chat_sort_by_name').'" onfocus="this.className=\'highlight\'" onblur="this.className=\'nav'.$highlight_name.'\'">'._AC('chat_name').'</a> ';
		if (($col == 'name') && ($order == 'asc')) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?col=name'.SEP.'order=desc" title="'._AC('chat_name_descending').'"><img src="images/desc.gif" height="7" width="11" alt="'._AC('chat_name_descending').'" border="0" /></a>';
		} else if (($col == 'name') && ($order == 'desc')) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?col=name'.SEP.'order=asc" title="'._AC('chat_name_ascending').'"><img src="images/asc.gif" height="7" width="11" alt="'._AC('chat_name_ascending').'" border="0" /></a>';
		} else {
			echo '<img src="images/clr.gif" height="7" width="11" alt="" />';
		}
		echo '</small></th>';
		echo '<th scope="col" class="cat">&nbsp;</th>';
		echo '<th scope="col" class="cat" align="right"><small><a href="'.$_SERVER['PHP_SELF'].'?col=date'.SEP.'order=desc" class="nav'.$highlight_date.'" title="'._AC('chat_sort_by_date').'" onfocus="this.className=\'highlight\'" onblur="this.className=\'nav'.$highlight_date.'\'">'._AC('chat_date').'</a> ';
		if (($col == 'date') && ($order == 'asc')) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?col=date'.SEP.'order=desc" title="'._AC('chat_date_descending').'"><img src="images/desc.gif" height="7" width="11" alt="'._AC('chat_date_descending').'" border="0" /></a>';
		} else if (($col == 'date') && ($order == 'desc')) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?col=date'.SEP.'order=asc" title="'._AC('chat_date_ascending').'"><img src="images/asc.gif" height="7" width="11" alt="'._AC('chat_date_ascending').'" border="0" /></a>';
		} else {
			echo '<img src="images/clr.gif" height="7" width="11" alt="" />';
		}
		echo '</small></th>';
		if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
			echo '<th scope="col" class="cat">&nbsp;</th>';
		}

		echo '</tr>';

		if (($col == 'date') && ($order == 'asc')) {
			asort($tran_files);
		} else if (($col == 'date') && ($order == 'desc')) {
			arsort($tran_files);
		} else if (($col == 'name') && ($order == 'asc')) {
			ksort($tran_files);
		} else if (($col == 'name') && ($order == 'desc')) {
			krsort($tran_files);
		}
		reset ($tran_files);

		foreach ($tran_files as $file => $date) {
			echo '<tr>';
			echo '<td class="row1"><small><a href="discussions/achat/tran.php?t='.$file.'" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'.$file.'</a>';
			echo '</small></td>';
			echo '<td class="row1"><small>';

			if (($file.'.html' == $admin['tranFile']) && ($admin['produceTran'])) {

				echo '<strong>'._AC('chat_currently_active').'</strong>';
			}
			echo '&nbsp;</small></td>';
				
			echo '<td class="row1" align="center"><small>'.date('Y-m-d h:i:s', $date).'</small></td>';
			
			if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
				echo '<td class="row1" align="right"><small>';
				if (($file.'.html' == $admin['tranFile']) && ($admin['produceTran'])) {

					echo '&nbsp;';
				} else {
					echo '<a href="discussions/achat/tran_delete.php?m='.$file.'" onfocus="this.className=\'highlight\'" onblur="this.className=\'\'">'._AC('chat_delete').'</a>';
				}
				
				echo '</small></td>';
			}

			echo '</tr>';
		}
		echo '</table>';
		echo '<p><small>'._AC('chat_use_headings_to_sort').'</small></p>';
	}
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
