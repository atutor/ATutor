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

define('AT_INCLUDE_PATH', 'include/');
	$_section = 'Home';

	require(AT_INCLUDE_PATH.'vitals.inc.php');

	if (!$cid) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		print_feedback($feedback);

		/* show the enable editor tool top if the editor is currently disabled */
		if ($_SESSION['is_admin'] && ($_SESSION['prefs'][PREF_EDIT] !=1) ) {
			$help[] = array(AT_HELP_ENABLE_EDITOR, $_my_uri);
			print_help($help);
			$help=array();
		}

		require(AT_INCLUDE_PATH.'html/announcements.inc.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */

	//require(AT_INCLUDE_PATH.'lib/format_content.inc.php');
	/* show the content page */
	$result = $contentManager->getContentPage($cid);
	require(AT_INCLUDE_PATH.'lib/format_content.inc.php');

	if (!($content_row = mysql_fetch_assoc($result))) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		print_feedback($feedback); /* unlikely to need it */
		$errors[] = AT_ERROR_PAGE_NOT_FOUND;
		print_errors($errors);
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */
		
	/* the "heading navigation": */
	$path	= $contentManager->getContentPath($cid);

	$course_base_href = 'content/'.$_SESSION['course_id'].'/';
	if ($content_row['content_path']) {
		$content_base_href .= $content_row['content_path'].'/';
	}
	require(AT_INCLUDE_PATH.'header.inc.php');

	print_feedback($feedback);

	/* show the enable editor tool top if the editor is currently disabled */
	if ($_SESSION['is_admin'] && ($_SESSION['prefs'][PREF_EDIT] !=1) ) {
		$help[] = array(AT_HELP_ENABLE_EDITOR, $_my_uri);
		print_help($help);
		$help=array();
	}

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
		) || $_SESSION['is_admin']) {
		echo '<small> ( <img src="'.$_base_path.'images/download.gif" height="24" width="20" class="menuimage14" alt="'._AT('export_content').'" /><a href="'.$_base_path.'tools/ims/ims_export.php?cid='.$cid.SEP.'g=27">'._AT('export_content').'</a> )</small>';
	}
	echo '</h2>';
	echo '<br />';
	/* TOC: */
	if ($_SESSION['prefs'][PREF_TOC] != NONE) {
		ob_start();

		//$p = $contentManager->getContent();
		$contentManager->printSubMenu($cid, $top_num);
		$content_stuff = ob_get_contents();

		ob_end_clean();

		if ($content_stuff != '') {
			$content_stuff = '<p class="toc">'._AT('contents').':<br />'.$content_stuff.'</p>';
		}
	}

	/* TOC: */
	if (($content_stuff != '') && ($_SESSION['prefs'][PREF_TOC] == TOP)) {
		echo '<br />'.$content_stuff;
		echo '<br />';
	}

	print_editorlg( _AT('edit_page'), $_base_path.'editor/edit_content.php?cid='.$cid, _AT('delete_page'), $_base_path.'editor/delete_content.php?cid='.$cid, _AT('sub_page') , $_base_path.'editor/add_new_content.php?pid='.$cid);

	/* if i'm an admin then let me see content, otherwise only if released */
	if (($content_row['r_date'] <= $content_row['n_date']) || $_SESSION['is_admin']) {
		if ($content_row['text'] == '') {
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);
		} else {
			if ($content_row['r_date'] > $content_row['n_date']) {
				$infos[] = array(AT_INFOS_NOT_RELEASED, AT_date(_AT('announcement_date_format'), $content_row['r_date'], AT_DATE_MYSQL_TIMESTAMP_14));
				print_infos($infos);
			}

			/* @See: include/lib/format_content.inc.php */

			echo format_content($content_row['text'], $content_row['formatting']);
			
			echo '<br /><br /><small class="spacer">' 
				. _AT('last_modified') . ': '.AT_date(_AT('inbox_date_format'), $content_row['last_modified'], AT_DATE_MYSQL_DATETIME) 
				. '. ' . _AT('revision').': '.$content_row['revision'] 
				. '. ' . _AT('release_date').': '.AT_date(_AT('inbox_date_format'), $content_row['release_date'], AT_DATE_MYSQL_DATETIME);
			echo '</small>'."\n";
		}
	} else {
		$errors[]=array(AT_ERROR_NOT_RELEASED, '<small>('._AT('release_date').': '.$content_row['release_date'].')</small>');
		print_errors($errors);
	}

	/* TOC: */
	if ($_SESSION['prefs'][PREF_TOC] == BOTTOM) {
		echo '<br />';
		echo $content_stuff;
	}

	require (AT_INCLUDE_PATH.'footer.inc.php');
?>