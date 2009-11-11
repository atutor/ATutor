<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');

global $db, $associated_forum;

$get_related_glossary = true;
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

$cid = intval($_REQUEST['cid']);

if ($_POST) {
	$do_check = TRUE;
} else {
	$do_check = FALSE;
}

require(AT_INCLUDE_PATH.'lib/editor_tab_functions.inc.php');

if ($_POST['close'] || $_GET['close']) {
	if ($_GET['close']) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addFeedback('CLOSED');
		if ($cid == 0) {
			header('Location: '.AT_BASE_HREF.'tools/content/index.php');
			exit;
		}
	}
	
	if ($_REQUEST['cid'] == 0) {
		header('Location: '.AT_BASE_HREF.'tools/content/index.php');
		exit;
	}
	header('Location: '.$_base_path.url_rewrite('content.php?cid='.intval($_REQUEST['cid'])));
	exit;
}
	
$tabs = get_tabs();	
$num_tabs = count($tabs);
for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

if (isset($_GET['tab'])) {
	$current_tab = intval($_GET['tab']);
}
if (isset($_POST['current_tab'])) {
	$current_tab = intval($_POST['current_tab']);
}

if (isset($_POST['submit_file'])) {
	paste_from_file(body_text);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit_file_alt'])) {
	paste_from_file(body_text_alt);
} else if (isset($_POST['submit']) && ($_POST['submit'] != 'submit1')) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (isset($_POST['submit'])) {
	/* we're saving. redirects if successful. */
	save_changes(true, $current_tab);
}

if (!isset($current_tab) && isset($_POST['button_1']) && ($_POST['button_1'] == -1) && !isset($_POST['submit'])) {
	$current_tab = 1;
} else if (!isset($current_tab) && (($_POST['desc_submit'] != '') || ($_POST['reverse'] != ''))) {
	$current_tab = 4;  /* after clicking 'make decisions' on accessibility tab */
} else if (!isset($current_tab)) {
	$current_tab = 0;
}

if ($cid) {
	$_section[0][0] = _AT('edit_content');
} else {
	$_section[0][0] = _AT('add_content');
}

if ($cid) {
	$result = $contentManager->getContentPage($cid);

	if (!($content_row = @mysql_fetch_assoc($result))) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('PAGE_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$path	= $contentManager->getContentPath($cid);
	$content_test = $contentManager->getContentTestsAssoc($cid);

	if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
		$course_base_href = 'get.php/';
	} else {
		$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}

	if ($content_row['content_path']) {
		$content_base_href .= $content_row['content_path'].'/';
	}
} else {
	if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
		$content_base_href = 'get.php/';
	} else {
		$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
	}
}

if ($current_tab == 4) {
	/* kludge for issue #1626: */
	/* fixes the base href for the AChecker tab. */
	$course_base_href = '';
	$content_base_href = '';
}

//Added by Silvia

// tools/filemanager/top.php

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$MakeDirOn = true;

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize		= $row['max_file_size'];

if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
	$my_MaxCourseSize = $MaxCourseSize;
}
if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
	$my_MaxFileSize = $MaxFileSize;
} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
	$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
}

$MaxSubDirs  = 5;
$MaxDirDepth = 10;

if ($_GET['pathext'] != '') {
	$pathext = urldecode($_GET['pathext']);
} else if ($_POST['pathext'] != '') {
	$pathext = $_POST['pathext'];
}

if (strpos($pathext, '..') !== false) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('UNKNOWN');	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if($_GET['back'] == 1) {
	$pathext  = substr($pathext, 0, -1);
	$slashpos = strrpos($pathext, '/');
	if($slashpos == 0) {
		$pathext = '';
	} else {
		$pathext = substr($pathext, 0, ($slashpos+1));
	}

}

$start_at = 2;
/* remove the forward or backwards slash from the path */
$newpath = $current_path;
$depth = substr_count($pathext, '/');

if ($pathext != '') {
	$bits = explode('/', $pathext);
	foreach ($bits as $bit) {
		if ($bit != '') {
			$bit_path .= $bit;

			$_section[$start_at][0] = $bit;
			$_section[$start_at][1] = '../tools/filemanager/index.php?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed;

			$start_at++;
		}
	}
	$bit_path = "";
	$bit = "";
}

