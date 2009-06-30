<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $include_all, $include_one;
global $db;

$file_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT file_id, file_name FROM ".TABLE_PREFIX."files WHERE owner_id=$_SESSION[course_id] ORDER BY date DESC LIMIT $file_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$file_list[] = array('sub_url' => $_base_path.url_rewrite('file_storage/home_view.php?fid='.$row['file_id']), 'sub_text' => $row['file_name']); 
	}
	return $file_list;	
} else {
	return 0;
}

?>