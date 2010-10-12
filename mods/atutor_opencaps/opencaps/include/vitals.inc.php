<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2010 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

if (!defined('INCLUDE_PATH')) {
	define('INCLUDE_PATH', 'include/');
}

require(INCLUDE_PATH.'classes/user_class.php');
require(INCLUDE_PATH.'classes/db_class.php');
require(INCLUDE_PATH.'classes/project_class.php');
require(INCLUDE_PATH.'classes/clip_class.php');
require(INCLUDE_PATH.'classes/caption_class.php');
require(INCLUDE_PATH.'classes/time_class.php');
require(INCLUDE_PATH.'classes/system_class.php');

@session_start();

require(INCLUDE_PATH.'config.inc.php');


$supported_ext = array('mov', 'qt', 'mp4', 'm4v', 'mpg', 'mpeg', 'dv', 'mp3', 'wav', 'aac', 'midi', 'au', 'avi', 'aiff');
$pub_pages = array("index.php", "login.php", "register.php", "start.php", "start_remote.php", "workflow.php");

$page = explode('/',$_SERVER['PHP_SELF']); 
$page = end($page);

$base_url = substr('http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"], 0, -(strlen($page)));

function my_add_null_slashes( $string ) {
    return mysql_real_escape_string(stripslashes($string));
}
function my_null_slashes($string) {
	return $string;
}
if ( get_magic_quotes_gpc() ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}

if (get_magic_quotes_gpc()) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'my_null_slashes';
} else {
	$addslashes   = 'addslashes';
	$stripslashes = 'stripslashes';
}


/* 
 * check for valid user 
 */

$this_user = new user($_SESSION['mid'], $_SESSION['username']);

if (!in_array($page, $pub_pages) && (!isset($_SESSION['valid_user']) || !$_SESSION['valid_user'] || !isset($_SESSION['mid']) || !$_SESSION['mid']) ) {
	header("Location:start.php");
	exit;
}

/* check if system connection */
if (ACTIVE_SYSTEM)
	$this_system = new system(ACTIVE_SYSTEM);		


/* 
 * connect to db 
 */
if (!isset($this_db) && !DISABLE_LOCAL) 
	$this_db = new database();	

	
/*
 * load project
 */
if (isset($_SESSION['pid']) || $page=="workflow.php") {
	$this_proj = new project();
	$this_proj->owner = &$this_user;
	
	if (isset($_SESSION['pid'])) 	
		$this_proj->id = $_SESSION['pid'];
	
	if (!empty($this_proj->id))
		setVals();	
}

function setVals() {
	global $this_proj, $stripslashes;
	
	$json = json_decode($stripslashes(@file_get_contents(INCLUDE_PATH.'../projects/'.$_SESSION['pid'].'/opencaps.json')));
	
	$this_proj->id = $json->id;
	$this_proj->name = $json->name;
	//$this_proj->prefs = $json->prefs;

	$this_proj->media_loc = $json->media_loc; 
	$this_proj->media_height = $json->media_height;
	$this_proj->media_width = $json->media_width;
	$this_proj->duration = $json->duration;
	$this_proj->layout = $json->layout;
	
	$this_proj->caption_loc = $json->caption_loc;
	$this_proj->clip_collection = $json->clip_collection;

}

/* 
 * check for valid project 
 
if ($page != "index.php" && $page != "login.php" && $page != "logout.php" && $page != "register.php" && $page != "start.php" && $page != "adminTasks.php") { 
	if (!isset($_SESSION['pid']) && empty($_SESSION['pid'])) {
		echo "Project not loaded.";
		exit;
	}
} 
*/


//check if using firefox
/*if(!strstr($_SERVER['HTTP_USER_AGENT'], "Firefox")) {
	$_SESSION['errors'][] = "At this time, Capscribe Web requires a Firefox browser.";
	include(INCLUDE_PATH.'basic_header.inc.php');
	include(INCLUDE_PATH.'footer.inc.php');	
	exit;
}*/

?>