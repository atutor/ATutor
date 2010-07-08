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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

$pid = intval($_GET['pid']);

/* ABS(sticky-1) : if 1 then 0, if 0 then 1 */
$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET sticky=ABS(sticky-1), last_comment=last_comment, date=date WHERE post_id=$pid";
$result = mysql_query($sql, $db);

$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

header('Location: '.AT_BASE_HREF.'mods/_standard/forums/forum/index.php?fid='.intval($_GET['fid']));
exit;

?>