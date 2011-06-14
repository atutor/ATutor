<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

/*****
* Free form PHP can appear here to retreive current information
* from the module, or a text description of the module where there is
* not current information
*****/
require('mw_connect.php');
global $db_mw;

$link_limit = 3;		// Number of links to be displayed on "detail view" box

$sql = "SELECT P.page_title, R.rev_timestamp FROM ".MW_DB_PREFIX."page P, ".MW_DB_PREFIX."revision R ORDER BY R.rev_timestamp DESC LIMIT  $link_limit";

// $sql = "SELECT hello_world_id, value FROM ".TABLE_PREFIX."hello_world WHERE course_id=".$_SESSION[course_id].
//        " ORDER BY value LIMIT $link_limit";
$result = mysql_query($sql, $db_mw);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		/****
		* SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY are defined in include/lib/constance.lib.inc
		* SUBLINK_TEXT_LEN determins the maxium length of the string to be displayed on "detail view" box.
		*****/
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('mods/mediawiki/index.php?p='.$row['page_title']).'"'.
		          (strlen($row['page_title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['page_title'].'"' : '') .'>'. 
		          validate_length($row['page_title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>