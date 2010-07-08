<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/chat.inc.php');
	$chatID	 = $_GET['chatID'];
	$uniqueID= intval($_GET['uniqueID']);

	$myPrefs = getPrefs($_GET['chatID']);

	howManyMessages(&$topMsgNum, &$bottomMsgNum);
	if ($myPrefs['lastChecked'] < $topMsgNum && $myPrefs['lastRead'] < $topMsgNum) {
		$myPrefs['lastChecked'] = $topMsgNum;
		writePrefs($myPrefs, $chatID);
		print "yes\n";
	}
	print "$topMsgNum $myPrefs[lastChecked] $myPrefs[lastRead] \n";
?>