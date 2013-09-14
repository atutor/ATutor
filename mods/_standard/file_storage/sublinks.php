<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$file_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT file_id, file_name, description FROM %sfiles WHERE owner_id=%d ORDER BY date DESC LIMIT %d";
$rows_files = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $file_limit));

if(count($rows_files) > 0){
	foreach($rows_files as $row){
		if($row['description'] !=""){
			$filetext = $row['description'];
		}else{
			$filetext = $row['file_name'];
		}

		$list[] = '<a href="'.url_rewrite('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($filetext, 'input.text').'"' : '') .'>'. 
		          AT_print(validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'input.text') .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>
