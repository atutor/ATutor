<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

//Job Board base variables
define('AT_JB_BASENAME',	'mods/job_board/');
define('AT_JB_BASE',		AT_INCLUDE_PATH.'../mods/job_board/');
define('AT_JB_INCLUDE',		AT_JB_BASE.'include/');

define('AT_JB_ROWS_PER_PAGE',		10);	//row per page constant, default is 50 (same as output.inc.php)


//Employer Statuses
define('AT_JB_STATUS_UNCONFIRMED',	0);
define('AT_JB_STATUS_CONFIRMED',	1);
define('AT_JB_STATUS_SUSPENDED',	2);

//Posting Statuses
define('AT_JB_POSTING_STATUS_UNCONFIRMED',	0);
define('AT_JB_POSTING_STATUS_CONFIRMED',	1);

?>
