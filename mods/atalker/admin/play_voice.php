<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 				        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: play_voice.php 5123 2005-07-12 14:59:03Z greg

// This file is required to prevent browsers from caching content of 
// TTS files if they were to  access speech file  directly through a URL 


	define('AT_INCLUDE_PATH', '../../../include/');

	require (AT_INCLUDE_PATH.'vitals.inc.php');

	define('AT_SPEECH_TEMPLATE_URL', $_base_href.'content/template/'.$_SESSION['lang'].'/');

	if(strstr($_GET['play_voice'], '.mp3')){

		header('Content-type: audio/x-mp3');

	}else if(strstr($_GET['play_voice'], '.ogg')){

		header('Content-type: audio/x-ogg');

	}

	header('Content-Disposition: inline; filename="'.str_replace("-","/",AT_SPEECH_TEMPLATE_URL).$_GET['play_voice'].'"');
	readfile(AT_SPEECH_TEMPLATE_URL.$_GET['play_voice']);

?>