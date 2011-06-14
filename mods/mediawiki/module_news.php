<?php
/*
* Rename the function to match the name of the module. Names of all news functions must be unique
* across all modules installed on a system. Return a variable called $news
*/
require('mw_connect.php');
		// Number of links to be displayed on "detail view" box
function mediawiki_news() {
	global $db_mw, $_config;
		$link_limit = "3";
	if($_GET['p'] == "all"){
		$link_limit = "100";
	}
	$news = array();
	$sql = "SELECT  P.page_id, P.page_title, R.rev_timestamp FROM ".MW_DB_PREFIX."page P, ".MW_DB_PREFIX."revision R WHERE R.rev_page = P.page_id ORDER BY R.rev_timestamp DESC";
	$result = mysql_query($sql, $db_mw);
	if($result){
		$news_count = 0;
		$page_ids = array();
		while($row = mysql_fetch_assoc($result)){

			if($news_count < $link_limit && !in_array( $row['page_id'],$page_ids)){
				$this_time = AT_date("%Y-%m-%d %G:%i:%s", $row['rev_timestamp'],AT_DATE_MYSQL_TIMESTAMP_14);
				$page_ids[] = $row['page_id'];
				$news[] = array('time'=> $this_time, 
					'alt'=>_AT('mediawiki_update'),
					'thumb'=>'mods/mediawiki/mw_icon_sm.png', 
					'link'=>'<a href="'.AT_BASE_HREF.url_rewrite('mods/mediawiki/index_mystart.php?p='.$row['page_title']).'"'.
					(strlen($row['page_title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['page_title'].'"' : '') .'>'. 
					validate_length($row['page_title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
				$news_count++;
			}
		}
	}
 	return $news;

}
?>
