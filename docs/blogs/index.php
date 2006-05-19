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
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT G.group_id, G.title, G.modules FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$_SESSION[course_id] ORDER BY G.title";
$result = mysql_query($sql, $db);

echo '<ol id="tools">';

while ($row = mysql_fetch_assoc($result)) {
	if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
		echo '<li class="top-tool"><a href="blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$row['group_id'].'">'.$row['title'].'</a></li>';
	}
}
echo '</ol>';
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>