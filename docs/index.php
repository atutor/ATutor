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
// $Id$

$page = 'home';
define('AT_INCLUDE_PATH', 'include/');
$_section = 'home';

	require(AT_INCLUDE_PATH.'vitals.inc.php');
	
	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	$course_base_href = 'get.php/';

	if (!$cid) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		
		$msg->printAll();

		/* show the enable editor tool top if the editor is currently disabled */
		if (authenticate(AT_PRIV_ANNOUNCEMENTS, AT_PRIV_RETURN) && ($_SESSION['prefs'][PREF_EDIT] !=1) ) {
			$help = array('ENABLE_EDITOR', $_my_uri);
			$msg->printHelps($help);
			unset($help);
		} else if (!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
			$sql    = "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND preferences<>''";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				$msg->printHelps('COURSE_REF');
			}
		}

		require(AT_INCLUDE_PATH.'html/announcements.inc.php');

		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */

	/* show the content page */
	$result = $contentManager->getContentPage($cid);

	if (!($content_row = mysql_fetch_assoc($result))) {
		require(AT_INCLUDE_PATH.'header.inc.php');

		$msg->addError('PAGE_NOT_FOUND');
		$msg->printAll();

		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */
		
	/* the "heading navigation": */
	$path	= $contentManager->getContentPath($cid);

	if ($content_row['content_path']) {
		$content_base_href .= $content_row['content_path'].'/';
	}

	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->printAll();

	/* show the enable editor tool top if the editor is currently disabled */
	if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['prefs'][PREF_EDIT] !=1) ) {
		$help = array('ENABLE_EDITOR', $_my_uri);
		$msg->printHelps($help);
		unset($help);
	}

	save_last_cid($cid);
	$parent_headings = '';
	$num_in_path = count($path)-1;
	for ($i=0; $i<$num_in_path; $i++) {
		$content_info = $path[$i];
		$h = ($i>5) ? 6 : $i+1;
		$parent_headings .= '<h'.$h.'>';
		$parent_headings .= '<a href="'.$_base_href.'index.php?cid='.$content_info['content_id'].SEP.'g=11">';
		if ($_SESSION['prefs'][PREF_NUMBERING]) {
			if ($contentManager->_menu_info[$content_info['content_id']]['content_parent_id'] == 0) {
				$top_num = $contentManager->_menu_info[$content_info['content_id']]['ordering'];
				$parent_headings .= $top_num;
			} else {
				$top_num = $top_num.'.'.$contentManager->_menu_info[$content_info['content_id']]['ordering'];
				$parent_headings .= $top_num;
			}
			$parent_headings .= ' ';
		}
		$parent_headings .= $content_info['title'].'</a>'."\n";
		$parent_headings .= '</h'.$h.'>'."\n";
	}

	if ($_SESSION['prefs'][PREF_HEADINGS] && ($parent_headings != '')) {
		echo $parent_headings;
		echo '<hr />'."\n";
	}

	/* the page title: */
	echo '<h2>';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/icons/default/square-large-content.gif" width="42" height="40" hspace="3" vspace="3" class="menuimage" border="0" alt="" />';
	}

	if ($_SESSION['prefs'][PREF_NUMBERING]) {
		if ($top_num != '') {
			$top_num = $top_num.'.'.$content_row['ordering'];
			echo $top_num.' ';
		} else {
			$top_num = $content_row['ordering'];
			echo $top_num.' ';
		}
	}

	echo AT_print($content_row['title'], 'content.title');
	if ((	($content_row['r_date'] <= $content_row['n_date'])
			&& ((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
				|| ($_SESSION['packaging'] == 'all'))
		) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		echo '<small><small> ( <img src="'.$_base_path.'images/download.gif" height="24" width="20" class="menuimage14" alt="'._AT('export_content').'" /><a href="'.$_base_path.'tools/ims/ims_export.php?cid='.$cid.SEP.'g=27">'._AT('export_content').'</a> )</small></small>';
	}
	echo '</h2>';
	echo '<br />';
	/* TOC: */
	if ($_SESSION['prefs'][PREF_TOC] != NONE) {
		ob_start();

		$contentManager->printTOCMenu($cid, $top_num);
		$content_stuff = ob_get_contents();

		ob_end_clean();

		if ($content_stuff != '') {
			$content_stuff = '<p class="toc">'._AT('contents').':<br />'.$content_stuff.'</p>';
		}
	}

	/* TOC: */
	if (($content_stuff != '') && ($_SESSION['prefs'][PREF_TOC] == TOP)) {
		echo $content_stuff;
	}

	unset($editors);
	$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('edit_page'), 'url' => $_base_path.'editor/edit_content.php?cid='.$cid);
	$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('delete_page'), 'url' => $_base_path.'editor/delete_content.php?cid='.$cid);
	$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('sub_page'), 'url' => $_base_path.'editor/edit_content.php?pid='.$cid);
	$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('import_content_package'), 'url' => $_base_path.'tools/ims/index.php?cid='.$cid);
	print_editor($editors , $large = true);

	/* if i'm an admin then let me see content, otherwise only if released */
	if ($contentManager->isReleased($cid) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		if ($content_row['text'] == '') {
			$msg->addInfo('NO_PAGE_CONTENT');
			$msg->printAll();
		} else {
			if (!$contentManager->isReleased($cid)) {
				/* show the instructor that this content hasn't been released yet */
				$infos = array('NOT_RELEASED', AT_date(_AT('announcement_date_format'), $content_row['r_date'], AT_DATE_MYSQL_TIMESTAMP_14));
				$msg->addInfo($infos);
				$msg->printAll();
				unset($infos);
			}

			/* @See: include/lib/format_content.inc.php */
			echo '<div class="content_text">';
			echo format_content($content_row['text'], $content_row['formatting'], $glossary);
			echo '</div>';
		}
	} else {
		$infos = array('NOT_RELEASED', '<small>('._AT('release_date').': '.$content_row['release_date'].')</small>');
		$msg->addInfo($infos);
		$msg->printAll();
		unset($infos);
	}

	/* TOC: */
	if ($_SESSION['prefs'][PREF_TOC] == BOTTOM) {
		echo '<br /><br />';
		echo $content_stuff;
	}

	echo '<br /><br /><small><small class="spacer">'._AT('page_info', AT_date(_AT('inbox_date_format'), $content_row['last_modified'], AT_DATE_MYSQL_DATETIME), $content_row['revision'], AT_date(_AT('inbox_date_format'), $content_row['release_date'], AT_DATE_MYSQL_DATETIME)).'</small></small>';	

	require (AT_INCLUDE_PATH.'footer.inc.php');
?>