<?php
//this file gets called into the end of the _AT function in include/lib/output.inc.php  to wrap a mouseover around language iteams that
// have an existing voice file.  

define('AT_SPEECH_TEMPLATE_DIR', AT_CONTENT_DIR.'template/'.$_SESSION['lang'].'/');
function read_template($outString){
global $format;
//define('AT_SPEECH_TEMPLATE_DIR', AT_CONTENT_DIR.'template/'.$_SESSION['lang'].'/');
	if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.mp3')){
		$outString ='<span onMouseOver="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';
		
$embed[$format] .='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.mp3" autostart="false" hidden="true" volumn="10" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>';

	}
return $outString;
}

// 		/////wrap a speech mouseover if audio file exists
// 
// 		define('AT_SPEECH_TEMPLATE_DIR', AT_CONTENT_DIR.'template/'.$_SESSION['lang'].'/');
// 
// 		if(file_exists(AT_SPEECH_TEMPLATE_DIR.$format.'.mp3')){
// 			$outString ='<span onMouseOver="javascript:evalSound(\''.$format.'\')" onfocus="javascript:evalSound(\''.$format.'\')"> '.$outString.'</span>';
// 			
// 
// 			$embed[$format] .='<embed src="'.AT_SPEECH_TEMPLATE_URL.$format.'.mp3" autostart="false" hidden="true" volumn="10" id="'.$format.'"  name="'.$format.'" enablejavascript="true"></embed>';
// 
// 		}
?>