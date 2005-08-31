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
// $Id: message_reader.php 5123 2005-07-12 14:59:03Z greg $

/*
Add a require statement to the end of a theme header template to have ATutor
read error and feedback messages out loud. add to  themes/"theme_name"/include/header.tmpl.php
e.g.
 require(AT_INCLUDE_PATH."../mods/atalker/message_reader.php");
*/

$file_recieve = '';

 require_once(AT_INCLUDE_PATH."../mods/atalker/atalkerlib.inc.php");
 if( $_SESSION['messages_on'] == '1'){ 
	if($_SESSION['message'] ){
		define('AT_MSGS_DIR', AT_CONTENT_DIR.'/msgs/');
		define('AT_MSGS_URL', $this->base_href.'content/msgs/');
		if(@!opendir(AT_MSGS_DIR)){
				mkdir(AT_MSGS_DIR, 0700);
		}
		//if(!strstr($_SERVER['PHP_SELF'],"atalker")){
			//require(AT_INCLUDE_PATH."../mods/atalker/atalkerlib.inc.php");
		//}
		$i = '0';
		
		if($_SESSION['message']['feedback'] != ''){
			foreach($_SESSION['message']['feedback'] as $var => $val){
				//debug($_SESSION['message']['feedback'] );
				//exit;
				if(is_array($val)){
					$messages[$i]= $val[0];
					$vals[$val[$i]] = $val[1];
				}else{
					$messages[$i] = $val;
				}
				$i++;
	
			}
		}
		$i = '0';
		if($_SESSION['message']['error'] != ''){
			foreach($_SESSION['message']['error'] as $var => $val){
				if(is_array($val)){
					$messages[$i]= $val[0];
					$vals[$val[$i]] = $val[1];
				}else{
					$messages[$i] = $val;
				}
				$i++;
			}
		}
	//  	if($_SESSION['message']['info']){
	//  		foreach($_SESSION['message']['confirm'] as $var => $val){
	//  			if(is_array($val)){
	//  				$messages[$i]= $val[0];
	//  			}else{
	//  				$messages[$i] = $val;
	//  			}
	//  			$i++;
	//  		}
	//  	}
		if($messages){
			if(!$vals){
				$vals = '';
			}
			read_messages($messages, $vals);
			//unset($_SESSION['file_recieve']);
			$messages = '';
		}
	}
}
?>