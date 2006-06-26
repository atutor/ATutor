<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 							      */
/* Adaptive Technology Resource Centre / University of Toronto  				*/
/* http://atutor.ca												*/
/*                                                        								      */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 5123 2005-07-12 14:59:03Z greg$
	$_user_location	= 'public';
	
	define('AT_INCLUDE_PATH', '../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

	$_POST['textin'] = $addslashes(stripslashes($_POST['textin']));	
	$_POST['add'] = $addslashes(stripslashes($_POST['add']));	
	$_POST['type'] = $addslashes(stripslashes($_POST['type']));	
	$_POST['page'] = $addslashes(stripslashes($_POST['page']));	
	$_POST['popup'] = $addslashes(stripslashes($_POST['popup']));	
	$_POST['download'] = $addslashes(stripslashes($_POST['download']));	
	$_POST['read'] = $addslashes(stripslashes($_POST['read']));	
	$_POST['save'] = $addslashes(stripslashes($_POST['save']));	
	$_POST['file_type'] = $addslashes(stripslashes($_POST['file_type']));	
	$_POST['volumn'] = $addslashes(stripslashes($_POST['volumn']));	
	$_POST['duration'] = $addslashes(stripslashes($_POST['duration']));	
	$_POST['filename'] = $addslashes(stripslashes($_POST['filename']));	


	$_POST['export'] = $addslashes(stripslashes($_POST['export']));	
	$_POST['language'] = $addslashes(stripslashes($_POST['language']));	
	$_POST['speaker'] = $addslashes(stripslashes($_POST['speaker']));
	$_POST['base'] = $addslashes(stripslashes($_POST['base']));
	$_POST['middle'] = $addslashes(stripslashes($_POST['middle']));
	$_POST['range'] = $addslashes(stripslashes($_POST['range']));
	$_POST['rate'] = $addslashes(stripslashes($_POST['rate']));


	require_once(AT_INCLUDE_PATH.'../mods/atalker/atalkerlib.inc.php');

	$_pages['mods/atalker/index.php']['title_var']  = _AT('atalker');
	
	$tabs = get_atalker_tabs();
	$num_tabs = count($tabs);
	if ($_REQUEST['tab']) {
		$tab = $_REQUEST['tab'];
	}


// when ATalker reader  is submitted check to see if the require fields have content, then get the approriate reader
if($_POST['type'] && trim($_POST['textin']) == '' && !$_POST['create']){
			$error = 'TTS_NO_TEXTIN';
			$msg->addError($error);

}else if ($_POST['type'] == "text"){
		require(AT_INCLUDE_PATH.'../mods/atalker/text_reader.php');

}else if ($_POST['type'] == "sable"){
		require(AT_INCLUDE_PATH.'../mods/atalker/sable_reader.php');

}

	
	require (AT_INCLUDE_PATH.'header.inc.php');
	require ('reader.html.php');

	require (AT_INCLUDE_PATH.'footer.inc.php');
?>