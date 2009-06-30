<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $include_all, $include_one;
global $savant;
global $db;

$tests_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT test_id, title, UNIX_TIMESTAMP(start_date) AS sd, UNIX_TIMESTAMP(end_date) AS ed FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY end_date DESC LIMIT $tests_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		if ( ($row['sd'] <= time()) && ($row['ed'] >= time() ))		//Solo i test "On Going" dovranno essere visualizzati, per questo vengono controllate le date di inizio e fine in riferimento alla data odierna
			$tests_list[] = array('sub_url' => $_base_path.url_rewrite('tools/test_intro.php?tid=' . $row['test_id']) , 'sub_text' => $row['title']); 
	}
	return $tests_list;	
} else {
	return 0;
}

?>