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
// $Id: index.php 5123 2005-07-12 14:59:03Z joel $
//$_user_location	= 'public';

	$page = 'wiki';
	define('AT_INCLUDE_PATH', '../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

	//authenticate(AT_PRIV_WIKI);
	include_once("config.php");
	$content =  ewiki_page();
	$content = stripslashes($content);
	global $_pages;
	$_pages =array();


if (empty($_GET) || ($_GET['page'] == EWIKI_PAGE_INDEX)) {

	header('Location: index.php');
	exit;
}


	if ($ewiki_title != $_GET['page']) {
		$_pages['mods/wiki/page.php']['title'] ='Editing this page';
		$_pages['mods/wiki/page.php']['parent'] = 'mods/wiki/page.php?page=' . $ewiki_title;

		$_pages['mods/wiki/page.php?page=' . $ewiki_title]['title'] = $ewiki_title;
		$_pages['mods/wiki/page.php?page=' . $ewiki_title]['children'] = array('mods/wiki/page.php');
		$_pages['mods/wiki/page.php?page=' . $ewiki_title]['parent'] = 'mods/wiki/index.php';

	} else {
		$_pages['mods/wiki/page.php']['title'] = $ewiki_title;
		$_pages['mods/wiki/page.php']['parent'] = 'mods/wiki/index.php';
		$_pages['mods/wiki/page.php']['children'] = array('mods/wiki/page.php?edit/'. $_GET['page']);
		$_pages['mods/wiki/page.php?edit/'. $_GET['page']]['title'] = 'Edit This Page';
	}

	//$_pages['mods/wiki/page.php?edit/'. $_GET['page']]['parent'] = 'mods/wiki/page.php';

	/*
	$_pages['./tools/wiki/index.php']['title']  = 'Wiki Home';
	$_pages['mods/wiki/index.php']['title']  = "$ewiki_title"; //'wiki';
	$_pages['mods/wiki/index.php?page='.$_GET['page'].'']['parent']    = 'mods/wiki/index.php';
	$_pages['mods/wiki/index.php']['children']  = array('./tools/wiki/index.php');
*/
	$_custom_css = $_base_path . 'mods/wiki/module.css';
	require (AT_INCLUDE_PATH.'header.inc.php');

	// Display the Toolbar at the top of the page too, after making $o global in ewiki_control_links_list() 
	if($o){
		echo '<div class="wiki"><div class="wiki-plugins" align="right">';
		echo $o;
		echo '</div><hr></div>';
	}
	echo $content;   
	// Show the Calendar if the page displayed has one associated with it.
    	 if (function_exists("calendar_exists") && calendar_exists()) {
      		  echo  calendar();
   	  }

 require(AT_INCLUDE_PATH.'footer.inc.php'); 
 ?>