/* if upload successful, close the window */
if ($f) {
	$onload = 'closeWindow(\'progWin\');';
}

/* make new directory */
if (isset($_POST['mkdir'])) {
if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
	$_POST['dirname'] = trim($_POST['dirname']);

	/* anything else should be okay, since we're on *nix..hopefully */
	$_POST['dirname'] = preg_replace('/[^a-zA-Z0-9._]/', '', $_POST['dirname']);

	if ($_POST['dirname'] == '') {
		$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
	} 
	else if (strpos($_POST['dirname'], '..') !== false) {
		$msg->addError('BAD_FOLDER_NAME');
	}	
	else {
		$result = @mkdir($current_path.$pathext.$_POST['dirname'], 0700);
		if($result == 0) {
			$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
		}
		else {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
}
}
$newpath = substr($current_path.$pathext, 0, -1);


/* open the directory */
if (!($dir = @opendir($newpath))) {
	if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
		@mkdir($newpath);
		if (!($dir = @opendir($newpath))) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('CANNOT_CREATE_DIR');			
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		} else {
			$msg->addFeedback('CONTENT_DIR_CREATED');
		}
	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');

		$msg->printErrors('CANNOT_OPEN_DIR');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}
/*
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
}*/
//end top.inc.php

// upload.php
$_SESSION['done'] = 1;
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize	= $row['max_file_size'];

	if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
		$my_MaxCourseSize = $MaxCourseSize;
	}
	if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
		$my_MaxFileSize = $MaxFileSize;
	} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
		$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
	}

$path = AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$_POST['pathext'];

if (isset($_POST['upload'])) {

	if($_FILES['uploadedfile']['name'])	{
//		echo 'il file esiste';
		$_FILES['uploadedfile']['name'] = trim($_FILES['uploadedfile']['name']);
		$_FILES['uploadedfile']['name'] = str_replace(' ', '_', $_FILES['uploadedfile']['name']);

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = $path_parts['extension'];

		/* check if this file extension is allowed: */
		/* $IllegalExtentions is defined in ./include/config.inc.php */
		if (in_array($ext, $IllegalExtentions)) {
			$errors = array('FILE_ILLEGAL', $ext);
			$msg->addError($errors);
			header('Location: index.php?pathext='.$_POST['pathext']);
			exit;
		}

		/* also have to handle the 'application/x-zip-compressed'  case	*/
		if (   ($_FILES['uploadedfile']['type'] == 'application/x-zip-compressed')
			|| ($_FILES['uploadedfile']['type'] == 'application/zip')
			|| ($_FILES['uploadedfile']['type'] == 'application/x-zip')){
			$is_zip = true;						
		}

	
		/* anything else should be okay, since we're on *nix.. hopefully */
		$_FILES['uploadedfile']['name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_FILES['uploadedfile']['name']);


		/* if the file size is within allowed limits */
		if( ($_FILES['uploadedfile']['size'] > 0) && ($_FILES['uploadedfile']['size'] <= $my_MaxFileSize) ) {

			/* if adding the file will not exceed the maximum allowed total */
			$course_total = dirsize($path);

			if ((($course_total + $_FILES['uploadedfile']['size']) <= ($my_MaxCourseSize + $MaxCourseFloat)) || ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED)) {

				/* check if this file exists first */
				if (file_exists($path.$_FILES['uploadedfile']['name'])) {
					/* this file already exists, so we want to prompt for override */

					/* save it somewhere else, temporarily first			*/
					/* file_name.time ? */
					$_FILES['uploadedfile']['name'] = substr(time(), -4).'.'.$_FILES['uploadedfile']['name'];

					$f = array('FILE_EXISTS',
									substr($_FILES['uploadedfile']['name'], 5), 
									$_FILES['uploadedfile']['name'],
									$_POST['pathext'],
									$_GET['popup'],
									SEP);
					$msg->addFeedback($f);
				}

				/* copy the file in the directory */
				$result = move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $path.$_FILES['uploadedfile']['name'] );

				if (!$result) {
					require(AT_INCLUDE_PATH.'header.inc.php');
					$msg->printErrors('FILE_NOT_SAVED');
					echo '<a href="tools/filemanager/index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'] . '">' . _AT('back') . '</a>';
					require(AT_INCLUDE_PATH.'footer.inc.php');
					exit;
				} else {
					if ($is_zip) {
						$f = array('FILE_UPLOADED_ZIP',
										urlencode($_POST['pathext']), 
										urlencode($_FILES['uploadedfile']['name']), 
										$_GET['popup'],
										SEP);
						//$msg->addFeedback($f);
		
					} /* else */

					$msg->addFeedback('FILE_UPLOADED');
				}
			} else {
				$msg->addError(array('MAX_STORAGE_EXCEEDED', get_human_size($my_MaxCourseSize)));
			}
		} else {
			$msg->addError(array('FILE_TOO_BIG', get_human_size($my_MaxFileSize)));
		}
	} else {
		$msg->addError('FILE_NOT_SELECTED');
	}
}

