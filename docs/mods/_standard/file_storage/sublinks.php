<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path, $include_all, $include_one;
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
		$file_list[] = array('sub_url' => $_base_path.url_rewrite('file_storage/index.php?download='.$row['file_id']).SEP.'files='. $row['file_id'], 'sub_text' => $filetext); 
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id']).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.$filetext.'"' : '') .'>'. 
		          validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>