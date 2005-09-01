<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 				        */
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
	$scheme_out = AT_SPEECH_DIR.$now.'.scm';
	//$sable_in= AT_SPEECH_DIR.$now.'.sable';
	// Build the Scheme file. Lots more can be done here to customize voices
	
if($_POST['type'] != "sable"){
	$scheme_in .= "(";
	$scheme_in .= $_POST['voice'];
	$scheme_in .= ")\n";
	$scheme_in .= "(";
	$scheme_in .= "Parameter.set 'Duration_Stretch ".$_POST['duration'];
	$scheme_in .= ")";

	// create a scheme file with the voice properties
	$fp = fopen($scheme_out,'w');
	if (!$fp) {
		echo _AT(AT_ERROR_TTS__NOT_CREATE_SCHEME);
		exit;
	}
 	fputs($fp, $scheme_in);
 	fclose($fp);
}

if($_POST['create'] && !$_GET['page']){
	$d = "0";
	if($_POST['file_type'] == "mp3"){
		if(shell_exec('lame --longhelp')){
			$command2 = 'lame --quiet '.$file_out.' '. $file_out_mp3;
		}else if (shell_exec('bladeenc -h')) {
			$command2 = 'bladeenc -quiet '.$file_out.' '. $file_out_mp3;	
		}else{
			$error = "TTS_MP3_ENCODER";
			$msg->addError($error);
		}
	}
	if($_POST['file_type'] == "ogg"){
	
		$command = 'oggenc --version';
		$command = escapeshellcmd($command);
		
		if(shell_exec($command) == ''){
			$error = "TTS_OGG_ENCODER";
			$msg->addError($error);
		}else{
			$command2 = 'oggenc -quiet '.$file_out.' '. $file_out_ogg;	
		}
	}
	if(!$error){
	
		foreach ($_POST['check'] as $lang_var){
			
			$file_recieve = AT_SPEECH_URL.$lang_var.'.'.$_POST['file_type'];
			$voice_file = AT_SPEECH_TEMPLATE_DIR.$lang_var.'.'.$_POST['file_type'];
			
			$sql = "SELECT * from ".TABLE_PREFIX."language_text WHERE language_code = '".$_SESSION['lang']."' AND term = '".$lang_var."'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_row($result);

			if($_POST['file_type'] == "mp3"){
				//$file_in =  AT_SPEECH_DIR.$lang_var.'.sable';
				$file_out = AT_SPEECH_DIR.$lang_var.'.wav';
				//If SABLE is being used, generate the SBALE markup as the input file
				if($_POST['type'] == "sable"){
					$file_in =  AT_SPEECH_DIR.$lang_var.'.sable';				
					$sable_out = '<?xml version="1.0"?>
					<!DOCTYPE SABLE PUBLIC "-//SABLE//DTD SABLE speech mark up//EN" "Sable.v0_2.dtd"[]>
					<SABLE>
					<LANGUAGE ID="'.$_POST['language'].'" CODE="ISO-8859-1">
					<SPEAKER NAME="'.$_POST['speaker'].'">
					<PITCH BASE="'.$_POST['base'].'" MIDDLE="'.$_POST['middle'].'" RANGE="'.$_POST['range'].'">
					<RATE SPEED="'.$_POST['rate'].'">
					<VOLUMN LEVEL="'.$_POST['volumn'].'">';
						$sable_out .= $row[3].".";
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
						echo _AT(AT_ERROR_TTS__NOT_CREATE_SABLE);
						exit;
					}
					fputs($fp, $sable_out);
					fclose($fp);
					$command = "text2wave $file_in -o $file_out -F 48000";
				}else{
					// If not SABLE being used, generate a standard scheme file and input text file.
					$file_in =  AT_SPEECH_DIR.$lang_var.'.txt';
					$scheme_out = AT_SPEECH_DIR.$now.'.scm';	
					$file_props = "-mode --tts -eval ".AT_SPEECH_DIR.$now.".scm";
					$fp = fopen($file_in,'w');
					if (!$fp) {
						echo _AT(AT_ERROR_TTS__NOT_CREATE_TEXT);echo 'Unable to create '.$name.' Text file.';
						exit;
					}
					fputs($fp, $row[3].'.');
					fclose($fp);	
					$command = "text2wave $file_props $file_in -o $file_out -F 48000  -scale ".$_POST['volumn']."";
				}


				$file_out_mp3 = AT_SPEECH_DIR.$lang_var.'.mp3';

				if(shell_exec('lame --longhelp')){
					$command2 = 'lame --quiet '.$file_out.' '. $file_out_mp3;
				}else if (shell_exec('bladeenc -h')) {
					$command2 = 'bladeenc -quiet '.$file_out.' '. $file_out_mp3;	
				}
				escapeshellcmd($command);
				escapeshellcmd($command2);
				passthru($command);
				passthru($command2);
				
				if(!copy($file_recieve, $voice_file)){
					$error =  TTS_FILE_SAVE_FAILED;
					$msg->addError($error);
				}
				
				//gen_tts();
			
			}else if($_POST['file_type'] == "ogg"){	
				$file_out = AT_SPEECH_DIR.$lang_var.'.wav';
			
				if($_POST['type'] == "sable"){
					$file_in =  AT_SPEECH_DIR.$lang_var.'.sable';				
			
					$sable_out = '<?xml version="1.0"?>
					<!DOCTYPE SABLE PUBLIC "-//SABLE//DTD SABLE speech mark up//EN" "Sable.v0_2.dtd"[]>
					<SABLE>
					<LANGUAGE ID="'.$_POST['language'].'" CODE="ISO-8859-1">
					<SPEAKER NAME="'.$_POST['speaker'].'">
					<PITCH BASE="'.$_POST['base'].'" MIDDLE="'.$_POST['middle'].'" RANGE="'.$_POST['range'].'">
					<RATE SPEED="'.$_POST['rate'].'">
					<VOLUMN LEVEL="'.$_POST['volumn'].'">';
						$sable_out .= $row[3].".";
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
						echo _AT(AT_ERROR_TTS__NOT_CREATE_SABLE);
						exit;
					}
					
					fputs($fp, $sable_out);
					fclose($fp);
					$command = "text2wave $file_in -o $file_out -F 48000";

				} 
				
				$file_props = "-mode --tts -eval ".AT_SPEECH_DIR.$now.".scm";
				$file_out_ogg = AT_SPEECH_DIR.$lang_var.'.ogg';
				$command2 = 'oggenc -quiet '.$file_out.' '. $file_out_ogg;
				
				escapeshellcmd($command);
				escapeshellcmd($command2);
				passthru($command);
				passthru($command2);
				
				if(!copy($file_recieve, $voice_file)){
					$error =  TTS_FILE_SAVE_FAILED;
					$msg->addError($error);
				}
				
				//gen_tts();
				
			}else if($_POST['file_type'] == "wav"){
				$error =  TTS_NO_WAV_ALLOWED;
				$msg->addError($error);	
			}
		}
		
		$feedback =  array(TTS_VOICE_SAVED);
		$msg->addFeedback($feedback);		
	}

}else if($_POST['remove'] && !$_GET['page']){

		if(!$_POST['check']){
			$error =  array(TTS_FILES_REMOVED_CHECK);
			$msg->addError($error);
		}else{
		
			foreach ($_POST['check'] as $lang_var){
				if(file_exists(AT_SPEECH_TEMPLATE_DIR.$lang_var.".ogg")){
				
					unlink(AT_SPEECH_TEMPLATE_DIR.$lang_var.".ogg");	
					
				}else if(file_exists(AT_SPEECH_TEMPLATE_DIR.$lang_var.".mp3")){
				
					unlink(AT_SPEECH_TEMPLATE_DIR.$lang_var.".mp3");
					
				};
				
			}
			$feedback =  array(TTS_FILES_REMOVED);
			$msg->addFeedback($feedback);
			
		}

}
?>