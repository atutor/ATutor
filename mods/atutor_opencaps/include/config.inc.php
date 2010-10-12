<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

/*
 * default value = ''; 
 * set this only if using an external instace of Open Caps
  */
$ocAtSettings['ocWebPath'] = '';

/*
 * default caption format 
 */
$ocAtSettings['ccReturnFormat'] = 'SubRipSrt';  

/*
 * default file extension for caption format 
 */
$ocAtSettings['defaultCcExt'] = 'srt'; 


/*
 * Media File Formats to look for in AT File Manager
 */
$ocAtSettings['supportedMedia'] = 'mov|mp4|flv|mpg4|avi|mp3|mov|qt|mp4|m4v|mpg|mpeg|dv|mp3|wav|aac|midi|au|avi|aiff';

/*
 * default = 0; uses AT/get.php to access files
 * = 1, uses AT/content/[courseId]/[fileName]
 */
$ocAtSettings['contentUrlType'] = 1;

/*
 *  default value = false; 
 * changing this will break connectivity with Open Caps. 
 * So do NOT set this = true unless you know what you are doing
 */
$ocAtSettings['debugMode'] = false;

/*
 *  default value = false; 
 * if set to true, the module will show all AT session and cookie variables 
 */
$ocAtSettings['showAtVars'] = false; 
?>