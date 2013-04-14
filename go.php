<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', 'include/');
define('AT_REDIRECT_LOADED', true);

$_user_location	= 'public';  //like browse, and registration, doesn't need username/passwords to get into

include_once(AT_INCLUDE_PATH . 'lib/vital_funcs.inc.php');
define('AT_SITE_PATH', get_site_path());

require_once(AT_INCLUDE_PATH . 'classes/UrlRewrite/UrlParser.class.php');
include_once(AT_SITE_PATH.'include/config.inc.php');
require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
//require_once(AT_INCLUDE_PATH.'vitals.inc.php');

//mimic config variables, vitals.inc.php 135-140
/* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */
$sql    = "SELECT * FROM ".TABLE_PREFIX."config";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) { 
	$_config[$row['name']] = $row['value'];
}

//Get path info
$pathinfo = getPathInfo();

$url_parser = new UrlParser($pathinfo);
$path_array =  $url_parser->getPathArray();
$_pretty_url_course_id = $path_array[0];
$obj = $path_array[1];

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
	//If page not found, forward back to index.php
	//ignore error, cause it will give a warning: No such file error, and can't send header.
	if (!@include($pretty_current_page)){
		header('Location: '.AT_BASE_HREF.'index.php');
		exit;
	} 
} elseif ($_pretty_url_course_id==0) {
	//TODO: $_SESSION[course_id] seems to be resetted to 0, causing vitals.inc.php line 273 to redirect incorrectly.
	//		Need to find out where exactly the course_id is being resetted. 
	return;
//	header('location: '.AT_BASE_HREF.'bounce.php?course=0');
//	exit;
} elseif ($_pretty_url_course_id != ''){
	header('location: '.AT_BASE_HREF.'bounce.php?course='.$_pretty_url_course_id);
	exit;
}


/**
 * This function will reconstruct all the $_GET variables.
 * @param	array	consist of all the pathinfo variables in querystring format
 */
function save2Get($var_query){
    if (empty($var_query) || !is_array($var_query))
        return;

    //recreate URL querystring so we can use the PHP function - parse_str() later on
    foreach($var_query as $k=>$v){
        if ($k=='page_to_load'){
            continue;
        }

        //If mod_rewrite is on, the page# will be shown as <page#>.html.
        //in this case, parse the page number out.
        if ($k=='page'){
            if (preg_match('/(.*)\.html$/', $v, $matches)==1){
                $v = $matches[1];
            }
        }
        $temp[] = $k . '=' . $v;
    }
    $var_query = implode(SEP, $temp);
    parse_str($var_query, $output); //convert querystring to php array

    //saves it to both GET and REQUEST
    //TODO: Would overwrite POST value that has the same GET name within REQUEST. Fix this.
    //Avoid the use of REQUEST in the code.
    foreach($output as $k=>$v){
        $_GET[$k] = $v;
        $_REQUEST[$k] = $v;
    }
}

/**
 * Get path info
 * @return the path info string
 */
function getPathInfo(){
	//Handles path_info in diff versions of PHP 
	if(isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO']!=''){
		//if both PATH_INFO and ORIG_PATH_INFO are set, then decide which to use by str ops.
		if (isset($_SERVER['PATH_INFO'])){
			$pathpos = strpos($_SERVER['ORIG_PATH_INFO'], $_SERVER['SCRIPT_NAME']);
			$pathlen = strlen($_SERVER['SCRIPT_NAME']);
			if (substr($_SERVER['ORIG_PATH_INFO'], $pathpos + $pathlen) == $_SERVER['PATH_INFO']){
				return $_SERVER['PATH_INFO'];
			} 
		}
		return $_SERVER['ORIG_PATH_INFO'];	
	} else {
		return $_SERVER['PATH_INFO'];	
	}
}
?>