<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$reading_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list R INNER JOIN ".TABLE_PREFIX."external_resources E ON E.resource_id = R.resource_id WHERE R.course_id=$_SESSION[course_id] ORDER BY R.reading_id DESC LIMIT $reading_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.url_rewrite('mods/_standard/reading_list/display_resource.php?id=' . $row['resource_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'. 
		          validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;	
} else {
	return 0;			//si ritorna 0 nel caso in cui il modulo corrente non possieda dei sottocontenuti
}

?>