<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$polls_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT poll_id, question FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY created_date DESC LIMIT $polls_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.url_rewrite('mods/_standard/polls/index.php#'.$row['poll_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'. AT_print($row['question'], 'polls.question').'"' : '') .'>'. 
		          AT_print(validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'polls.question') .'</a>'; 
	}
	return $list;	
} else {
	return 0;
}

?>