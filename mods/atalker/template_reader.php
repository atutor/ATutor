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
// SPAN element with a mouseover/onfocus around 

	global $atalker_on;
	//$atalker_on = '1';
		//if($atalker_on){

		if($_SESSION['atalker_on'] == '1'){
			global $_base_path, $_base_href, $_SESSION;
			define('AT_SPEECH_TEMPLATE_DIR', $_SERVER['DOCUMENT_ROOT'].$_base_path.'content/template/'.$_SESSION['lang'].'/');
			define('AT_SPEECH_TEMPLATE_URL', $_base_href.'content/template/'.$_SESSION['lang'].'/');
			if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.ogg')){
				$outString ='<span onmouseover="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';

				$embed[$format] .='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.ogg" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>';

			}else if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.mp3')){
				$outString ='<span onmouseover="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';

				$embed[$format] .='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.mp3" autostart="false" hidden="true" volumn="8" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>';

			}
		}
?>