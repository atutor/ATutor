<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_RSS', $this->getAdminPrivilege());

define('AT_FEED_TIMEOUT', 21600);  //feed is cached for this long.  21600 = 6 hours
define('AT_FEED_NUM_RESULTS', 5);
define('AT_FEED_SHOW_DESCRIPTION', FALSE);

//admin pages
if (admin_authenticate(AT_ADMIN_PRIV_RSS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/config_edit.php']['children']      = array('mods/_standard/rss_feeds/index.php');
		$this->_pages['mods/_standard/rss_feeds/index.php']['parent'] = 'admin/config_edit.php';
	} else {
		$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/rss_feeds/index.php');
		$this->_pages['mods/_standard/rss_feeds/index.php']['parent'] = AT_NAV_ADMIN;
	}

	$this->_pages['mods/_standard/rss_feeds/index.php']['title_var'] = 'rss_feeds';
	$this->_pages['mods/_standard/rss_feeds/index.php']['children']  = array('mods/_standard/rss_feeds/add_feed.php');
	$this->_pages['mods/_standard/rss_feeds/index.php']['guide']     = 'admin/?p=feeds.php';

		$this->_pages['mods/_standard/rss_feeds/add_feed.php']['title_var'] = 'add';
		$this->_pages['mods/_standard/rss_feeds/add_feed.php']['parent'] = 'mods/_standard/rss_feeds/index.php';

		$this->_pages['mods/_standard/rss_feeds/edit_feed.php']['title_var'] = 'edit';
		$this->_pages['mods/_standard/rss_feeds/edit_feed.php']['parent'] = 'mods/_standard/rss_feeds/index.php';

		$this->_pages['mods/_standard/rss_feeds/delete_feed.php']['title_var'] = 'delete';
		$this->_pages['mods/_standard/rss_feeds/delete_feed.php']['parent'] = 'mods/_standard/rss_feeds/index.php';

		$this->_pages['mods/_standard/rss_feeds/preview.php']['title_var'] = 'preview';
		$this->_pages['mods/_standard/rss_feeds/preview.php']['parent'] = 'mods/_standard/rss_feeds/index.php';
}

//make the rss files side menu stacks
$rss_files = array();
$dh  = opendir(AT_CONTENT_DIR.'/feeds');

$count = 0;
while (false !== ($file = @readdir($dh))) {
	if (strpos($file, '_rss.inc.php')) {
		$feed_id = intval($file);
		if (file_exists(AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache')) {
			$title = @file_get_contents(AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache');
			$this->_stacks[$feed_id.'_rss_title'] = array('title'=>$title, 'file'=>AT_CONTENT_DIR.'feeds/'.$file);
		}
	}
}

//creates or updates the cache file
function make_cache_file($feed_id) {
	global $db;
	static $rss;

	if (!isset($rss)) {  
		require_once(AT_INCLUDE_PATH.'../mods/_standard/rss_feeds/classes/lastRSS.php');
		$rss = new lastRSS; 
		$rss->cache_dir = AT_CONTENT_DIR.'feeds/'; 
		$rss->num_results = AT_FEED_NUM_RESULTS;
		$rss->description = AT_FEED_SHOW_DESCRIPTION;
	} 

	$sql	= "SELECT url, feed_id FROM ".TABLE_PREFIX."feeds WHERE feed_id=".intval($feed_id);
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$output = $rss->get($row['url'], $row['feed_id']);

		$cache_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
		if ($f = @fopen($cache_file, 'w')) {
			fwrite ($f, $output, strlen($output));
			fclose($f);
		}
		return 0;
	} else {
		$output = $rss->get($_POST['url'], 0);
		return $output;
	}
}

function print_rss_feed($file) {
	global $savant;

	$feed_id = intval(basename($file));
	$cache_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
	$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

	ob_start(); 

	//if file doesn't exist or is more than AT_FEED_TIMEOUT old
	if (!file_exists($cache_file) || ((time() - filemtime($cache_file)) > AT_FEED_TIMEOUT) ) {
		make_cache_file($feed_id);
	}
	if (file_exists($cache_file)) {
		readfile($cache_file);
		echo '<br /><small>'._AT('new_window').'</small>';
	} else {
		echo _AT('no_content_avail');
	}

	$savant->assign('dropdown_contents', ob_get_contents());
	ob_end_clean();

	$savant->assign('title', @file_get_contents($title_file));
	$savant->display('include/box.tmpl.php');
}

?>