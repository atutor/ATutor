<?php
//define ($_INCLUDE_PATH, "../../../../include/");
//$url= AT_CONTENT_DIR.$_SESSION['course_id']."feeds/cache/forum_feed.xml";
//require_once(AT_INCLUDE_PATH.'rss/rss_fetch.inc');
define('AT_INCLUDE_PATH' , '../../../include/');
if (!file_exists($_base_path.'pub/feeds/browse_courses_feed.xml')) {
		$fp = @fopen($_base_path. 'pub/feeds/browse_courses_feed.xml', 'w+');
		//@fwrite($fp, "test");
	}else{

	//echo "file does not exist";
	//echo AT_CONTENT_DIR.$_SESSION['course_id']. '/feeds/cache/forum_feed.xml';
	//exit;
	}

require_once(AT_INCLUDE_PATH.'rss/rss_parse.inc');
$rss_file = $_base_path."pub/feeds/browse_courses_feed.xml";
$rss_string = read_file($rss_file);
$rss = new MagpieRSS( $rss_string );
//echo $rss_string;

function read_file($rss_file) {
    $fh = fopen($rss_file, 'r') or die($php_errormsg);
    $rss_string = fread($fh, filesize($rss_file) );
    fclose($fh);
    return $rss_string;
}

if ($rss and !$rss->ERROR) {
	$count=0;
	//echo '<table width="100%" cellspacing="0" cellpadding="0">';
	foreach ($rss->items as $item) {
			$href = $item['link'];
			$title = $item['title'];
			$author = $item['author'];
			$description = $item['description'];
			//$date = date("m.d.H:m", strtotime($item['pubDate']));
			$date = $item['pubDate'];

	//echo '<tr><td class="test-box">';
	//echo '';
	//echo '<small><a href="'.$_base_href.'include/rss/forum_feed.php">RSS</a></small><br />';
			echo '<small><span style="align:right;">*</span> <a href="'.$href.'">'.$title.'</a></small><br />';

	}
//echo '</table>';
}else {
    echo "Error: " . $rss->ERROR;
}


?>
