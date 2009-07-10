<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $_base_path, $include_all, $include_one;
global $savant;
global $db;

$links_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page

$sql = "SELECT * FROM ".TABLE_PREFIX."links L INNER JOIN ".TABLE_PREFIX."links_categories C ON C.cat_id = L.cat_id WHERE owner_id=$_SESSION[course_id] ORDER BY SubmitDate DESC LIMIT $links_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('links/index.php?view='.$row['link_id']).'"'.
		          (strlen($row['LinkName']) > SUBLINK_TEXT_LEN ? ' title="'.$row['LinkName'].'"' : '') .'>'. 
		          validate_length($row['LinkName'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;	
} else {
	return 0;
}
?>