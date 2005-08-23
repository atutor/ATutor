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
// $Id: atalkerlib.inc.php 5123 2005-07-12 14:59:03Z greg

// Common functions used thoughout ATalker

// Garbage collector
 function clean_tts_files(){
 	$filelife = "1200"; //1200 seconds = 20 minutes
 	if ($handle = opendir(AT_SPEECH_DIR)){
 		while (false !== ($file = readdir($handle))) {
 			// echo "$file\n";
 			$rawfile = split("\.", $file);
 			if($rawfile[0] != ''){
 				$dir_files[$i] .= "$rawfile[0]\n";
 				$this_now = time();
 				if($this_now - $rawfile[0] > $filelife){
 					unlink(AT_SPEECH_DIR.$file);
 				}
 			$i++;
 			}
 		}  
 	}
 }

// Manage ATalker tabs and popup window


// 	$tabs = get_atalker_tabs();
// 	$num_tabs = count($tabs);
// 	if ($_REQUEST['tab']) {
// 		$tab = $_REQUEST['tab'];
// 	}
// 
// 
// 
// 	if ((isset($_REQUEST['popup']))  &&  ($_REQUEST['popup'] == TRUE)) {
// 		$popup = TRUE;
// 		$popup_win = "popup=1";
// 	} 


// function show_atalker_tabs(){
// 	$tabs = get_atalker_tabs();
// 	$num_tabs = count($tabs);
// 	if ($_REQUEST['tab']) {
// 		$tab = $_REQUEST['tab'];
// 	}
// }
// 
// function atalker_popup(){
// 	if ((isset($_REQUEST['popup']))  &&  ($_REQUEST['popup'] == TRUE)) {
// 		$popup = TRUE;
// 		$popup_win = "popup=1";
// 	} 
// }

// Read feedback messages out load
function read_messages($messages, $vals){
	global $_base_href, $course_base_href, $msg, $play, $val, $db;
 	foreach ($messages as $item){
		$sql = "SELECT * from ".TABLE_PREFIX."language_text WHERE language_code = '$_SESSION[lang]' AND term = '$item'";
		$result = mysql_query($sql, $db);
		
		while($row = mysql_fetch_row($result)){

			$file_in =  AT_MSGS_DIR.$row[2].'.txt';
			$file_out =  AT_MSGS_DIR.$row[2].'.wav';	
			$file_out_mp3 = AT_MSGS_DIR.$row[2].'.mp3';
			$file_recieve = AT_MSGS_URL.$row[2].'.mp3';

			if(file_exists($file_out_mp3)){
				$_SESSION['file_recieve'] = $file_recieve;
				echo  '<embed src="'.$file_recieve.'" autostart="true" hidden="true" volumn="10" />';			
				//exit;		
			}else{
				$fp = fopen($file_in,'w');
				if (!$fp) {
					echo 'Unable to create '.$name.' text file.';
					exit;
				}
				$message = strip_tags($row[3]);
				$message = str_replace("%s", $vals[$row[2]], $message);
				fputs($fp, $message);
				fclose($fp);
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
				echo '<embed src="'.$file_recieve.'" autostart="true" hidden="true"  volumn="10" />';			
			}
		}
	}
}


// List tabs for the ATalker 
function get_atalker_tabs() {
	//these are the _AT(x) variable names and their include file
	$tabs[0] = array('text_reader',   'index.php', '');
	$tabs[1] = array('sable_reader', 'index.php', '');
	return $tabs;
}
	
// Check to see what encoders are available: currently supported encoders include: lame. bladeenc, oggenc
function get_encoders(){
	global $select, $_POST;
	$command = 'oggenc --version';
	$command = escapeshellcmd($command);
	if(shell_exec($command) != ''){
		echo '<option value="ogg"';
		if($_POST['file_type'] == 'ogg'){ 
			echo $select; 
		}
		echo '>OGG</option>';	
		}
	$command2 = 'bladeenc -h';
	$command2 = escapeshellcmd($command2);
	$command3 = 'lame --version';
	$command3 = escapeshellcmd($command3);
	if(shell_exec($command2) != '' || shell_exec($command3) != ''){
		echo '<option value="mp3"';
		if($_POST['file_type'] == 'mp3'){ 
			echo $select; 
		}
		echo '>MP3</option>';	
	}
}

// Send the user to the right place after each TTS action
function gen_tts(){
	global $_POST, $now, $file_recieve, $error, $postdata, $msg, $file_save, $filename, $tab, $voice_file, $lang_var, $messages;
	//echo $voice_file;
	//exit;
	if(!$error){
		if($_POST['download']){
			//unset($_POST['export'])
			header('Content-type: audio/x-'.$_POST['file_type']);
			header('Content-Disposition: attachment; filename="'.$now.'.'.$_POST['file_type']);
			readfile($file_recieve);
			
		}else if($_POST['save']){
		
			if(@fopen($file_save, r)){
				$error=  array(TTS_FILE_EXISTS, $filename);
				$msg->addError($error);
			}else{
				if(copy($file_recieve, $file_save)){
					$feedback =  array(TTS_FILE_SAVED, $filename);
					$msg->addFeedback($feedback);
				}else{
					$error =  array(TTS_FILE_SAVE_FAILED, $filename);
					$msg->addError($error);
				}
			}
			unset($_POST['save']);
		}else if($_POST['export']){
			unset($_POST['export']);
			header('Content-type: text/plain');
			header("Location:".AT_SPEECH_URL.$now.".sable");
		
		}else{
		
			//header('Content-type: audio/x-'.$_POST['file_type']);
			header('Content-type: audio/x-'.$_POST['file_type']);
			header("Location:".$file_recieve);
		}
	}
}

?>
