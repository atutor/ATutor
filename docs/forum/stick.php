<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

$pid = intval($_GET['pid']);

/* ABS(sticky-1) : if 1 then 0, if 0 then 1 */
$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET sticky=ABS(sticky-1) WHERE post_id=$pid";
$result = mysql_query($sql, $db);

$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

header('Location: '.$_base_href.'forum/index.php?fid='.intval($_GET['fid']));
exit;

?>