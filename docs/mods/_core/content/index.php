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
authenticate(AT_PRIV_CONTENT);

global $contentManager;

if (isset($_GET['edit'], $_GET['ctid'])) {
	$cid = intval($_GET['ctid']);
	$result = $contentManager->getContentPage($cid);
	$row = mysql_fetch_assoc($result);
	
	if ($row['content_type'] == CONTENT_TYPE_CONTENT || $row['content_type'] == CONTENT_TYPE_WEBLINK) {
		header('Location: '.AT_BASE_HREF.'mods/_core/editor/edit_content.php?cid='.$cid);
	} else if ($row['content_type'] == CONTENT_TYPE_FOLDER) {
		header('Location: '.AT_BASE_HREF.'mods/_core/editor/edit_content_folder.php?cid='.$cid);
	}
	exit;
} else if (isset($_GET['delete'], $_GET['ctid'])) {
	header('Location: '.AT_BASE_HREF.'mods/_core/editor/delete_content.php?cid='.intval($_GET['ctid']));
	exit;
} else if (isset($_GET['view'], $_GET['ctid'])) {
	$cid = intval($_GET['ctid']);
	$result = $contentManager->getContentPage($cid);
	$row = mysql_fetch_assoc($result);

    if ($row['content_type'] == CONTENT_TYPE_CONTENT || $row['content_type'] == CONTENT_TYPE_WEBLINK) {
		header('Location: '.AT_BASE_HREF.'content.php?cid='.intval($_GET['ctid']));
    } else if ($row['content_type'] == CONTENT_TYPE_FOLDER) {
		header('Location: '.AT_BASE_HREF.'mods/_core/editor/edit_content_folder.php?cid='.$cid);
    }
	exit;
} else if (isset($_GET['usage'], $_GET['ctid'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/tracker/tools/page_student_stats.php?content_id='.intval($_GET['ctid']));
	exit;
} else if (!isset($_GET['ctid']) && !isset($_GET['sub_content']) && (isset($_GET['usage']) || isset($_GET['view']) || isset($_GET['delete']) || isset($_GET['edit']))) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'content_parent_id, ordering';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

if (!isset($_GET['sub_content'])) {
	$parent_id = 0;	
} else {
	$parent_id = intval($_GET['ctid']);
}


$all_content = $contentManager->getContent();

$content = $all_content[$parent_id];

function print_select($pid, $depth) {
	global $all_content;

	if (!isset($all_content[$pid])) {
		return;
	}

	foreach ($all_content[$pid] as $row) {
		if (isset($all_content[$row['content_id']])) {
			echo '<option value="'.$row['content_id'].'"';
			if ($_GET['ctid'] == $row['content_id']) {
				echo ' selected="selected"';
			}
			echo '>';
			echo str_repeat('&nbsp;', $depth * 5);
			echo $row['title'].'</option>';

			print_select($row['content_id'], $depth+1);
		}
	}
}
$savant->assign('all_content', $all_content);
$savant->assign('content', $content);
$savant->display('instructor/content/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>