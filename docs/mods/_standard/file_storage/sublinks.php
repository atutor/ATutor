<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$file_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT file_id, file_name, description FROM ".TABLE_PREFIX."files WHERE owner_id=$_SESSION[course_id] ORDER BY date DESC LIMIT $file_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		if($row['description'] !=""){
			$filetext = $row['description'];
		}else{
			$filetext = $row['file_name'];
		}

		$list[] = '<a href="'.url_rewrite('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($filetext, 'text.input').'"' : '') .'>'. 
		          AT_print(validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'text.input') .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>