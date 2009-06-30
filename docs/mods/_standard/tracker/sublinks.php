<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $include_all, $include_one, $contentManager;
global $savant;
global $db;

$record_limit = 3;		// number of sublinks to display at module home "detail view"

$sql = "SELECT content_id FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id] ORDER BY last_accessed DESC limit ".$record_limit;
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = array('sub_url' => $_base_path.url_rewrite('content.php?cid='.$row['content_id']), 'sub_text' => $contentManager->_menu_info[$row['content_id']]['title']); 
	}
	return $list;	
} else {
	return 0;
}

?>