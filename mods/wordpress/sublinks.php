<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

/*****
* Free form PHP can appear here to retreive current information
* from the module, or a text description of the module where there is
* not current information
*****/
require('wp_connect.php');

global $db_wp;

$link_limit = 3;		// Number of links to be displayed on "detail view" box
$sql = "SELECT ID, post_title, guid FROM ".WP_DB_PREFIX."posts WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT $link_limit";

$result = mysql_query($sql, $db_wp);
$wp_posts = array();
$i = 1;
while($row = mysql_fetch_assoc($result)){
	$wp_posts[$i] = $row;
	$i++;
}

if($wp_posts != ''){
	foreach($wp_posts as $key=>$value){
	$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('mods/wordpress/index.php?p='. $value['ID']).'"'.
					(strlen($row['value']) > SUBLINK_TEXT_LEN ? ' title="'.$row['value'].'"' : '') .'>'. 
					validate_length($value['post_title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
	}
	return $list;
} else {
 	return 0; 
}

?>