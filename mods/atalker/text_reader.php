<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay       */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: text_reader.php 5123 2005-07-12 14:59:03Z greg $

	// Get the time to use as a default filename
	$now = time();
	// Otherwise use the file name entered by the user
	if($_POST['filename']){
		if($_SESSION['course_id'] > 0){
				$file_save = AT_SPEECH_FILES_DIR.$_POST['filename'];
		}else{
				$file_save = AT_SPEECH_TEMPLATE_DIR.$_POST['filename'];
		}
	}else{
		if($_SESSION['course_id'] > 0){
			$file_save = AT_SPEECH_FILES_DIR.$now.'.'.$_POST['file_type'];		
		}else{
			$file_save = AT_SPEECH_TEMPLATE_DIR.$now.'.'.$_POST['file_type'];	
		}

	}

	$file_out = AT_SPEECH_DIR.'/'.$now.'.wav';
	$file_out_mp3 = AT_SPEECH_DIR.$now.'.mp3';	
	$file_out_ogg = AT_SPEECH_DIR.$now.'.ogg';	
	$file_in =  AT_SPEECH_DIR.$now.'.txt';
	$file_recieve = AT_SPEECH_URL.$now.'.'.$_POST['file_type'];
	$scheme_out = AT_SPEECH_DIR.$now.'.scm';

	
	// Build the Scheme file. Lots more can be done here to customize voices
	$scheme_in .= "(";
	$scheme_in .= $_POST['voice'];
	$scheme_in .= ")\n";
	$scheme_in .= "(";
	$scheme_in .= "Parameter.set 'Duration_Stretch ".$_POST['duration'];
	$scheme_in .= ")";
	
	// create a scheme file with the voice properties
	$fp = fopen($scheme_out,'w');
	if (!$fp) {
		echo AT_ERROR_TTS_NOT_CREATE_SCHEME;
		exit;
	}
 	fputs($fp, $scheme_in);
 	fclose($fp);
	
	//$file_props = "-mode --tts -eval ".AT_SPEECH_DIR.$now.".scm";

	if(!$_POST['create'] && !$_POST['remove']){
	
		//create a text file from the inputted text
		$fp = fopen($file_in,'w');
		if (!$fp) {
			echo AT_ERROR_TTS_NOT_CREATE_TEXT;
			exit;
		}
		fputs($fp, $_POST['textin']);
		fclose($fp);
	
		if($_POST['file_type'] ==  "mp3"){
			$command = "text2wave $file_props $file_in -o $file_out -F 48000  -scale ".$_POST['volumn']."";
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
			$command = "text2wave $file_props $file_in -o $file_out -F 48000  -scale ".$_POST['volumn']."";
			$command2 = 'oggenc -quiet '.$file_out.' '. $file_out_ogg;
			escapeshellcmd($command);
			escapeshellcmd($command2);
			passthru($command);
			passthru($command2);
			gen_tts();
			
		}else{
			$command = "text2wave $file_props $file_in -o $file_out -F 48000 -scale ".$_POST['volumn']."";
			escapeshellcmd($command);
			passthru($command);
			gen_tts();
			
		
		}
	}
?>