<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $include_all, $include_one;
global $savant;
global $db;

$reading_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list R INNER JOIN ".TABLE_PREFIX."external_resources E ON E.resource_id = R.resource_id WHERE R.course_id=$_SESSION[course_id] ORDER BY R.reading_id DESC LIMIT $reading_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$reading_list[] = array('sub_url' =>$_base_path.url_rewrite('reading_list/display_resource.php?id=' . $row['resource_id']) , 'sub_text' => $row['title']); 
	}
	return $reading_list;	
} else {
	return 0;			//si ritorna 0 nel caso in cui il modulo corrente non possieda dei sottocontenuti
}

?>