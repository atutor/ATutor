<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 5824 2005-12-08 16:43:32Z joel $
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$group_list = implode(',', $_SESSION['groups']);
$sql = "SELECT group_id, title, modules FROM ".TABLE_PREFIX."groups WHERE group_id IN ($group_list) ORDER BY title";
$result = mysql_query($sql, $db);

echo '<ol id="tools">';

$blog_module =& $moduleFactory->getModule('_standard/blogs');

while ($row = mysql_fetch_assoc($result)) {
	if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
		echo '<li class="top-tool"><a href="blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$row['group_id'].'">'.$row['title'].'</a></li>';
	}
}
echo '</ol>';
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>