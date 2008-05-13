<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: redirect.php 7208 2008-01-09 16:07:24Z harris $

define('AT_INCLUDE_PATH', 'include/');
define('AT_REDIRECT_LOADED', true);

$_user_location	= 'public';  //like browse, and registration, doesn't need username/passwords to get into

require_once(AT_INCLUDE_PATH . 'classes/UrlRewrite/UrlParser.class.php');
include_once(AT_INCLUDE_PATH.'config.inc.php');
require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
//require(AT_INCLUDE_PATH.'vitals.inc.php');

//mimic config variables, vitals.inc.php 135-140
/* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */
$sql    = "SELECT * FROM ".TABLE_PREFIX."config";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) { 
	$_config[$row['name']] = $row['value'];
}

$pathinfo = $_SERVER['PATH_INFO'];
$url_parser = new UrlParser($pathinfo);
$path_array =  $url_parser->getPathArray();
$_pretty_url_course_id = $path_array[0];
$obj = $path_array[1];
//debug($obj);exit;
//check if we are in the requested course, if not, bounce to it.
//if ($_SESSION['course_id'] != $course_id){
//	debug('why am i being loaded..stop it stop it!!!!!!');exit;
//	header('Location: '.AT_BASE_HREF.'bounce.php?course='.$course_id);
//	exit;
//}

if (!$obj->isEmpty()){
	/* 
	 * Addresses the issue for relative uri 
	 * @refer to constants.inc.php $_rel_link
	 */
	$_rel_url = $obj->redirect();

	$var_query = $obj->parsePrettyQuery();
	save2Get($var_query);	//remake all the _GET and _REQUEST variables so that the vitals can use it
	$_user_location	= '';	//reset user_location so that the vital file in each page would validate
	$pretty_current_page = $obj->getPage();
	if (!@include($obj->getPage())){
		header('location: '.AT_BASE_HREF.'index.php');
		exit;
	} 
} elseif ($_pretty_url_course_id==0) {
	return;
//	header('location: '.AT_BASE_HREF.'bounce.php?course=0');
//	exit;
} elseif ($_pretty_url_course_id != ''){
	header('location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id);
	exit;
}

function save2Get($var_query){
	if (empty($var_query) || !is_array($var_query))
		return;
	foreach($var_query as $k=>$v){
		if ($k=='page_to_load'){
			continue;
		}
		$_GET[$k] = $v;
		$_REQUEST[$k] = $v;
	}
}
?>