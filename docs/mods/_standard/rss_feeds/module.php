<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_ADMIN_PRIV_RSS', $this->getAdminPrivilege());

//admin pages
if (admin_authenticate(AT_ADMIN_PRIV_RSS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$_module_pages['mods/_standard/rss_feeds/admin/index_admin.php']['title_var'] = 'rss_feeds';
	$_module_pages['mods/_standard/rss_feeds/admin/index_admin.php']['parent'] = AT_NAV_ADMIN;
	$_module_pages['mods/_standard/rss_feeds/admin/index_admin.php']['children'] = array('mods/_standard/rss_feeds/admin/add_feed.php');

	$_module_pages['admin/config_edit.php']['children'] = array('mods/_standard/rss_feeds/admin/index_admin.php');

		$_module_pages['mods/_standard/rss_feeds/admin/add_feed.php']['title_var'] = 'add';
		$_module_pages['mods/_standard/rss_feeds/admin/add_feed.php']['parent'] = 'mods/_standard/rss_feeds/admin/index_admin.php';

		$_module_pages['mods/_standard/rss_feeds/admin/edit_feed.php']['title_var'] = 'edit';
		$_module_pages['mods/_standard/rss_feeds/admin/edit_feed.php']['parent'] = 'mods/_standard/rss_feeds/admin/index_admin.php';

		$_module_pages['mods/_standard/rss_feeds/admin/delete_feed.php']['title_var'] = 'delete';

		$_module_pages['mods/_standard/rss_feeds/admin/preview.php']['title_var'] = 'preview';
}

//make the rss files side menu stacks
$rss_files = array();
$dh  = opendir(AT_CONTENT_DIR.'/feeds');
$count = 0;
while (false !== ($file = readdir($dh))) {
	if (strpos($file, '_rss.inc.php')) {
		$feed_id = intval($file);
		$title = file_get_contents(AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache');
		$_module_stacks[$feed_id.'_rss_title'] = array('title'=>$title, 'file'=>AT_CONTENT_DIR.'feeds/'.$file);
	}
}

//creates or updates the cache file
function make_cache_file($feed_id) {
	global $db;
	static $rss;

	if (!isset($rss)) {  
		require_once(AT_INCLUDE_PATH.'classes/lastRSS.php');
		$rss =& new lastRSS; 
		$rss->cache_dir = AT_CONTENT_DIR.'feeds/'; 
		$rss->num_results = 5;
		$rss->description = FALSE;
	} 

	$sql	= "SELECT * FROM ".TABLE_PREFIX."feeds WHERE feed_id=".intval($feed_id);
	$result = mysql_query($sql, $db);

	$count = 0;
	if ($row = mysql_fetch_assoc($result)) {
		$rss->get($row['url'], $row['feed_id']);
	}
}

?>