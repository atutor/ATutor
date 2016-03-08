<?php
global $savant, $_config, $stripslashes;
ob_start(); 

$sql = "SELECT feed_id FROM %sfeeds ORDER BY feed_id DESC LIMIT 1";
$row_lastid = queryDB($sql, array(TABLE_PREFIX, $feed_id), TRUE);
$feed_id = $row_lastid['feed_id'];

$sql	= "SELECT url, feed_id FROM %sfeeds WHERE feed_id=%d";
$row_feeds = queryDB($sql, array(TABLE_PREFIX, $feed_id), TRUE);

if (!isset($rss)) {  
    require_once(AT_INCLUDE_PATH.'../mods/_standard/rss_feeds/classes/lastRSS.php');
    $rss = new lastRSS; 
    $rss->cache_dir = AT_CONTENT_DIR.'feeds/'; 
    $rss->num_results = AT_FEED_NUM_RESULTS;
    $rss->description = AT_FEED_SHOW_DESCRIPTION;
} 
$output = $rss->get($row_feeds['url'], 0);

readfile(AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache');
echo $output;
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();
$savant->assign('title', _AT('rss_feeds'));
$savant->display('include/box.tmpl.php');

?>