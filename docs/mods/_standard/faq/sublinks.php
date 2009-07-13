<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$post_limit = 3;	//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM ".TABLE_PREFIX."faq_topics T INNER JOIN ".TABLE_PREFIX."faq_entries E ON T.topic_id = E.topic_id WHERE T.course_id = $_SESSION[course_id] ORDER BY E.revised_date DESC LIMIT $post_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('faq/index.php#'.$row['entry_id']).'"'.
		          (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.$row['question'].'"' : '') .'>'. 
		          validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;
	
} else {
	return 0;
}


?>