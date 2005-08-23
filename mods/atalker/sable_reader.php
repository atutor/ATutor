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
// $Id: index.php 5123 2005-07-12 14:59:03Z greg $

	// Get the time to use as a default filename
	$now = time();
	if($error){
		exit;
	}
	if($_POST['filename']){
		$file_save = AT_SPEECH_FILES_DIR.$_POST['filename'];
	}else{
		$file_save = AT_SPEECH_FILES_DIR.$now.'.'.$_POST['file_type'];
	}

	$file_in = AT_SPEECH_DIR.$now.'.sable';
	$file_out = AT_SPEECH_DIR.$now.'.wav';
	$file_out_mp3 = AT_SPEECH_DIR.$now.'.mp3';	
	$file_out_ogg = AT_SPEECH_DIR.$now.'.ogg';	
	$file_recieve = AT_SPEECH_URL.$now.'.'.$_POST['file_type'];
	
	//$_REQUEST['textin'] = stripslashes(trim($_POST['textin'], ""));

 //debug($_POST);
 $postdata = serialize($_POST);
// echo $postdata;
 //$repostdata = unserialize( $postdata);
 //debug($repostdata);
 //exit;
 if(!$_POST['create'] && !$_POST['remove']){
$sable_out = '
<?xml version="1.0"?>
<!DOCTYPE SABLE PUBLIC "-//SABLE//DTD SABLE speech mark up//EN" "Sable.v0_2.dtd"[]>
<SABLE>
 <LANGUAGE ID="'.$_POST['language'].'" CODE="ISO-8859-1">
  <SPEAKER NAME="'.$_POST['speaker'].'">
   <PITCH BASE="'.$_POST['base'].'" MIDDLE="'.$_POST['middle'].'" RANGE="'.$_POST['range'].'">
    <RATE SPEED="'.$_POST['rate'].'">
     <VOLUMN LEVEL="'.$_POST['volumn'].'">';
	$sable_out .= stripslashes($_POST['textin']);
	$sable_out .= '
     </VOLUMN>
    </RATE>
   </PITCH>
  </SPEAKER>
 </LANGUAGE>
</SABLE>';

	//write the SABLE file
	$fp = fopen($file_in,'w');
	if (!$fp) {
		echo 'Unable to create '.$name.' Sable file.';
		exit;
	}
	fputs($fp, $sable_out);
	fclose($fp);
}
if($_POST['create'] || $_POST['remove']){
		require(AT_INCLUDE_PATH.'../tools/atalker/admin_voice.php');

}else if($_POST['file_type'] ==  "mp3"){
	$command = "text2wave $file_in -o $file_out -F 48000";
	if(shell_exec('lame --longhelp')){
		$command2 = 'lame --quiet '.$file_out.' '. $file_out_mp3;
	}else if (shell_exec('bladeenc -h')) {
		$command2 = 'bladeenc -quiet '.$file_out.' '. $file_out_mp3;	
	}
	escapeshellcmd($command);
	escapeshellcmd($command2);
	passthru($command);
   	passthru($command2);
	gen_tts();
}else if($_POST['file_type'] ==  "ogg"){
	$command = "text2wave $file_in -o $file_out -F 48000";
	$command2 = 'oggenc -quiet '.$file_out.' '. $file_out_ogg;
	escapeshellcmd($command);
	escapeshellcmd($command2);
	passthru($command);
   	passthru($command2);
	gen_tts();

}else{
	$command = "text2wave $file_in -o $file_out -F 48000";
	escapeshellcmd($command);
	passthru($command);
	gen_tts();
}


?>