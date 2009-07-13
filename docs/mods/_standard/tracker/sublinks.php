<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$record_limit = 3;		// number of sublinks to display at module home "detail view"

$sql = "SELECT content_id FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id] ORDER BY last_accessed DESC limit ".$record_limit;
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$link_title = $contentManager->_menu_info[$row['content_id']]['title'];
		
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('content.php?cid='.$row['content_id']).'"'.
		          (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.$link_title.'"' : '') .'>'. 
		          validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>