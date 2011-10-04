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
$sql	= "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name";
$result = mysql_query($sql, $db);
//not working? 
$sql_cat	= "SELECT cat_name FROM ".TABLE_PREFIX."course_cats WHERE cat_id=".$row['cat_parent'];
$result_cat = mysql_query($sql_cat, $db);
$row_cat = mysql_fetch_assoc($result_cat);
			

$savant->assign('row_cat', $row_cat);
$savant->assign('result', $result);
$savant->display('admin/courses/course_categories.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 

?>