// end upload.php	


if ($current_tab == 5){
	if ($_POST['alternatives']==1){
		if (isset($_POST['add'])){
			if (isset($_POST['resources'])){
				if (isset($_POST['radio_alt'])){
				
					$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and primary_resource_id='$_POST[resources]'";
		    		$result = mysql_query($sql, $db);
	    		
		    		if (mysql_num_rows($result) > 0) {
	    	 	 		while ($row = mysql_fetch_assoc($result)) {
	     		 			$sql_contr 	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]' and secondary_resource='$_POST[radio_alt]'";
	     	 				$contr 	 	= mysql_query($sql_contr, $db);
	     	 				if (mysql_num_rows($contr) > 0) {
	     	 					$msg->addError('ALTERNATIVE_ALREADY_DECLARED');
		     	 			}
		     	 			else {
	    	 	 				$sql_ins = "INSERT INTO ".TABLE_PREFIX."secondary_resources VALUES (NULL, '$row[primary_resource_id]', '$_POST[radio_alt]', 'en')";
								$ins     = mysql_query($sql_ins, $db);
								$msg->addFeedback('ALTERNATIVE_ADDED');
							}
						}
					}
	    		}
				else 
					$msg->addError('ALTERNATIVE_NOT_DEFINED');
			}
			else 
				$msg->addError('RESOURCE_NOT_DEFINED');
			}
		}

		
	if ($_REQUEST[act]=='delete')	{
		$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources WHERE secondary_resource_id='$_REQUEST[id_alt]'";
		$result = mysql_query($sql,$db);
		$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$_REQUEST[id_alt]'";
		$result = mysql_query($sql,$db);
		
		$msg->addFeedback('ALTERNATIVE_DELETED');
	}

}


