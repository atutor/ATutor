<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$post_limit = 3;	//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM %sfaq_topics T INNER JOIN %sfaq_entries E ON T.topic_id = E.topic_id WHERE T.course_id = %d ORDER BY E.revised_date DESC LIMIT %d";
$rows_faqs = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $post_limit));

if(count($rows_faqs) > 0){
    foreach($rows_faqs as $row){
		$list[] = '<a href="'.url_rewrite('mods/_standard/faq/index.php#'.$row['entry_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($row['question'], 'faqs.question').'"' : '') .'>'. 
		          AT_print(validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'faqs.question') .'</a>'; 
	}
	return $list;	
} else {
	return 0;
}


?>