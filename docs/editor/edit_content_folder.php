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
require(AT_INCLUDE_PATH.'lib/editor_tab_functions.inc.php');

if (isset($_GET['cid'])) $cid = intval($_GET['cid']);
if (isset($_GET['pid'])) $pid = intval($_GET['pid']);

if ($cid > 0)
{
	$result = $contentManager->getContentPage($cid);
	$content_row = mysql_fetch_assoc($result);
}

// save changes
if ($_POST['submit'])
{
	if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
		
	if (!($release_date = generate_release_date())) {
		$msg->addError('BAD_DATE');
	}
	
	if (!$msg->containsErrors()) 
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
			                                    $release_date, 
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
			
			$cid = $contentManager->addContent($_SESSION['course_id'],
			                                   $pid,
			                                   $ordering,
			                                   $_POST['title'],
			                                   '',
			                                   '',
			                                   '',
			                                   0,
			                                   $release_date,
			                                   '',
			                                   0,
			                                   '',
			                                   1,
			                                   CONTENT_TYPE_FOLDER);
		}
		
		// save pre-tests
		$sql = "DELETE FROM ". TABLE_PREFIX . "content_prerequisites 
		         WHERE content_id=".$cid." AND type='".CONTENT_PRE_TEST."'";
		$result = mysql_query($sql, $db);
		
		if (is_array($_POST['tid']) && sizeof($_POST['tid']) > 0)
		{
			foreach ($_POST['tid'] as $i => $tid){
				$tid = intval($tid);
				$sql = "INSERT INTO ". TABLE_PREFIX . "content_prerequisites 
				           SET content_id=".$cid.", type='".CONTENT_PRE_TEST."', item_id=$tid";
				$result = mysql_query($sql, $db);

				if ($result===false) $msg->addError('MYSQL_FAILED');
			}
		}
	}
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_base_path.'editor/edit_content_folder.php?cid='.$cid);
	exit;
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

	$release_date = $content_row['release_date'];

	// display pre-tests
	$sql = 'SELECT * FROM '.TABLE_PREFIX."content_prerequisites WHERE content_id=$_REQUEST[cid] AND type='".CONTENT_PRE_TEST."'";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$_POST['pre_tid'][] = $row['item_id'];
	}

	$savant->assign('ftitle', $content_row['title']);
	$savant->assign('shortcuts', $shortcuts);
	$savant->assign('cid', $cid);
}

// display pre-tests
// get a list of all the tests we have, and links to create, edit, delete, preview 
$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue 
             FROM ".TABLE_PREFIX."tests 
            WHERE course_id=$_SESSION[course_id] 
            ORDER BY start_date DESC";
$result	= mysql_query($sql, $db);
$num_tests = mysql_num_rows($result);

$i = 0;
while($row = mysql_fetch_assoc($result))
{
	$results[$i]['test_id'] = $row['test_id'];
	$results[$i]['title'] = $row['title'];
	
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		$results[$i]['status'] = '<em>'._AT('ongoing').'</em>';
	} else if ($row['ue'] < time() ) {
		$results[$i]['status'] = '<em>'._AT('expired').'</em>';
	} else if ($row['us'] > time() ) {
		$results[$i]['status'] = '<em>'._AT('pending').'</em>';
	} 

	$startend_date_format=_AT('startend_date_format'); 

	$results[$i]['availability'] = AT_date($startend_date_format, $row['start_date'], AT_DATE_MYSQL_DATETIME). ' ' ._AT('to_2').' ';
	$results[$i]['availability'] .= AT_date($startend_date_format, $row['end_date'], AT_DATE_MYSQL_DATETIME);
	
	// get result release
	if ($row['result_release'] == AT_RELEASE_IMMEDIATE)
		$results[$i]['result_release'] = _AT('release_immediate');
	else if ($row['result_release'] == AT_RELEASE_MARKED)
		$results[$i]['result_release'] = _AT('release_marked');
	else if ($row['result_release'] == AT_RELEASE_NEVER)
		$results[$i]['result_release'] = _AT('release_never');
		
	//get # marked submissions
	$sql_sub = "SELECT COUNT(*) AS sub_cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$row['test_id'];
	$result_sub	= mysql_query($sql_sub, $db);
	$row_sub = mysql_fetch_assoc($result_sub);
	$results[$i]['submissions'] = $row_sub['sub_cnt'].' '._AT('submissions').', ';

	//get # submissions
	$sql_sub = "SELECT COUNT(*) AS marked_cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$row['test_id']." AND final_score=''";
	$result_sub	= mysql_query($sql_sub, $db);
	$row_sub = mysql_fetch_assoc($result_sub);
	$results[$i]['submissions'] .= $row_sub['marked_cnt'].' '._AT('unmarked');

	//get assigned groups
	$sql_sub = "SELECT G.title FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."tests_groups T USING (group_id) WHERE T.test_id=".$row['test_id'];
	$result_sub	= mysql_query($sql_sub, $db);
	if (mysql_num_rows($result_sub) == 0) {
		$results[$i]['assign_to'] = _AT('everyone');
	} else {
		$row_sub = mysql_fetch_assoc($result_sub);
		$results[$i]['assign_to'] = $row_sub['title'];
		do {
			$results[$i]['assign_to'] .= ', '.$row_sub['title'];
		} while ($row_sub = mysql_fetch_assoc($result_sub));
	}
	
	if ($row['passscore'] == 0 && $row['passpercent'] == 0)
		$results[$i]['pass_score'] = _AT('no_pass_score');
	else if ($row['passscore'] <> 0)
		$results[$i]['pass_score'] = $row['passscore'];
	else if ($row['passpercent'] <> 0)
		$results[$i]['pass_score'] = $row['passpercent'].'%';
		
	$i++;
}

if (isset($results)) $savant->assign('pretests', $results);

// set release date
if (!isset($release_date)) $release_date = date('Y-m-d H:i:s');

$_POST['day']   = substr($release_date, 8, 2);
$_POST['month'] = substr($release_date, 5, 2);
$_POST['year']  = substr($release_date, 0, 4);
$_POST['hour']  = substr($release_date, 11, 2);
$_POST['min']= substr($release_date, 14, 2);

if ($pid > 0) $savant->assign('pid', $pid);
$savant->display('editor/edit_content_folder.tmpl.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>