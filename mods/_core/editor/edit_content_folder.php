<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/editor/editor_tab_functions.inc.php');

if (isset($_GET['cid'])) $cid = intval($_GET['cid']);
if (isset($_GET['pid'])) $pid = intval($_GET['pid']);

if ($cid > 0)
{
	$content_array = $contentManager->getContentPage($cid);
	// This is a hack, getContentPage() only outputs a multi-array, 
	// needed to display content pages. A single row is needed here.
    foreach($content_array as $content_sub){
        $content_row = $content_sub;
    }
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
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
        

        function clean_title($title){
            //strip any bad stuff off the title
            $title= htmlspecialchars_decode($title);
            // This might be problematic for multi sentence title?
            $title = preg_replace_callback('/([\?^\s])(.*)/', function ($str) {
                return str_replace(array("'", '"', "&quot;"), '', $str[0]);    
                }, $title);
            $title = preg_replace('/<(.*?)>(.*?)<(.*?)>/','',$title );
            $title = preg_replace('/>/','',$title );
            $title = preg_replace('/\"\'/','',$title );
            $title	= htmlspecialchars($title);
            return $title;
        }

        $_POST['title'] =  clean_title($_POST['title']);

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
			                                    $content_row['allow_test_export'],
                                                CONTENT_TYPE_FOLDER);
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
		$sql = "DELETE FROM %scontent_prerequisites 
		         WHERE content_id=%d AND type='".CONTENT_PRE_TEST."'";
		$result = queryDB($sql, array(TABLE_PREFIX, $cid));
		
		if (is_array($_POST['tid']) && sizeof($_POST['tid']) > 0)
		{
			foreach ($_POST['tid'] as $i => $tid){
				$tid = intval($tid);
				$sql = "INSERT INTO %scontent_prerequisites 
				           SET content_id=%d, type='".CONTENT_PRE_TEST."', item_id=%d";
				$result = queryDB($sql, array(TABLE_PREFIX, $cid, $tid));
			}
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_base_path.'mods/_core/editor/edit_content_folder.php?cid='.$cid);
		exit;
	}
}

if ($cid > 0)
{ // edit existing content folder
	if (!$content_row) {
		$_pages['mods/_core/editor/edit_content_folder.php']['title_var'] = 'missing_content';
		$_pages['mods/_core/editor/edit_content_folder.php']['parent']    = 'index.php';
		$_pages['mods/_core/editor/edit_content_folder.php']['ignore']	= true;

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
//	foreach ($path as $i=>$page) {
//		if (!$parent) {
//			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['title']    = $page['content_number'] . $page['title'];
//			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['parent']   = 'index.php';
//		} else {
//			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['title']    = $page['content_number'] . $page['title'];
//			$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['parent']   = 'editor/edit_content_folder.php?cid='.$parent;
//		}
//	
//		$_pages['editor/edit_content_folder.php?cid='.$page['content_id']]['ignore'] = true;
//		$parent = $page['content_id'];
//	}
//	$last_page = array_pop($_pages);
//	$_pages['editor/edit_content_folder.php'] = $last_page;
	
	reset($path);
	$first_page = current($path);
	
	save_last_cid($cid);
	
	if (isset($top_num) && $top_num != (int) $top_num) {
		$top_num = substr($top_num, 0, strpos($top_num, '.'));
	}
	
	// used by header.inc.php
	$_tool_shortcuts = $contentManager->getToolShortcuts($content_row);
	$release_date = $content_row['release_date'];

	// display pre-tests
	$sql = "SELECT * FROM %scontent_prerequisites WHERE content_id=%d AND type='".CONTENT_PRE_TEST."'";
	$rows = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['cid']));	
	foreach($rows as $row){
	    $_POST['pre_tid'][] = $row['item_id'];
	}

	$savant->assign('ftitle', $content_row['title']);
	$savant->assign('shortcuts', $shortcuts);
	$savant->assign('cid', $cid);
}

// display pre-tests
// get a list of all the tests we have, and links to create, edit, delete, preview 

$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue 
             FROM %stests 
            WHERE course_id=%d 
            ORDER BY start_date DESC";
$rows	= queryDB($sql, array(TABLE_PREFIX,$_SESSION['course_id']));
$num_tests = count($rows);
$i = 0;

foreach($rows as $row)
{
	$results[$i]['test_id'] = $row['test_id'];
	$results[$i]['title'] = $row['title'];
	
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		$results[$i]['status'] = '<strong>'._AT('ongoing').'</strong>';
	} else if ($row['ue'] < time() ) {
		$results[$i]['status'] = '<strong>'._AT('expired').'</strong>';
	} else if ($row['us'] > time() ) {
		$results[$i]['status'] = '<strong>'._AT('pending').'</strong>';
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
	$sql_sub = "SELECT COUNT(*) AS sub_cnt FROM %stests_results WHERE status=1 AND test_id=%d";
	$row_sub	= queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);

	$results[$i]['submissions'] = $row_sub['sub_cnt'].' '._AT('submissions').', ';

	//get # submissions
	$sql_sub = "SELECT COUNT(*) AS marked_cnt FROM %stests_results WHERE status=1 AND test_id=%d AND final_score=''";
	$row_sub	= queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);
		
	$results[$i]['submissions'] .= $row_sub['marked_cnt'].' '._AT('unmarked');

	//get assigned groups

	$sql_sub = "SELECT G.title FROM %sgroups G INNER JOIN %stests_groups T USING (group_id) WHERE T.test_id=%d";
	$rows_sub	= queryDB($sql_sub, array(TABLE_PREFIX, TABLE_PREFIX, $row['test_id']));
	$rows_sub_count = count($rows_sub);
	if ($result_sub_count == 0) {
		$results[$i]['assign_to'] = _AT('everyone');
	} else {
		$results[$i]['assign_to'] = $row_sub['title'];
		foreach($rows_sub as $row_sub){
		    $results[$i]['assign_to'] .= ', '.$row_sub['title'];
		}
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

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('editor/edit_content_folder.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>