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

//	$admin = TRUE;
	define('AT_INCLUDE_PATH', '../../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_COURSES);
// when ATalker reader  is submitted check to see if the required fields have content, then get the approriate reader

require_once(AT_INCLUDE_PATH.'../mods/atalker/atalkerlib.inc.php');
if($_POST['type'] && trim($_POST['textin']) == '' && !$_POST['create'] && !$_POST['remove']){
			$error = 'TTS_NO_TEXTIN';
			$msg->addError($error);
}else if ($_POST['type'] == "text"){
		require(AT_INCLUDE_PATH.'../mods/atalker/text_reader.php');
 }else if ($_POST['type'] == "sable"){

 		require(AT_INCLUDE_PATH.'../mods/atalker/sable_reader.php');
 }
		
require_once(AT_INCLUDE_PATH.'../mods/atalker/admin/admin_voice.php');

	
require (AT_INCLUDE_PATH.'header.inc.php');

//echo "<a href=\"#\"  onMouseOver=\"javascript:evalSound('sound1')\" id=\"sound1\">test</a><embed src=\"".AT_SPEECH_TEMPLATE_URL."configuration.mp3\" autostart=\"true\" hidden=\"true\" volumn=\"10\" ></embed>";
//echo "<span onMouseOver=\"javascript:evalSound('sound1')\" id=\"sound1\">test</span><embed src=\"".AT_SPEECH_TEMPLATE_URL.$format."..mp3\" autostart=\"true\" hidden=\"true\" volumn=\"10\" ></embed>";
?>
<a href="#" onmouseover="javascript:evalSound('sound1')" onfocus="javascript:evalSound('sound1')">mouse over here</a>

<a href="<?php echo $_SERVER['PHP_SELF']; ?> " onmouseover="javascript:evalSound('sound2')" onfocus="javascript:evalSound('sound2')">mouse over here</a>

<embed src="<?php echo AT_SPEECH_TEMPLATE_URL; ?>languages.mp3" autostart="false" hidden="true" id="sound2" name="sound2" enablejavascript="true"></embed>
<embed src="<?php echo AT_SPEECH_TEMPLATE_URL; ?>themes.mp3" autostart="false" hidden="true" id="sound1" name="sound1" enablejavascript="true"></embed>
<?php

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
debug($embed);
foreach($embed as $speech_item){
	echo $speech_item."\n";

}
require (AT_INCLUDE_PATH.'footer.inc.php');

?>