<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path;
global $db;

$polls_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT poll_id, question FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY created_date DESC LIMIT $polls_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {   
		$polls_list[] = array('sub_url' => $_base_path.url_rewrite('polls/index.php#'.$row['poll_id']) , 'sub_text' => $row['question']); 
	}
	return $polls_list;	
} else {
	return 0;
}

?>