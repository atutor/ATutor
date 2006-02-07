<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay                                                            */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: template_reader.php 5123 2005-07-12 14:59:03Z greg $

// This file is called into the _AT() function used to generate language (see include/lib/output.inc.php), to wrap a
// SPAN element with a mouseover/onfocus around it 
// e.g.  
//		global $_base_path;
//		/////wrap a speech mouseover if audio file exists
//		require($_SERVER['DOCUMENT_ROOT'].$_base_path.'mods/atalker/template_reader.php');
//
// This script accommodatesinterface sound for MSIE and Mozilla browsers. 
// See the ATalker theme header.html.php file for the evalSound and playSound javascripts

	global $atalker_on;

		if($_SESSION['atalker_on'] == '1'){

			global $_base_path, $_base_href, $_SESSION;
			define('AT_SPEECH_TEMPLATE_DIR', $_SERVER['DOCUMENT_ROOT'].$_base_path.'content/template/'.$_SESSION['lang'].'/');
			define('AT_SPEECH_TEMPLATE_URL', $_base_href.'content/template/'.$_SESSION['lang'].'/');


			// If the browser is MSIE, handle interface sound a little differently
			if(stristr( $_SERVER['HTTP_USER_AGENT'], "MSIE")){

				if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.ogg')){
					
					$outString =$outString ='<span onmouseover="playSound(\''.AT_SPEECH_TEMPLATE_URL.$format.'.ogg\')" onfocus="playSound(\''.AT_SPEECH_TEMPLATE_URL.$format.'.ogg\')"> '.$outString.'</span>';

				}else if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.mp3')){
	
					$outString ='<span onmouseover="playSound(\''.AT_SPEECH_TEMPLATE_URL.$format.'.mp3\')"  onfocus="playSound(\''.AT_SPEECH_TEMPLATE_URL.$format.'.mp3\')"> '.$outString.'</span>';
				}

			}else{
			
			// Mozilla browsers
				if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.ogg')){
					/* Modified by Eura Ercolani: mimetype support - BEGIN */
					
					//$outString ='<span onmouseover="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';
					$outString ='<span onMouseOver="evalSound(\''.$format.'\');" onMouseOut="stopSound(\''.$format.'\');"> '.$outString.'</span>';
					/* Modified by Eura Ercolani: mimetype support - END */

					if(!$embed[$format]){
	
						/* Modified by Eura Ercolani: mimetype support - BEGIN */
						//$embed[$format] ='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.ogg" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>'."\n";

						$embed[$format] ='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.ogg" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true" type="'.$_SESSION['mp3HiddenMimeType'].'"></embed>'."\n";
						/* Modified by Eura Ercolani: mimetype support - BEGIN */

						$outString .= $embed[$format];
					}
	
				}else if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.mp3')){
	
	
					/* Modified by Eura Ercolani: mimetype support - BEGIN */
					//$outString ='<span onmouseover="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';
					$outString ='<span onMouseOver="evalSound(\''.$format.'\');" onMouseOut="stopSound(\''.$format.'\')"> '.$outString.'</span>';
					/* Modified by Eura Ercolani: mimetype support - END */
	
					if(!$embed[$format]){
						/* Modified by Eura Ercolani: mimetype support - BEGIN */
						//$embed[$format] ='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.mp3" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>'."\n";
						$embed[$format] ='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.mp3" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true" type="'.$_SESSION['mp3HiddenMimeType'].'"></embed>'."\n";
						/* Modified by Eura Ercolani: mimetype support - END */
						$outString .= $embed[$format];
					}
				}
			}

		}
?>