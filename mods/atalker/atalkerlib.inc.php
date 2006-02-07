<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 				        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: atalkerlib.inc.php 5123 2005-07-12 14:59:03Z greg

// Common functions etc used thoughout ATalker


// Setup the speech directories if they don't yet exist
if($_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){

	// where admin audio files are saved
	define('AT_SPEECH_TEMPLATE_ROOT', AT_CONTENT_DIR.'template/');
	define('AT_SPEECH_TEMPLATE_DIR', AT_CONTENT_DIR.'template/'.$_SESSION['lang'].'/');
	define('AT_SPEECH_TEMPLATE_URL', $_base_href.'content/template/'.$_SESSION['lang'].'/');
	define('AT_SPEECH_FILES_DIR', AT_CONTENT_DIR.'template/temp/'); 
	define('AT_SPEECH_URL', $_base_href.'content/template/temp/');
	define('AT_SPEECH_DIR', AT_CONTENT_DIR.'template/temp/');

	// See if the speech directories exists yet, and create them if they don't	
 	if(!opendir(AT_SPEECH_TEMPLATE_ROOT)){
 			mkdir(AT_SPEECH_TEMPLATE_ROOT, 0755);
 	}
	if(!opendir(AT_SPEECH_DIR)){
			mkdir(AT_SPEECH_DIR, 0755);
	}
	
	if(!opendir(AT_SPEECH_FILES_DIR)){
				mkdir(AT_SPEECH_FILES_DIR, 0755);
	}
	
	if(!opendir(AT_SPEECH_TEMPLATE_DIR)){
			mkdir(AT_SPEECH_TEMPLATE_DIR, 0755);
	}	 

}else{
	define('AT_SPEECH_DIR', AT_CONTENT_DIR.'speech/');
	define('AT_SPEECH_FILES_DIR', AT_CONTENT_DIR.$_SESSION['course_id'].'/speech/'); 
	define('AT_SPEECH_URL', $_base_href.'content/speech/');
	// See if the speech directories exists yet, and create them if they don't	
	if(@!opendir(AT_SPEECH_DIR)){
			mkdir(AT_SPEECH_DIR, 0755);
	}
	if($_SESSION['course_id'] != "0"){
		if(@!opendir(AT_SPEECH_FILES_DIR)){
				mkdir(AT_SPEECH_FILES_DIR, 0755);
			}
	}

}

//Validate filename form field
if($_POST['filename']){
	if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_\.])*$", $_POST['filename']))) {
				$error = "TTS_FILE_CHARS";
				$msg->addError($error);
	}
}

if($_GET['postdata']){
    	$postdata = stripslashes($_GET['postdata']);
	$_POST = unserialize($postdata);
	
 }else{
 			
   	$postdata  = serialize($_POST);
}

