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

define('AT_INCLUDE_PATH', '../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

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
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=$ordering WHERE content_id=$row[content_id]";
		mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_UPDATE, 'content', mysql_affected_rows($db), $sql);
		
	}

	 echo "\n";

	$ordering++;
	
}

$savant->assign('ordering', $ordering);
$savant->assign('content_id', $content_id);	
$savant->assign('content_parent_id', $content_parent_id);

echo' </pre></div>';
$savant->display('admin/fix_content.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php');
?>