if ($current_tab == 5) {
	if (isset($_POST['save_types_and_language'])){
		if(($_POST['alternatives']==1) || ($_GET['alternatives']==1)){
			$sql	= "SELECT primary_resource_id FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid'";
	    	$result = mysql_query($sql, $db);

	    	if (mysql_num_rows($result) > 0) {
	   	 		while ($row = mysql_fetch_assoc($result)) {
	   	 			$sql_type	 = "SELECT * FROM ".TABLE_PREFIX."resource_types";
	    			$result_type = mysql_query($sql_type, $db);
	    			
     	 			if (mysql_num_rows($result_type) > 0) {
	   	 				while ($type = mysql_fetch_assoc($result_type)) {
	   	 					$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$row[primary_resource_id]' and type_id='$type[type_id]'";
	   	 					$contr		= mysql_query($sql_contr, $db);	   
	   	 					if (mysql_num_rows($contr) > 0) {
	   	 						while ($control = mysql_fetch_assoc($contr)) {
	   	 							if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary']))
	   	 								continue;
	   	 							else {
	   	 								$sql_del = "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$control[primary_resource_id]' and type_id='$control[type_id]'";
										$result_del = mysql_query($sql_del, $db);
		 							}
	   	 						}	
	   	 					}
	   	 					else {
	   	 						if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary'])){
									$sql_ins	= "INSERT INTO ".TABLE_PREFIX."primary_resources_types VALUES ($row[primary_resource_id], $type[type_id])";
									$ins 		= mysql_query($sql_ins, $db);
									}	
	   	 						
	   	 						$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]'";
		    					$result_alt = mysql_query($sql_alt, $db);
	    					
								if (mysql_num_rows($result_alt) > 0) {
     		 						while ($alt = mysql_fetch_assoc($result_alt)) {
										$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$alt[secondary_resource_id]' and type_id='$type[type_id]'";
	   	 								$contr	= mysql_query($sql_contr, $db);	   
	   	 								if (mysql_num_rows($contr) > 0) {
	   	 									while ($control = mysql_fetch_assoc($contr)) {
	   	 										if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary']))
	   	 											continue;
	   	 										else {
	   	 											$sql_del = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$control[secondary_resource_id]' and type_id='$control[type_id]'";
													$result_del = mysql_query($sql_del, $db);
			 										}
		   	 									}		
	   		 								}
	   	 								else {
											if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary'])){
												$sql_ins	= "INSERT INTO ".TABLE_PREFIX."secondary_resources_types VALUES ($alt[secondary_resource_id], $type[type_id])";
												$ins 		= mysql_query($sql_ins, $db);
	   	 										}
	   	 									$lang=$_POST['lang_'.$alt[secondary_resource_id].'_secondary'];
											$sql_up	= "UPDATE ".TABLE_PREFIX."secondary_resources SET language_code='$lang' WHERE secondary_resource_id=$alt[secondary_resource_id]";
											$up 	= mysql_query($sql_up, $db);

      
		   	 							}	
									}			
								}
						
								$lang=$_POST['lang_'.$row[primary_resource_id].'_primary'];
								$sql_up	= "UPDATE ".TABLE_PREFIX."primary_resources SET language_code='$lang' WHERE primary_resource_id=$row[primary_resource_id]";
								$up 	= mysql_query($sql_up, $db);
								/* Added feedback message after types and language are updated Greg Oct 19/08  

								  */
								 $msg->addFeedback('RESOURCE_PROPERTIES_UPDATED');
	   	 					}
	   	 				}
					}
				}
	    	}
		}
		else {
			if (isset($_POST['body_text_alt'])){
				$cid_wholepage = $cid.'_wholepage';
				$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='$cid_wholepage'";
	    		$result = mysql_query($sql, $db);
				if (mysql_num_rows($result) > 0){
	    	 		while ($row = mysql_fetch_assoc($result)) {
		    	 		$sql_type	 = "SELECT * FROM ".TABLE_PREFIX."resource_types";
						$result_type = mysql_query($sql_type, $db);
	    				
		     	 		if (mysql_num_rows($result_type) > 0) {
	   			 			while ($type = mysql_fetch_assoc($result_type)) {
	   	 						$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$row[primary_resource_id]' and type_id='$type[type_id]'";
			   	 				$contr		= mysql_query($sql_contr, $db);	   
			   					if (mysql_num_rows($contr) > 0) {
	   			 					while ($control = mysql_fetch_assoc($contr)) {
	   	 								if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary']))
	   	 									continue;
			   	 						else {
	   			 							$sql_del = "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$control[primary_resource_id]' and type_id='$control[type_id]'";
											$result_del = mysql_query($sql_del, $db);
			 							}
	   	 							}	
	   	 						} else {
	   	 							if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary'])){
										$sql_ins	= "INSERT INTO ".TABLE_PREFIX."primary_resources_types VALUES ($row[primary_resource_id], $type[type_id])";
										$ins 		= mysql_query($sql_ins, $db);
									}
	   	 						}	
	   	 						if ($_POST['body_text_alt'] != $_POST['body_text']){
	    							$sql_check_secondary 	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]'";
	     				 			$check_secondary 	 	= mysql_query($sql_check_secondary, $db);
	     	 						if (mysql_num_rows($check_secondary) > 0) {
	     	 							while ($secondary = mysql_fetch_assoc($check_secondary)) {
			     	 						$sql_up	= "UPDATE ".TABLE_PREFIX."secondary_resources SET secondary_resource='$_POST[body_text_alt]' WHERE secondary_resource_id=$secondary[secondary_resource_id]";
											$up 	= mysql_query($sql_up, $db);
											$msg->addFeedback('ALTERNATIVE_UPDATED');
	     	 							}	
				     				}else {
	    			 					$sql_ins = "INSERT INTO ".TABLE_PREFIX."secondary_resources VALUES (NULL, '$row[primary_resource_id]', '$_POST[body_text_alt]', 'en')";
										$ins     = mysql_query($sql_ins, $db);
										$msg->addFeedback('ALTERNATIVE_ADDED');
									}
									$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]'";
	    							$result_alt = mysql_query($sql_alt, $db);
	    					
									if (mysql_num_rows($result_alt) > 0) {
     			 						while ($alt = mysql_fetch_assoc($result_alt)) {
											$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$alt[secondary_resource_id]' and type_id='$type[type_id]'";
	   	 									$contr	= mysql_query($sql_contr, $db);	   
				   	 						if (mysql_num_rows($contr) > 0) {
	   	 										while ($control = mysql_fetch_assoc($contr)) {
	   	 											if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary']))
	   	 												continue;
				   	 								else {
	   	 												$sql_del = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$control[secondary_resource_id]' and type_id='$control[type_id]'";
														$result_del = mysql_query($sql_del, $db);
		 											}
				   	 							}		
	   	 									} else {
												if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary'])){
													$sql_ins	= "INSERT INTO ".TABLE_PREFIX."secondary_resources_types VALUES ($alt[secondary_resource_id], $type[type_id])";
													$ins 		= mysql_query($sql_ins, $db);
		   	 									}
							   	 				$lang   = $_POST['lang_'.$alt[secondary_resource_id].'_secondary'];
												$sql_up	= "UPDATE ".TABLE_PREFIX."secondary_resources SET language_code='$lang' WHERE secondary_resource_id='$alt[secondary_resource_id]'";
												$up 	= mysql_query($sql_up, $db);

	   	 									}
     	 									$lang=$_POST['lang_'.$row[primary_resource_id].'_primary'];
											$sql_up	= "UPDATE ".TABLE_PREFIX."primary_resources SET language_code='$lang' WHERE primary_resource_id='$row[primary_resource_id]'";
											$up 	= mysql_query($sql_up, $db);

	   	 								}
									}
								}
	   	 					}
						}
	   				}
				}
			}
	    }
	}
}		

