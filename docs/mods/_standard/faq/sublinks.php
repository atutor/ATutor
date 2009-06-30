<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
global $_base_path;

$post_limit = 3;	//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM ".TABLE_PREFIX."faq_topics T INNER JOIN ".TABLE_PREFIX."faq_entries E ON T.topic_id = E.topic_id WHERE T.course_id = $_SESSION[course_id] ORDER BY E.revised_date DESC LIMIT $post_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$faq_list[] = array('sub_url' => $_base_path.url_rewrite('faq/index.php#'.$row['entry_id']) , 'sub_text' => $row['question']); 
	}
	return $faq_list;
	
} else {
	return 0;
}


?>