<?php
/*
* Rename the function to match the name of the module. Names of all news functions must be unique
* across all modules installed on a system. Return a variable called $news
*/
require('wp_connect.php');
		// Number of links to be displayed on "detail view" box
function wordpress_news() {
	global $db_wp, $_config;
if($_GET['p'] != "all"){
	$link_limit = "LIMIT 3";
}

	$news = array();

	$sql = "SELECT ID, post_title, guid, post_modified FROM ".WP_DB_PREFIX."posts ORDER BY post_modified DESC ".$link_limit;
	

$result = mysql_query($sql, $db_wp);

	if($result){
		//$news_count = 0;
		//$page_ids = array();
		$post_title = array();
		while($row = mysql_fetch_assoc($result)){

				if(!in_array($row['post_title'], $post_title)){
				$post_title[] = $row['post_title'];
				$news[] = array('time'=> $row['post_modified'], 
					'alt'=>_AT('wordpress_update'),
					'thumb'=>'mods/wordpress/wordpress_icon_sm.png', 
					'link'=>'<a href="'.AT_BASE_HREF.url_rewrite('mods/wordpress/index_mystart.php?p='.$row['ID']).'"'.
					(strlen($row['post_title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['post_title'].'"' : '') .'>'. 
					validate_length($row['post_title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
				}
		}
	}

 	return $news;

}
?>
