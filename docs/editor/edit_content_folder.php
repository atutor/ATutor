<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: content.php 8784 2009-09-04 20:02:32Z cindy $
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['cid'])) $cid = intval($_GET['cid']);
if (isset($_GET['pid'])) $pid = intval($_GET['pid']);

if ($cid > 0)
{
	$result = $contentManager->getContentPage($cid);
	$content_row = mysql_fetch_assoc($result);
}

if ($cid > 0)
{ // edit existing content folder
	if (!$content_row) {
		$_pages['editor/edit_content_folder.php']['title_var'] = 'missing_content';
		$_pages['editor/edit_content_folder.php']['parent']    = 'index.php';
		$_pages['editor/edit_content_folder.php']['ignore']	= true;

		require(AT_INCLUDE_PATH.'header.inc.php');
	
		$msg->addError('PAGE_NOT_FOUND');
		$msg->printAll();
	
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} /* else: */
	
	/* the "heading navigation": */
	$path	= $contentManager->getContentPath($cid);
	
	if ($content_row['content_path']) {
		$content_base_href = $content_row['content_path'].'/';
	}
	
	$parent_headings = '';
	$num_in_path = count($path);
	
	/* the page title: */
	$page_title = '';
	$page_title .= $content_row['title'];
	
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
			if ($_SESSION['prefs']['PREF_NUMBERING']) {
				$path[$i]['content_number'] = $top_num . ' ';
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
	foreach ($path as $i=>$page) {
		if (!$parent) {
			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['title']    = $page['content_number'] . $page['title'];
			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['parent']   = 'index.php';
		} else {
			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['title']    = $page['content_number'] . $page['title'];
			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['parent']   = 'editor/edit_content_folder.php?cid='.$parent;
		}
	
		$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['ignore'] = true;
		$parent = $page['content_id'];
	}
	$last_page = array_pop($_pages);
	$_pages['editor/edit_content_folder.php'] = $last_page;
	
	reset($path);
	$first_page = current($path);
	
	save_last_cid($cid);
	
	if (isset($top_num) && $top_num != (int) $top_num) {
		$top_num = substr($top_num, 0, strpos($top_num, '.'));
	}
	
	$shortcuts = array();
	if (((!$content_row['content_parent_id'] && $_SESSION['packaging'] == 'top') || $_SESSION['packaging'] == 'all') 
	    || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		$shortcuts[] = array('title' => _AT('export_content'), 'url' => $_base_href . 'tools/ims/ims_export.php?cid='.$cid);
	}
	
	if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		$shortcuts[] = array('title' => _AT('add_top_folder'),   'url' => $_base_href . 'editor/edit_content_folder.php');
	
		if ($contentManager->_menu_info[$cid]['content_parent_id']) {
			$shortcuts[] = array('title' => _AT('add_sibling_folder'), 'url' => $_base_href .
				'editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id']);
		}
		
		$shortcuts[] = array('title' => _AT('add_sub_folder'),   'url' => $_base_href . 'editor/edit_content_folder.php?pid='.$cid);
		
		$shortcuts[] = array('title' => _AT('add_top_page'),     'url' => $_base_href . 'editor/edit_content.php');
		if ($contentManager->_menu_info[$cid]['content_parent_id']) {
			$shortcuts[] = array('title' => _AT('add_sibling_page'), 'url' => $_base_href .
				'editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id']);
		}
	
		$shortcuts[] = array('title' => _AT('add_sub_page'),     'url' => $_base_href . 'editor/edit_content.php?pid='.$cid);
		$shortcuts[] = array('title' => _AT('delete_this_page'), 'url' => $_base_href . 'editor/delete_content.php?cid='.$cid);
	}
	$savant->assign('shortcuts', $shortcuts);
	$savant->assign('ftitle', $content_row['title']);
	$savant->assign('cid', $cid);
}
//debug($contentManager->getContent($pid));
// save changes
if ($_POST['submit'])
{
	$_POST['title']	= $content_row['title'] = $addslashes($_POST['title']);

	if ($cid > 0)
	{ // edit existing content
		$err = $contentManager->editContent($cid, 
		                                    $_POST['title'], 
		                                    '', 
		                                    '', 
		                                    '', 
		                                    $content_row['formatting'], 
		                                    $content_row['release_date'], 
		                                    '', 
		                                    $content_row['use_customized_head'], 
		                                    '', 
		                                    $content_row['allow_test_export']);
	}
	else
	{ // add new content
		// find out ordering and content_parent_id
		if ($pid)
		{ // insert sub content folder
			$ordering = count($contentManager->getContent($pid))+1;
		}
		else
		{ // insert a top content folder
			$ordering = count($contentManager->getContent(0)) + 1;
			$pid = 0;
		}
		
//		debug($ordering);exit;
		$cid = $contentManager->addContent($_SESSION['course_id'],
		                                   $pid,
		                                   $ordering,
		                                   $_POST['title'],
		                                   '',
		                                   '',
		                                   '',
		                                   0,
		                                   date('Y-m-d H:i:s'),
		                                   '',
		                                   0,
		                                   '',
		                                   1,
		                                   CONTENT_TYPE_FOLDER);
	}
//	header('Location: editor/edit_content_folder.php?cid='.$cid);
//	debug($_base_path.'edit_content_folder.php?cid='.$cid);
	header('Location: '.$_base_path.'editor/edit_content_folder.php?cid='.$cid);
}

if ($pid > 0) $savant->assign('pid', $pid);
$savant->display('editor/edit_content_folder.tmpl.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require (AT_INCLUDE_PATH.'footer.inc.php');
?>