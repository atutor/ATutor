<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$ $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'mods/_core/courses/admin/courses.php');
	exit;
}

$_POST['encyclopedia'] == $addslashes($_POST['encyclopedia']);
$_POST['dictionary'] == $addslashes($_POST['dictionary']);
$_POST['thesaurus'] == $addslashes($_POST['thesaurus']);
$_POST['atlas'] == $addslashes($_POST['atlas']);
$_POST['calculator'] == $addslashes($_POST['calculator']);
$_POST['abacus'] == $addslashes($_POST['abacas']);
$_POST['note_taking'] == $addslashes($_POST['note_taking']);

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value){
		if($key != "submit"){
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('$key', '$value')";
		$result = mysql_query($sql, $db);
		};
	}
		
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location:'. $_SERVER[PHP_SELF]);
	exit;

}
require(AT_INCLUDE_PATH.'header.inc.php');

?>

<?php 
$savant->display('admin/courses/scaffolds.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>