<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id$

$page	 = 'search';
$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

require(AT_INCLUDE_PATH . 'lib/search.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');


echo '<h2>'._AT('search').'</h2>';

$msg->addHelp('SEARCH_ALL_PUBLIC');
$msg->printAll();

require(AT_INCLUDE_PATH.'html/search.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>