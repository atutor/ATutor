<?php

function glossary_delete($course) {
	global $db;

	$sql	= "DELETE FROM ".TABLE_PREFIX."glossary WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>