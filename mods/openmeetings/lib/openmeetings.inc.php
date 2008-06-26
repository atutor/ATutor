<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li, Harris Wong		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: openmeetings.inc.php 7575 2008-06-04 18:17:14Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
 * Check against the array, if the value within is empty, replace each with the
 * default values.
 * @param	array	parameter
 * @return	array 
 */
function loadDefaultValues($post){
	$_om_config = array (
		'openmeetings_roomtype'				=> 1,	//conference
		'openmeetings_num_of_participants'	=> 5,
		'openmeetings_ispublic'				=> 1,	//true
		'openmeetings_vid_w'				=> 270,
		'openmeetings_vid_h'				=> 270,
		'openmeetings_show_wb'				=> 1,	//true
		'openmeetings_wb_w'					=> 600,
		'openmeetings_wb_h'					=> 660,
		'openmeetings_show_fp'				=> 1,	//true
		'openmeetings_fp_w'					=> 270,
		'openmeetings_fp_h'					=> 270
	);

	//replace each key if empty
	foreach ($_om_config as $key=>$value){
		if (empty($post[$key])){
			$post[$key] = $value;
		}
	}

	return $post;
}


/**
 * Check if openmeeting is being setup correctly.
 * @param	int	the course id
 */
function checkAccess($course_id){
	global $_config, $msg;
	if (!isset($_config['openmeetings_username']) || !isset($_config['openmeetings_userpass'])){
		include(AT_INCLUDE_PATH.'header.inc.php');
		$msg->addError('OPENMEETINGS_NOT_SETUP');
		include(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}
?>