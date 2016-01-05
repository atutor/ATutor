<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

$tests_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT T.test_id, T.title, UNIX_TIMESTAMP(T.start_date) AS sd, UNIX_TIMESTAMP(T.end_date) AS ed 
          FROM %stests T, %stests_questions_assoc Q 
         WHERE Q.test_id=T.test_id 
           AND T.course_id=%d 
         GROUP BY T.test_id 
         ORDER BY T.end_date DESC";
$rows_tests = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id']));
global $_base_href;
$cnt = 0;
if(count($rows_tests) > 0){
    foreach($rows_tests as $row){
		if ( ($row['sd'] <= time()) && ($row['ed'] >= time() ) && $cnt < $tests_limit)		//Solo i test "On Going" dovranno essere visualizzati, per questo vengono controllate le date di inizio e fine in riferimento alla data odierna
//			$list[] = '<a href="'.url_rewrite('mods/_standard/tests/test_intro.php?tid=' . $row['test_id'], AT_PRETTY_URL_IS_HEADER).'"'.(strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'.validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
$list[] = '<a href="'.$_base_href.'mods/_standard/tests/test_intro.php?tid=' . $row['test_id'].'"'.(strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'.validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
			$cnt++; 
	}
	return $list;	
} else {
	return 0;
}

?>