//End Added by Silvia 

//Extended Test within content functionality - Harris @Sep 9, 2008
//if ($current_tab == 6){
//}

if (($current_tab == 0) || ($_current_tab == 5)) {
	if (!isset($_REQUEST['setvisual']) && !isset($_REQUEST['settext'])) {
		if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 1) {
			$_POST['formatting'] = 1;
			$_REQUEST['settext'] = 0;
			$_REQUEST['setvisual'] = 0;

		} else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) {
			$_POST['formatting'] = 1;
			$_POST['settext'] = 0;
			$_POST['setvisual'] = 1;

		} else { // else if == 0
			$_POST['formatting'] = 0;
			$_REQUEST['settext'] = 0;
			$_REQUEST['setvisual'] = 0;
		}
	}
	if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
		$onload = "document.form.ctitle.focus(); ";
	}
}

// initialize buttons, texts, radio buttons for editor
if ($current_tab == 0) 
{
	$onload.="on_load();";
}

if ($current_tab == 5) 
{
	$onload.="on_load();";
}

require(AT_INCLUDE_PATH.'header.inc.php');

if ($current_tab == 0) 
{
	load_editor(false, "none");
}

if ($current_tab == 5) 
{
	load_editor(false, "none");
}


//TODO*************BOLOGNA****************REMOVE ME**************/
//loading toolbar for insert discussion topic or web link into the content
if ($current_tab == 0){
    if(authenticate(AT_PRIV_CONTENT,AT_PRIV_RETURN)){
        $home_links = get_home_navigation();                        //vengono lette le caratteristiche di ogni modulo attivato nella home page.
        $main_links = get_main_navigation($current_page);           //vengono lette le caratteristiche di ogni modulo attivo nel main navigation

        $num = count($main_links);                                  //necessario elminare il primo e l'utlimo elemento poichè sono rispettivamente "Home" e "Manage"
        unset($main_links[0]);                                      //"Home" label
        unset($main_links[$num-1]);                                 //"Manage" label

        $all_tools = $home_links;                                   //$all_tools represent a merge between $home_links and main_links without repetitions.
        $check=false;
        foreach($main_links as $main) {
            foreach($home_links as $home) {
                if($home['title'] == $main['title']) {
                    $check=true;
                    break;
                }
            }
            if(!$check)
                $all_tools[]=$main;
            else
                $check=false;
        }
    }
}


