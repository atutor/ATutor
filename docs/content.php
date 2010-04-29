<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
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
	// When login is a student, remove content folder from breadcrumb path as content folders are
	// just toggles for students. Keep content folder in breadcrumb path for instructors as they
	// can edit content folder title. 
	if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && 
	    $contentManager->_menu_info[$page['content_id']]['content_type'] == CONTENT_TYPE_FOLDER) {
		unset($path[$i]);
		continue;
	}
	
	if ($contentManager->_menu_info[$page['content_id']]['content_type'] == CONTENT_TYPE_FOLDER)
		$content_url = 'mods/_core/editor/edit_content_folder.php?cid='.$page['content_id'];
	else
		$content_url = 'content.php?cid='.$page['content_id'];
		
	if (!$parent) {
		$_pages[$content_url]['title']    = $page['content_number'] . $page['title'];
		$_pages[$content_url]['parent']   = 'index.php';
	} else {
		$_pages[$content_url]['title']    = $page['content_number'] . $page['title'];
		$_pages[$content_url]['parent']   = 'mods/_core/editor/edit_content_folder.php?cid='.$parent;
	}

	$_pages[$content_url]['ignore'] = true;
	$parent = $page['content_id'];
}
$last_page = array_pop($_pages);
$_pages['content.php'] = $last_page;

reset($path);
$first_page = current($path);

/* the content test extension page */
$content_test_ids = array();	//the html
$content_test_rs = $contentManager->getContentTestsAssoc($cid);
while ($content_test_row = mysql_fetch_assoc($content_test_rs)){
	$content_test_ids[] = $content_test_row;
}

/*TODO***************BOLOGNA***************REMOVE ME**********/
/* the content forums extension page*/
$content_forum_ids = array();	//the html
$content_forum_rs = $contentManager->getContentForumsAssoc($cid);
while ($content_forum_row = mysql_fetch_assoc($content_forum_rs)){
	$content_forum_ids[] = $content_forum_row;
}

// use any styles that were part of the imported document
// $_custom_css = $_base_href.'headstuff.php?cid='.$cid.SEP.'path='.urlEncode($_base_href.$course_base_href.$content_base_href);

if ($content_row['use_customized_head'] && strlen($content_row['head']) > 0)
{
	$_custom_head .= $content_row['head'];
}

global $_custom_head;
$_custom_head .= '
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery(function() {
	jQuery(\'a.tooltip\').tooltip( { showBody: ": ", showURL: false } );
	} );
	//-->
	</script>
';

save_last_cid($cid);

if (isset($top_num) && $top_num != (int) $top_num) {
	$top_num = substr($top_num, 0, strpos($top_num, '.'));
}

$shortcuts = array();
if ((	($content_row['r_date'] <= $content_row['n_date'])
		&& ((!$content_row['content_parent_id'] && ($_SESSION['packaging'] == 'top'))
			|| ($_SESSION['packaging'] == 'all'))
	) || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {

	$shortcuts[] = array('title' => _AT('export_content'), 'url' => $_base_href . 'mods/_core/imscp/ims_export.php?cid='.$cid, 'icon' => $_base_href . 'images/download.png');
}

if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	$shortcuts[] = array('title' => _AT('edit_this_page'),   'url' => $_base_href . 'mods/_core/editor/edit_content.php?cid='.$cid, 'icon' => $_base_href . 'images/medit.gif');
	$shortcuts[] = array('title' => _AT('add_top_folder'),   'url' => $_base_href . 'mods/_core/editor/edit_content_folder.php', 'icon' => $_base_href . 'images/folder_new.gif');

	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$shortcuts[] = array('title' => _AT('add_sibling_folder'), 'url' => $_base_href .
			'mods/_core/editor/edit_content_folder.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'], 'icon' => $_base_href . 'images/folder_new_sibling.gif');
	}
	$shortcuts[] = array('title' => _AT('add_top_page'),     'url' => $_base_href . 'mods/_core/editor/edit_content.php', 'icon' => $_base_href . 'images/page_add.gif');
	if ($contentManager->_menu_info[$cid]['content_parent_id']) {
		$shortcuts[] = array('title' => _AT('add_sibling_page'), 'url' => $_base_href .
			'mods/_core/editor/edit_content.php?pid='.$contentManager->_menu_info[$cid]['content_parent_id'], 'icon' => $_base_href . 'images/page_add_sibling.gif');
	}
	$shortcuts[] = array('title' => _AT('delete_this_page'), 'url' => $_base_href . 'mods/_core/editor/delete_content.php?cid='.$cid, 'icon' => $_base_href . 'images/page_delete.gif');
}
$savant->assign('shortcuts', $shortcuts);

