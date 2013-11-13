<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $contentManager;

$record_limit = 3;		// number of sublinks to display at module home "detail view"

$sql = "SELECT content_id FROM %smember_track WHERE course_id=%d AND member_id=%d ORDER BY last_accessed DESC limit %d";
$rows_tracks = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_SESSION['member_id'], $record_limit));

if(count($rows_tracks) > 0){
	foreach($rows_tracks as $row){
		$link_title = $contentManager->_menu_info[$row['content_id']]['title'];
		
		$list[] = '<a href="'.url_rewrite('content.php?cid='.$row['content_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.$link_title.'"' : '') .'>'. 
		          validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>