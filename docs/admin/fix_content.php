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
// $Id$

$page = 'server_configuration';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>'._AT('server_configuration').'</h3>';
echo '<h4>'._AT('fix_content_ordering').'</h4>';

echo '<div style="padding-left: 30px;"><pre>';

echo "cpID\torder\t cID";

$sql    = "SELECT content_id, content_parent_id, ordering, course_id FROM ".TABLE_PREFIX."content ORDER BY course_id, content_parent_id, ordering";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	if ($current_course_id != $row['course_id']) {
		echo "\n\n-- course id $row[course_id]\n\n";
		$current_course_id = $row['course_id'];
		unset($current_parent_id);
		unset($ordering);
	}
	echo $row['content_parent_id'] . "\t" . $row['ordering'] . "\t" . $row['content_id'];
	if ($current_parent_id != $row['content_parent_id']) {
		$current_parent_id = $row['content_parent_id'];
		$ordering = 1;
	}

	if ($row['ordering'] != $ordering) {
		echo "\t mismatch : expecting $ordering [fixed]";
		mysql_query("UPDATE ".TABLE_PREFIX."content SET ordering=$ordering WHERE content_id=$row[content_id]", $db);
	}

	 echo "\n";

	$ordering++;
}

echo' </pre></div>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>