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

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

global $db;

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

if (isset($_POST['submit_yes'])) {
	/* delete files and directories */
	/* delete the file  */
	$pathext = $_POST['pathext'];
	if (isset($_POST['listoffiles']))  {
		$checkbox = explode(',',$_POST['listoffiles']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];

			if (course_realpath($current_path . $pathext . $filename) == FALSE) {
				$msg->addError('FILE_NOT_DELETED');
				$result=false;
				break;
			} else if (!(@unlink($current_path.$pathext.$filename))) {
				$msg->addError('FILE_NOT_DELETED');
				$result=false;
				break;
			}			
		}
		if ($result)
		{ 
			// delete according definition of primary resources and alternatives for adapted content
			$filename = '../'.$pathext.$filename;
			
			// 1. delete secondary resources types
			$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types
			         WHERE secondary_resource_id in (SELECT secondary_resource_id 
			                      FROM ".TABLE_PREFIX."secondary_resources
			                     WHERE secondary_resource = '".$filename."'
			                        OR primary_resource_id in (SELECT primary_resource_id
			                                      FROM ".TABLE_PREFIX."primary_resources
			                                     WHERE resource='".$filename."'))";
			$result = mysql_query($sql, $db);
			
			// 2. delete secondary resources 
			$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources
			         WHERE secondary_resource = '".$filename."'
			            OR primary_resource_id in (SELECT primary_resource_id
			                     FROM ".TABLE_PREFIX."primary_resources
			                    WHERE resource='".$filename."')";
			$result = mysql_query($sql, $db);
			
			// 3. delete primary resources types
			$sql = "DELETE FROM ".TABLE_PREFIX."primary_resources_types
			         WHERE primary_resource_id in (SELECT primary_resource_id 
			                      FROM ".TABLE_PREFIX."primary_resources
			                     WHERE resource = '".$filename."')";
			$result = mysql_query($sql, $db);
			
			// 4. delete primary resources 
			$sql = "DELETE FROM ".TABLE_PREFIX."primary_resources
			         WHERE resource = '".$filename."'";
			$result = mysql_query($sql, $db);
			
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
	/* delete directory */
	if (isset($_POST['listofdirs'])) {
				
		$checkbox = explode(',',$_POST['listofdirs']);
		$count = count($checkbox);
		$result=true;
		for ($i=0; $i<$count; $i++) {
			$filename=$checkbox[$i];
				
			if (strpos($filename, '..') !== false) {
				$msg->addError('UNKNOWN');
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			} else if (!is_dir($current_path.$pathext.$filename)) {
				$msg->addError(array('DIR_NOT_DELETED',$filename));
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			} else if (!($result = clr_dir($current_path.$pathext.$filename))) { 
				$msg->addError('DIR_NO_PERMISSION');
				$result=false;
				header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			} 
		}
		if ($result)
			$msg->addFeedback('DIR_DELETED');
	}
	
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

	require(AT_INCLUDE_PATH.'header.inc.php');
	// find the files and directories to be deleted 
	$total_list = explode(',', $_GET['list']);
	$pathext = $_GET['pathext']; 
	$popup   = $_GET['popup'];
	$framed  = $_GET['framed'];
	$cp = $_GET['cp'];
	$cid = $_GET['cid'];
	$pid = $_GET['pid'];
	$a_type = $_GET['a_type'];
	
	$count = count($total_list);
	$countd = 0;
	$countf = 0;
	
	foreach ($total_list as $list_item) {
		if (is_dir($current_path.$pathext.$list_item)) {
			$_dirs[$countd]  = $list_item;
			$countd++;
		} else {
			$_files[$countf] = $list_item;
			$countf++;
		}
	}
				
	$hidden_vars['pathext'] = $pathext;
	$hidden_vars['popup']   = $popup;
	$hidden_vars['framed']  = $framed;
	$hidden_vars['cp']  = $cp;
	$hidden_vars['cid']  = $cid;
	$hidden_vars['pid']  = $pid;
	$hidden_vars['a_type']  = $a_type;
	
	if (isset($_files)) {
		$list_of_files = implode(',', $_files);
		$hidden_vars['listoffiles'] = $list_of_files;

		foreach ($_files as $file) {
			$file_list_to_print .= '<li>'.$file.'</li>';
		}
		$msg->addConfirm(array('FILE_DELETE', $file_list_to_print), $hidden_vars);
	}
		
	if (isset($_dirs)) {
		$list_of_dirs = implode(',', $_dirs);
		$hidden_vars['listofdirs'] = $list_of_dirs;

		foreach ($_dirs as $dir) {
			$dir_list_to_print .= '<li>'.$dir.'</li>';
		}

		$msg->addConfirm(array('DIR_DELETE',$dir_list_to_print), $hidden_vars);
	}

	$msg->printConfirm();
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>