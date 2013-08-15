<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_CATEGORIES);

if (isset($_POST['delete'], $_POST['cat_id'])) {
	header('Location: delete_category.php?cat_id='.$_POST['cat_id']);
	exit;
} else if (isset($_POST['edit'], $_POST['cat_id'])) {
	header('Location: edit_category.php?cat_id='.$_POST['cat_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql = "SELECT * FROM %scourse_cats ORDER BY cat_name";
$rows_cats = queryDB($sql, array(TABLE_PREFIX));

$categories = array();
foreach($rows_cats as $row){
	if ($row['cat_parent']) {
		$sql_cat = "SELECT cat_name FROM %scourse_cats WHERE cat_id=%d";
		$row_cat= queryDB($sql_cat, array(TABLE_PREFIX, $row['cat_parent']), TRUE);
		$row['parent_cat_name'] = $row_cat['cat_name'];
	} else {
		$row['parent_cat_name'] = '';
	}
	$categories[] = $row;
}

$savant->assign('categories', $categories);
$savant->display('admin/courses/course_categories.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 

?>