$cid = intval($_REQUEST['cid']);
$pid = intval($_REQUEST['pid']);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?cid=<?php echo $cid; ?>" method="post" name="form" enctype="multipart/form-data">
<?php

	if ($cid) {
		//$content_row = sql_quote($content_row);
		if (isset($_POST['current_tab'])) {
			//$changes_made = check_for_changes($content_row);
		} else {
			$changes_made = array();

			$_POST['formatting'] = $content_row['formatting'];
			$_POST['head'] = $content_row['head'];
			$_POST['use_customized_head'] = $content_row['use_customized_head'];
			$_POST['title']      = $content_row['title'];
			$_POST['body_text']  = $content_row['text'];
			$_POST['keywords']   = $content_row['keywords'];
			$_POST['test_message'] = $content_row['test_message'];
			$_POST['allow_test_export'] = $content_row['allow_test_export'];

			$_POST['day']   = substr($content_row['release_date'], 8, 2);
			$_POST['month'] = substr($content_row['release_date'], 5, 2);
			$_POST['year']  = substr($content_row['release_date'], 0, 4);
			$_POST['hour']  = substr($content_row['release_date'], 11, 2);
			$_POST['min']= substr($content_row['release_date'], 14, 2);

			$_POST['ordering'] = $content_row['ordering'];
			$_POST['related'] = $contentManager->getRelatedContent($cid);
			
			$_POST['pid'] = $pid = $content_row['content_parent_id'];

			$_POST['related_term'] = $glossary_ids_related;
		}

	} else {
		$cid = 0;
		if (!isset($_POST['current_tab'])) {
			$_POST['day']  = date('d');
			$_POST['month']  = date('m');
			$_POST['year'] = date('Y');
			$_POST['hour'] = date('H');
			$_POST['min']  = 0;

			if (isset($_GET['pid'])) {
				$pid = $_POST['pid'] = intval($_GET['pid']);
				$_POST['ordering'] = count($contentManager->getContent($pid))+1;
			} else {
				$_POST['pid'] = 0;
				$_POST['ordering'] = count($contentManager->getContent(0))+1;
			}
		}
	}

	echo '<input type="hidden" name="cid" value="'.$cid.'" />';
	echo '<input type="hidden" name="title" value="'.htmlspecialchars($stripslashes($_POST['title'])).'" />';
	if ($_REQUEST['sub'] == 1)
	{
		echo '<input type="hidden" name="sub" value="1" />';
		echo '<input type="hidden" name="folder_title" value="'.htmlspecialchars($stripslashes($_POST['folder_title'])).'" />';
	}
	echo '<input type="submit" name="submit" style="display:none;"/>';
	if (($current_tab != 0) && (($_current_tab != 5))) {
		echo '<input type="hidden" name="body_text" value="'.htmlspecialchars($stripslashes($_POST['body_text'])).'" />';
		echo '<input type="hidden" name="head" value="'.htmlspecialchars($stripslashes($_POST['head'])).'" />';
		echo '<input type="hidden" name="use_customized_head" value="'.(($_POST['use_customized_head']=="") ? 0 : $_POST['use_customized_head']).'" />';
		echo '<input type="hidden" name="displayhead" value="'.$_POST['displayhead'].'" />';
		echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
		echo '<input type="hidden" name="settext" value="'.$_POST['settext'].'" />';		
		echo '<input type="hidden" name="formatting" value="'.$_POST['formatting'].'" />';
	}

	echo '<input type="hidden" name="ordering" value="'.$_POST['ordering'].'" />';
	echo '<input type="hidden" name="pid" value="'.$pid.'" />';

	echo '<input type="hidden" name="day" value="'.$_POST['day'].'" />';
	echo '<input type="hidden" name="month" value="'.$_POST['month'].'" />';
	echo '<input type="hidden" name="year" value="'.$_POST['year'].'" />';
	echo '<input type="hidden" name="hour" value="'.$_POST['hour'].'" />';
	echo '<input type="hidden" name="minute" value="'.$_POST['minute'].'" />';
	echo '<input type="hidden" name="min" value="'.$_POST['min'].'" />';
	
	echo '<input type="hidden" name="alternatives" value="'.$_POST['alternatives'].'" />';
	
	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';

	if (is_array($_POST['related']) && ($current_tab != 1)) {
		foreach($_POST['related'] as $r_id) {
			echo '<input type="hidden" name="related[]" value="'.$r_id.'" />';
		}
	}
	echo '<input type="hidden" name="keywords" value="'.htmlspecialchars(stripslashes($_POST['keywords'])).'" />';

	//content test association
	echo '<input type="hidden" name="test_message" value="'.$_POST['test_message'].'" />';
	
	/* get glossary terms */
	$matches = find_terms(stripslashes($_POST['body_text']));
	$num_terms = count($matches[0]);
	$matches = $matches[0];
	$word = str_replace(array('[?]', '[/?]'), '', $matches);

	if (is_array($word)) {
		/* update $_POST['glossary_defs'] with any new/changed terms */
		for($i=0; $i<$num_terms; $i++) {
			$word[$i] = urlencode($word[$i]);
			if (!isset($_POST['glossary_defs'][$word[$i]])) {
				$_POST['glossary_defs'][$word[$i]] = $glossary[$word[$i]];
			}
		}
	}

	if (is_array($_POST['glossary_defs']) && ($current_tab != 2)) {
		foreach($_POST['glossary_defs'] as $w => $d) {
			/* this term still exists in the content */
			if (!in_array($w, $word)) {
				unset($_POST['glossary_defs'][$w]);
				continue;
			}
			echo '<input type="hidden" name="glossary_defs['.$w.']" value="'.htmlspecialchars(stripslashes($d)).'" />';
		}
		if (isset($_POST['related_term'])) {
			foreach($_POST['related_term'] as $w => $d) {
				echo '<input type="hidden" name="related_term['.$w.']" value="'.$d.'" />';
			}
		}
	}

	//tests
	if (is_array($_POST['tid']) && $current_tab != 6){
		/* Test & Survey --> Other tabs triggers this condition */
		foreach ($_POST['tid'] as $i=>$tid){
			echo '<input type="hidden" name="tid['.$i.']" value="'.$tid.'" />';
		}
	} elseif ($current_tab != 6){
		/* Edit Content (On Edit content tab), without clicking Test & Survey */
		$i = 0;
		if ($content_test){
			while ($content_test_row = mysql_fetch_assoc($content_test)){
				echo '<input type="hidden" name="tid['.$i++.']" value="'.$content_test_row['test_id'].'" />';
			}
		}
	}
	if (!isset($_POST['allow_test_export']) && $current_tab != 6) {
		//export flag handling.
		$sql = "SELECT `allow_test_export` FROM ".TABLE_PREFIX."content WHERE content_id=$_REQUEST[cid]";
		$result2 = mysql_query($sql, $db);
		if ($result2){
			$c_row = mysql_fetch_assoc($result2);
		}
		if (intval($c_row['allow_test_export'])==1){
			echo '<input type="hidden" name="allow_test_export" value="1" />';
		} else {
			echo '<input type="hidden" name="allow_test_export" value="0" />';
		}
	} else {
		echo '<input type="hidden" name="allow_test_export" value="'.intval($_POST['allow_test_export']).'" />';
	}

	if ($do_check) {
		$changes_made = check_for_changes($content_row);
	}

