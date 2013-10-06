<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

$polls_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT poll_id, question FROM %spolls WHERE course_id=%d ORDER BY created_date DESC LIMIT %d";
$rows_polls = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $polls_limit));

if(count($rows_polls) > 0){
	foreach($rows_polls as $row){
		$list[] = '<a href="'.url_rewrite('mods/_standard/polls/index.php#'.$row['poll_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'. AT_print($row['question'], 'polls.question').'"' : '') .'>'. 
		          AT_print(validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'polls.question') .'</a>'; 
	}
	return $list;	
} else {
	return 0;
}

?>