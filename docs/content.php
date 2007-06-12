<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$cid = $_GET['cid'] = intval($_GET['cid']);

if ($cid == 0) {
	header('Location: '.$_base_href.'index.php');
	exit;
}

/* show the content page */
$result = $contentManager->getContentPage($cid);

if (!($content_row = mysql_fetch_assoc($result))) {
	$_pages['content.php']['title_var'] = 'missing_content';
	$_pages['content.php']['parent']    = 'index.php';
	$_pages['content.php']['ignore']	= true;


	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->addError('PAGE_NOT_FOUND');
	$msg->printAll();

	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} /* else: */

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

/* the "heading navigation": */
$path	= $contentManager->getContentPath($cid);

if ($content_row['content_path']) {
	$content_base_href = $content_row['content_path'].'/';
}

$parent_headings = '';
$num_in_path = count($path)-1;

/* the page title: */
$page_title = '';
$page_title .= $content_row['title'];

$num_in_path = count($path)-1;
for ($i=0; $i<$num_in_path; $i++) {
	$content_info = $path[$i];
	if ($_SESSION['prefs']['PREF_NUMBERING']) {
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

if ($_SESSION['prefs']['PREF_NUMBERING']) {
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

	$_pages['content.php?cid='.$page['content_id']]['ignore'] = true;
	$parent = $page['content_id'];
}

$last_page = array_pop($_pages);
$_pages['content.php'] = $last_page;

reset($path);
$first_page = current($path);

// use any styles that were part of the imported document
// $_custom_css = $_base_href.'headstuff.php?cid='.$cid.SEP.'path='.urlEncode($_base_href.$course_base_href.$content_base_href);

require(AT_INCLUDE_PATH.'header.inc.php');

save_last_cid($cid);
if (isset($top_num) && $top_num != (int) $top_num) {
	$top_num = substr($top_num, 0, strpos($top_num, '.'));
}

$shortcuts = array();
if ((	($content_row['r_date'] <= $content_row['n_date'])
		&& ((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
			|| ($_SESSION['packaging'] == 'all'))
	) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {

	$shortcuts[] = array('title' => _AT('export_content'), 'url' => $_base_href . 'tools/ims/ims_export.php?cid='.$cid);
}

if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	$shortcuts[] = array('title' => _AT('edit_this_page'),   'url' => $_base_href . 'editor/edit_content.php?cid='.$cid);
	$shortcuts[] = array('title' => _AT('add_top_page'),     'url' => $_base_href . 'editor/edit_content.php');
	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$shortcuts[] = array('title' => _AT('add_sibling_page'), 'url' => $_base_href .
			'editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id']);
	}
	$shortcuts[] = array('title' => _AT('add_sub_page'),     'url' => $_base_href . 'editor/edit_content.php?pid='.$cid);
	$shortcuts[] = array('title' => _AT('delete_this_page'), 'url' => $_base_href . 'editor/delete_content.php?cid='.$cid);
}
$savant->assign('shortcuts', $shortcuts);

/* if i'm an admin then let me see content, otherwise only if released */
$released_status = $contentManager->isReleased($cid);
if ($released_status === TRUE || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	if ($content_row['text'] == '') {
		$msg->addInfo('NO_PAGE_CONTENT');
		$msg->printAll();
		$savant->assign('body', '');
	} else {
		if ($released_status !== TRUE) {
			/* show the instructor that this content hasn't been released yet */
			$infos = array('NOT_RELEASED', AT_date(_AT('announcement_date_format'), $released_status, AT_DATE_UNIX_TIMESTAMP));
			$msg->addInfo($infos);
			$msg->printAll();
			unset($infos);
		}

		/* @See: include/lib/output.inc.php */
		$savant->assign('body', format_content($content_row['text'], $content_row['formatting'], $glossary));
	}
} else {
	$infos = array('NOT_RELEASED', AT_date(_AT('announcement_date_format'), $released_status, AT_DATE_UNIX_TIMESTAMP));
	$msg->addInfo($infos);
	$msg->printAll();
	unset($infos);
}

$savant->assign('content_info', _AT('page_info', AT_date(_AT('inbox_date_format'), $content_row['last_modified'], AT_DATE_MYSQL_DATETIME), $content_row['revision'], AT_date(_AT('inbox_date_format'), $content_row['release_date'], AT_DATE_MYSQL_DATETIME)));

$savant->display('content.tmpl.php');

require (AT_INCLUDE_PATH.'footer.inc.php');
?>