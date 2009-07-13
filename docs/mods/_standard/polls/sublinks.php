<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$polls_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT poll_id, question FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY created_date DESC LIMIT $polls_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {   
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('polls/index.php#'.$row['poll_id']).'"'.
		          (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.$row['question'].'"' : '') .'>'. 
		          validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;	
} else {
	return 0;
}

?>