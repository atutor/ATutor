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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);


if (isset($_POST['edit'], $_POST['word_id'])) {
	header('Location: edit.php?gid='.$_POST['word_id']);
	exit;
} else if (isset($_POST['delete'], $_POST['word_id'])) {
	header('Location: delete.php?gid='.$_POST['word_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

//get terms
$sql	= "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] ORDER BY word";			
$result= mysql_query($sql, $db);

$gloss_results = array();
while ($row = mysql_fetch_assoc($result)) {
	$gloss_results[] = $row;
}
$num_results = count($gloss_results);
$results_per_page = 25;
$num_pages = ceil($num_results / $results_per_page);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}
	
$count = (($page-1) * $results_per_page) + 1;
$gloss_results = array_slice($gloss_results, ($page-1)*$results_per_page, $results_per_page);
	
if($num_pages > 1) {
	echo _AT('page').': ';
	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == $page) {
			echo '<strong>'.$i.'</strong>';
		} else {
			echo ' | <a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
		}
	}
}

if(!empty($gloss_results)) {
	foreach ($gloss_results as $row) {	
		//get related term name
		$related_word = '';
		if ($row['related_word_id']) {
			$sql	= "SELECT word FROM ".TABLE_PREFIX."glossary WHERE word_id=".$row['related_word_id']." AND course_id=".$_SESSION['course_id'];
			$result = mysql_query($sql, $db);
			if ($row_related = mysql_fetch_array($result)) {
				$related_word = $row_related['word'];			
			}
		}

		$def_trunc = validate_length($row['definition'], 70, VALIDATE_LENGTH_FOR_DISPLAY);
		$gloss_results_row[] = $row;
	}
}
$savant->assign('gloss_results_row', $gloss_results_row);
$savant->assign('related_word', $related_word);
$savant->assign('def_trunc', $def_trunc);	

$savant->display('instructor/glossary/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>