// Garbage collector: delete tempfiles after $filelife seconds
 function clean_tts_files(){
	//global AT_SPEECH_DIR;
 	$filelife = "1200"; //1200 seconds = 20 minutes
 	if ($handle = opendir(AT_SPEECH_DIR)){
 		while (false !== ($file = readdir($handle))) {
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
clean_tts_files();

// Manage ATalker tabs and popup window
 	if ((isset($_REQUEST['popup']))  &&  ($_REQUEST['popup'] == TRUE)) {
 		$popup = TRUE;
 		$popup_win = "popup=1";
 	} 


// Read feedback messages out load

/**
*	Create scm file to pass to text2wave to define the voice, the volumn and the speed of the wave to build
	@access private
	@author Eura Ercolani
	@return The complete path of the generated file
**/

function build_scm_file()
{
	global $db;
	
	//get voice info from the table AT_languages and AT_voices
	$sql_command = "SELECT voice_name, voice_volumn, voice_speed FROM ".TABLE_PREFIX."languages, ".TABLE_PREFIX."voices WHERE language_code='";
	$sql_command .= $_SESSION[lang]."' AND ".TABLE_PREFIX."languages.voice_id = ".TABLE_PREFIX."voices.voice_id";
	$result = mysql_query($sql_command, $db);
	$row = mysql_fetch_row($result);
	$voice_name = $row[0]; //name of the voice to use
	$volumn = $row[1]; //range value between 1(low) and 10 (high)
	$speed = $row[2]; //range value between 0.6 (very fast) and 2.0 (very slow)
	$scheme_file_txt .= "(";
	$scheme_file_txt .= $voice_name;
	$scheme_file_txt .= ")\n";
	$scheme_file_txt .= "(";
	$scheme_file_txt .= "Parameter.set 'Duration_Stretch ".$speed;
	$scheme_file_txt .= ")";
	
	//Define the scm file name
	$now = time();
	$scheme_file_name = AT_MSGS_DIR.$_SESSION[lang].DIRECTORY_SEPARATOR.$now.'.scm';
	//Open the file for output
	$fp = fopen($scheme_file_name,'w');
	if (!$fp) 
	{
		echo _AT(AT_ERROR_TTS__NOT_CREATE_SCHEME);
		exit;
	}
	//Write into the file
	fputs($fp, $scheme_file_txt);
	fclose($fp);
	
	$voice_info[0] = $scheme_file_name;
	$voice_info[1] = $volumn;
	
	return $voice_info;
	 
}

/**
*  Reads aloud  error and feedback messages
*  @ access  public
*  @param array $messages      a list of messages sent to the  $msg->printAll() function; 
*  @param  array $vals  	      a list of subistute (i.e.for %s within language) values for dynamic messages
*  @author  Greg Gay
*/


function read_messages($messages, $vals){
	global $_base_href, $course_base_href, $msg, $play, $val, $db;
	/* Modified by Eura Ercolani: mimetype support - BEGIN */
	
	/* Modified by Eura Ercolani: mimetype support - END */
 	foreach ($messages as $item){
		$sql = "SELECT * from ".TABLE_PREFIX."language_text WHERE language_code = '$_SESSION[lang]' AND term = '$item'";
		$result = mysql_query($sql, $db);
		
		while($row = mysql_fetch_row($result)){

			/* Modified by Eura Ercolani: messages localization - BEGIN */
			//check to see if the folder exists....
			if(!is_dir(AT_MSGS_DIR.DIRECTORY_SEPARATOR.$_SESSION[lang]))//folder does not exists, I make it
				mkdir(AT_MSGS_DIR.$_SESSION[lang]);
			//$file_in =  AT_MSGS_DIR.$row[2].'.txt';
			$file_in =  AT_MSGS_DIR.$_SESSION[lang].DIRECTORY_SEPARATOR.$row[2].'.txt';
			//$file_out =  AT_MSGS_DIR.$row[2].'.wav';
			$file_out =  AT_MSGS_DIR.$_SESSION[lang].DIRECTORY_SEPARATOR.$row[2].'.wav';
			//$file_out_mp3 = AT_MSGS_DIR.$row[2].'.mp3';
			$file_out_mp3 = AT_MSGS_DIR.$_SESSION[lang].DIRECTORY_SEPARATOR.$row[2].'.mp3';
			//$file_recieve = AT_MSGS_URL.$row[2].'.mp3';
			$file_recieve = AT_MSGS_URL.$_SESSION[lang].DIRECTORY_SEPARATOR.$row[2].'.mp3';
			/* Modified by Eura Ercolani: messages localization - END */

			if(file_exists($file_out_mp3)){

				/* Modified by Eura Ercolani: mime type support - BEGIN */
				//echo  '<embed src="'.$file_recieve.'" autostart="true" hidden="true" volumn="10" ></embed>';			
				echo  '<embed src="'.$file_recieve.'" autostart="true" height="0" width="0" volumn="10" type="'.$_SESSION['mp3HiddenMimeType'].'"></embed>';
				/* Modified by Eura Ercolani: mime type support - END */
				

			}else{
				$fp = fopen($file_in,'w');

				if (!$fp) {
					echo AT_ERROR_TTS_NOT_CREATE_TEXT;
					exit;
				}

				$message = strip_tags($row[3]);
				$message = str_replace("%s", $vals[$row[2]], $message);
				fputs($fp, $message);
				fclose($fp);
				/* Modified by Eura Ercolani: voice setting - BEGIN */
				$voice_info = build_scm_file();
				$command = "text2wave ".$file_in." -o ".$file_out;
				$command .= " -F 48000 -scale ".$voice_info[1]." -eval ".$voice_info[0]; 
				//$command = "text2wave $file_in -o $file_out -F 48000";
				/* Modified by Eura Ercolani: voice setting - BEGIN */
	
				if(shell_exec('lame --longhelp')){

					$command2 .= ' lame --quiet '.$file_out.' '. $file_out_mp3;

				}else if (shell_exec('bladeenc -h')) {

					$command2 .= ' bladeenc -quiet '.$file_out.' '. $file_out_mp3;	
				}
				
				escapeshellcmd($command);
				escapeshellcmd($command2);
				passthru($command);

				passthru($command2);
				/* Modified by Eura Ercolani: mimetype support - BEGIN */
				//echo '<embed src="'.$file_recieve.'" autostart="true" hidden="true"  volumn="10" ></embed>';	
				echo '<embed src="'.$file_recieve.'" autostart="true" height="0" width="0"  volumn="10" type="'.$_SESSION['mp3HiddenMimeType'].'"></embed>';	
				/* Modified by Eura Ercolani: mimetype support - END */
				unlink($file_in);	
				unlink($file_out);
				/* Modified by Eura Ercolani: delete scm file - BEGIN */
				unlink($voice_info[0]);
				/* Modified by Eura Ercolani: delete scm file - END */
			}
		}
	}
}


// List tabs for the ATalker 
function get_atalker_tabs() {
	//these are the _AT(x) variable names and their include file
	$tabs[0] = array('text_reader',   'index.php', '');
	$tabs[1] = array('sable_reader', 'index.php', '');
	if($_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){
		$tabs[2] = array('voice_files', 'index.php', '');
	}
	
return $tabs;
}
	

// Check to see what encoders are available: currently supported encoders include: lame. bladeenc, oggenc

function get_encoders(){
	global $select, $_POST;

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

	$command = 'oggenc --version';
	$command = escapeshellcmd($command);
	if(shell_exec($command) != ''){
		echo '<option value="ogg"';
		if($_POST['file_type'] == 'ogg'){ 
			echo $select; 
		}
		echo '>OGG</option>';	
		}

}

// Send the user to the right place after each TTS action
function gen_tts(){
	global $_POST, $now, $file_recieve, $error, $postdata, $msg, $file_save, $filename, $tab, $voice_file, $lang_var, $messages;
	//echo $voice_file;
	//exit;
	if(!$error && !$_GET['page']){
		if($_POST['download']){
			//unset($_POST['export'])
			header('Content-type: audio/x-'.$_POST['file_type']);
			header('Content-Disposition: attachment; filename="'.$now.'.'.$_POST['file_type']);
			readfile($file_recieve);
			
		}else if($_POST['save']){
			$file_save = str_replace(" ", "_", $file_save);


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