/* if i'm an admin then let me see content, otherwise only if released */
$released_status = $contentManager->isReleased($cid);

if ($released_status === TRUE || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	//if it has test and forum associated with it, still display it even if the content is empty
	if ($content_row['text'] == '' && (empty($content_test_ids) && empty($content_forum_ids))){
		$msg->addInfo('NO_PAGE_CONTENT');
		$savant->assign('body', '');
	} else {
		if ($released_status !== TRUE) {
			/* show the instructor that this content hasn't been released yet */
			$infos = array('NOT_RELEASED', AT_date(_AT('announcement_date_format'), $released_status, AT_DATE_UNIX_TIMESTAMP));
			$msg->addInfo($infos);
			unset($infos);
		}

		$pre_test_id = $contentManager->getPretest($cid);
		
		if (intval($pre_test_id) > 0)
		{
			if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
				$msg->addInfo('PRETEST');
			}
			else {
				header('Location: '.url_rewrite('mods/_standard/tests/test_intro.php?tid='.$pre_test_id.SEP.'cid='.$cid, AT_PRETTY_URL_IS_HEADER));
			}
		}
		
		// if one of the prerequisite test(s) has expired, student cannot view the content 
		if (intval($pre_test_id) != -1 || authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN))
		{
			// find whether the body has alternatives defined
			list($has_text_alternative, $has_audio_alternative, $has_visual_alternative, $has_sign_lang_alternative)
			= provide_alternatives($cid, $content_row['text'], true);
			
			// apply alternatives
			if (intval($_GET['alternative']) > 0) {
				$content = provide_alternatives($cid, $content_row['text'], false, intval($_GET['alternative']));
			} else {
				$content = provide_alternatives($cid, $content_row['text']);
			}
	        
			$content = format_content($content, $content_row['formatting'], $glossary);
	
			$content_array = get_content_table($content);
			
			$savant->assign('content_table', $content_array[0]);
			$savant->assign('body', $content_array[1]);
			$savant->assign('cid', $cid);
			$savant->assign('has_text_alternative', $has_text_alternative);
			$savant->assign('has_audio_alternative', $has_audio_alternative);
			$savant->assign('has_visual_alternative', $has_visual_alternative);
			$savant->assign('has_sign_lang_alternative', $has_sign_lang_alternative);
						
			//assign test pages if there are tests associated with this content page
			if (!empty($content_test_ids)){
				$savant->assign('test_message', $content_row['test_message']);
				$savant->assign('test_ids', $content_test_ids);
			} else {
				$savant->assign('test_message', '');
				$savant->assign('test_ids', array());
			}
	
	                /*TODO***************BOLOGNA***************REMOVE ME**********/
	                //assign forum pages if there are forums associated with this content page
			if (!empty($content_forum_ids)){
				$savant->assign('forum_message','');
				$savant->assign('forum_ids', $content_forum_ids);
			} else {
				$savant->assign('forum_message', '');
				$savant->assign('forum_ids', array());
			}
		}	
	}
} else {
	$infos = array('NOT_RELEASED', AT_date(_AT('announcement_date_format'), $released_status, AT_DATE_UNIX_TIMESTAMP));
	$msg->addInfo($infos);
	unset($infos);
}

$savant->assign('content_info', _AT('page_info', AT_date(_AT('inbox_date_format'), $content_row['last_modified'], AT_DATE_MYSQL_DATETIME), $content_row['revision'], AT_date(_AT('inbox_date_format'), $content_row['release_date'], AT_DATE_MYSQL_DATETIME)));

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->display('content.tmpl.php');

//save last visit page.
$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require (AT_INCLUDE_PATH.'footer.inc.php');
?>