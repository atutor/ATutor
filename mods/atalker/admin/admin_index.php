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
// $Id: admin_index.php 5123 2005-07-12 14:59:03Z greg $

// This file intiates the  Text Reader, SABLE reader, and Voice Manager 
// for the ATutor administrator

	$_user_location	= 'public';
	$admin = TRUE;
	define('AT_INCLUDE_PATH', '../../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

 	if(admin_authenticate()){
 
		// where admin audio files are saved
		define('AT_SPEECH_TEMPLATE_DIR', AT_CONTENT_DIR.'template/'.$_SESSION['lang'].'/');
		define('AT_SPEECH_TEMPLATE_URL', $_base_href.'content/template/'.$_SESSION['lang'].'/');
 		define('AT_SPEECH_FILES_DIR', AT_CONTENT_DIR.'template/temp/'); 
		define('AT_SPEECH_URL', $_base_href.'content/template/temp/');
		define('AT_SPEECH_DIR', AT_CONTENT_DIR.'template/temp/');
 	}	



// Manage ATalker tabs and popup window
	$_pages['mods/atalker/admin/admin_index.php']['title_var']  = 'ATalker Admin';

	if($_GET['postdata']){
		$postdata = stripslashes($_GET['postdata']);
		$_POST = unserialize($postdata);
	
	}else{
	
		$postdata  = serialize($_POST);
	}


// See if the speech directories exists yet, and create them if they don't	
	if(@!opendir(AT_SPEECH_DIR)){
			mkdir(AT_SPEECH_DIR, 0700);
	}
	
	if(@!opendir(AT_SPEECH_FILES_DIR)){
				mkdir(AT_SPEECH_FILES_DIR, 0700);
	}
	
	if(@!opendir(AT_SPEECH_TEMPLATE_DIR)){
			mkdir(AT_SPEECH_TEMPLATE_DIR, 0700);
	}	 


// when ATalker reader  is submitted check to see if the require fields have content, then get the approriate reader

//require_once(AT_INCLUDE_PATH.'../mods/atalker/atalkerlib.inc.php');


if ($_POST['type'] == "text"){
		require(AT_INCLUDE_PATH.'../mods/atalker/text_reader.php');
 }else if ($_POST['type'] == "sable"){
 		require(AT_INCLUDE_PATH.'../mods/atalker/sable_reader.php');
 }
		

require_once(AT_INCLUDE_PATH.'../mods/atalker/admin/admin_voice.php');

	
require (AT_INCLUDE_PATH.'header.inc.php');
clean_tts_files();
	$tabs = get_atalker_tabs();
	$num_tabs = count($tabs);
	if ($_REQUEST['tab']) {
		$tab = $_REQUEST['tab'];
	}



	if ((isset($_REQUEST['popup']))  &&  ($_REQUEST['popup'] == TRUE)) {
		$popup = TRUE;
		$popup_win = "popup=1";
	} 

require ('../reader.html.php');
require (AT_INCLUDE_PATH.'footer.inc.php');

clean_tts_files();
?>