?>
<div align="center">
	<?php output_tabs($current_tab, $changes_made); ?>
</div>
<div class="input-form" style="width: 95%">

	<?php if ($changes_made): ?>
		<div class="unsaved">
			<span style="color:red;"><?php echo _AT('save_changes_unsaved'); ?></span> 
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" style="border: 1px solid red;" /> 
			<input type="submit" name="close" class="button green" value="<?php echo _AT('close'); ?>" />  <input type="checkbox" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?> />
			<label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>

	<?php else: ?>
		<div class="saved">
			<?php //if ($cid) { echo _AT('save_changes_saved'); } ?> <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?> alt-s" accesskey="s" /> <input type="submit" name="close" value="<?php echo _AT('close'); ?>" /> <input type="checkbox" style="border:0px;" id="close" name="save_n_close" value="1" <?php if ($_SESSION['save_n_close']) { echo 'checked="checked"'; } ?> /><label for="close"><?php echo _AT('close_after_saving'); ?></label>
		</div>
	<?php endif; ?>
	<?php 
	//	if ($current_tab != 5){
				include(AT_INCLUDE_PATH.'html/editor_tabs/'.$tabs[$current_tab][1]);
				echo '</div></form>';
	//	}
	//	else 
	//	{
	//		echo '</div></form>';
	//		include(AT_INCLUDE_PATH.'html/editor_tabs/'.$tabs[$current_tab][1]);
			
	//	}
			
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
