<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 5123 2005-07-12 14:59:03Z joel $
	$_user_location	= 'public';
	
	define('AT_INCLUDE_PATH', '../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	require_once(AT_INCLUDE_PATH.'../mods/atalker/atalkerlib.inc.php');

	$_pages['mods/atalker/index.php']['title_var']  = 'ATalker';
	
	$tabs = get_atalker_tabs();
	$num_tabs = count($tabs);
	if ($_REQUEST['tab']) {
		$tab = $_REQUEST['tab'];
	}

/*
	if ((isset($_REQUEST['popup']))  &&  ($_REQUEST['popup'] == TRUE)) {
		$popup = TRUE;
		$popup_win = "popup=1";
	} 
*/

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