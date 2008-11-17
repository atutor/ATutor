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

// Ewiki Module for ATutor - Version: .02
if (isset($_GET['top'])) {
	header('Location: index.php');
	exit;
}
	$page = 'wiki';
	define('AT_INCLUDE_PATH', '../../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

	include_once(AT_INCLUDE_PATH."../mods/wiki/config.php");
	$content =  ewiki_page();
	$content = stripslashes($content);

	$_pages['mods/wiki/index.php']['title']  = 'Wiki';
	$_pages['mods/wiki/index.php']['children'] = array('mods/wiki/page.php?edit/'.$ewiki_title);

	$_pages['mods/wiki/page.php?edit/'.$ewiki_title]['title'] = 'Edit This Page';
	$_pages['mods/wiki/page.php?edit/'.$ewiki_title]['parent'] ='mods/wiki/index.php';

	//$_pages['mods/wiki/page.php?page/'.$ewiki_title]['title'] = 'Edit This Page';
	//$_pages['mods/wiki/page.php?page/'.$ewiki_title]['parent'] ='mods/wiki/index.php';

	
// 	$_pages['./tools/wiki/index.php']['title']  = 'Wiki Home';
// 	$_pages['tools/wiki/index.php']['title']  = "$ewiki_title"; //'wiki';
// 	$_pages['tools/wiki/index.php?page='.$_GET['page'].'']['parent']    = 'tools/wiki/index.php';
// 	$_pages['tools/wiki/index.php']['children']  = array('./tools/wiki/index.php');

	$_custom_css = $_base_path . 'mods/wiki/module.css';
	require (AT_INCLUDE_PATH.'header.inc.php');

	// Display the Toolbar at the top of the page too, after making $o global in ewiki_control_links_list()
 if($o){
		echo '<div class="wiki-plugins" align="right" style="margin-right:1em;">';
		echo $o;
		echo '</div><hr>';
	}
	echo $content;
	// Show the Calendar if the page displayed has one associated with it.
    	 if (function_exists("calendar_exists") && calendar_exists()) {
      		  echo  calendar();
   	  }

 require(AT_INCLUDE_PATH.'footer.inc.php');
 ?>
