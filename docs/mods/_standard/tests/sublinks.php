<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$tests_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT test_id, title, UNIX_TIMESTAMP(start_date) AS sd, UNIX_TIMESTAMP(end_date) AS ed FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY end_date DESC";
$result = mysql_query($sql, $db);

$cnt = 0;

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		if ( ($row['sd'] <= time()) && ($row['ed'] >= time() ) && $cnt < $tests_limit)		//Solo i test "On Going" dovranno essere visualizzati, per questo vengono controllate le date di inizio e fine in riferimento alla data odierna
			$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('tools/test_intro.php?tid=' . $row['test_id']).'"'.
			          (strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'. 
			          validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
			$cnt++; 
	}
	return $list;	
} else {
	return 0;
}

?>