<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 3535 2005-02-25 16:23:38Z shozubq $

define('AT_INCLUDE_PATH', 'include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

$cid = intval($_GET['cid']);

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

$parent_headings = '';
$num_in_path = count($path)-1;

/* the page title: */

$page_title = '';

$page_title .= $content_row['title'];

$num_in_path = count($path)-1;
for ($i=0; $i<$num_in_path; $i++) {
	$content_info = $path[$i];
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
}

if ($_SESSION['prefs'][PREF_NUMBERING]) {
	if ($top_num != '') {
		$top_num = $top_num.'.'.$content_row['ordering'];
		$page_title .= $top_num.' ';
	} else {
		$top_num = $content_row['ordering'];
		$page_title .= $top_num.' ';
	}
}

$parent = 0;
foreach ($path as $page) {
	if (!$parent) {
		$_pages['content.php?cid='.$page['content_id']]['title']    = $page['title'];
		$_pages['content.php?cid='.$page['content_id']]['parent']   = 'index.php';
	} else {
		$_pages['content.php?cid='.$page['content_id']]['title']    = $page['title'];
		$_pages['content.php?cid='.$page['content_id']]['parent']   = 'content.php?cid='.$parent;
	}

	$parent = $page['content_id'];
}

$last_page = array_pop($_pages);
$_pages['content.php'] = $last_page;

require(AT_INCLUDE_PATH.'header.inc.php');

/* show the enable editor tool top if the editor is currently disabled */
/*
if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['prefs'][PREF_EDIT] !=1) ) {
	$help = array('ENABLE_EDITOR', $_my_uri);
	$msg->printHelps($help);
	unset($help);
}
*/

save_last_cid($cid);

/*
if ((	($content_row['r_date'] <= $content_row['n_date'])
		&& ((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
			|| ($_SESSION['packaging'] == 'all'))
	) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	echo '<small><small> ( <img src="'.$_base_path.'images/download.gif" height="24" width="20" class="menuimage14" alt="'._AT('export_content').'" /><a href="'.$_base_path.'tools/ims/ims_export.php?cid='.$cid.SEP.'g=27">'._AT('export_content').'</a> )</small></small>';
}
echo '</h2>';
*/

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

/*
unset($editors);
$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('edit_page'), 'url' => $_base_path.'editor/edit_content.php?cid='.$cid);
$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('delete_page'), 'url' => $_base_path.'editor/delete_content.php?cid='.$cid);
$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('sub_page'), 'url' => $_base_path.'editor/edit_content.php?pid='.$cid);
$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('import_content_package'), 'url' => $_base_path.'tools/ims/index.php?cid='.$cid);
print_editor($editors , $large = true);
*/

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