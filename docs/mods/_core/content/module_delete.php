<?php

function content_delete($course) {
	require(AT_INCLUDE_PATH.'classes/A4a/a4a.class.php');

	global $db;

	// related_content + content:
	$sql	= "SELECT content_id FROM ".TABLE_PREFIX."content WHERE course_id=$course";

	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_array($result)) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$row[0]";
		$result2 = mysql_query($sql, $db);

		$sql3	 = "DELETE FROM ".TABLE_PREFIX."member_track WHERE content_id=$row[0]";
		$result3 = mysql_query($sql3, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$row[0]";
		$result4 = mysql_query($sql, $db);

		// Delete all AccessForAll contents 
		$a4a = new A4a($row[0]);
		$a4a->deleteA4a();
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE course_id=$course";
	$result = mysql_query($sql,$db);

	$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."content";
	$result = @mysql_query($sql, $db);

}

?>