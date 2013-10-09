<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

$reading_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM %sreading_list R INNER JOIN %sexternal_resources E ON E.resource_id = R.resource_id WHERE R.course_id=%d ORDER BY R.reading_id DESC LIMIT %d";
$rows_readings = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $reading_limit));

if(count($rows_readings) > 0){
	foreach($rows_readings as $row){
		$list[] = '<a href="'.url_rewrite('mods/_standard/reading_list/display_resource.php?id=' . $row['resource_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'. 
		          validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;
} else {
	return 0;			//si ritorna 0 nel caso in cui il modulo corrente non possieda dei sottocontenuti
}

?>
