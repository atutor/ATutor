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
// $Id$

define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$_info = array('404_BLURB', htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));
$msg->printInfos($_info);

$msg->printAll();

require (AT_INCLUDE_PATH.'footer.inc.php'); 

?>
