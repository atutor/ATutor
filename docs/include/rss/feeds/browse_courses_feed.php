<?php
define('AT_INCLUDE_PATH' , '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_INCLUDE_PATH."rss/feedcreator.class.php");

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = SITE_NAME;
$rss->description = _AT('available_courses');
$rss->link = $_base_href;
$rss->syndicationURL = $_base_href;
$image = new FeedImage();
$image->title = SITE_NAME;
$image->url = $_base_href.HEADER_LOGO;
$image->link = $_base_href;
$image->description = _AT('available_courses_on', SITE_NAME);
$rss->image = $image;

$sql= "SELECT C.*, M.member_id, M.first_name, M.last_name from ".TABLE_PREFIX."courses C, ".TABLE_PREFIX."members M WHERE C.hide<>1";

//echo $sql;
//exit;
//$sql = "SELECT T.*, F.* from ".TABLE_PREFIX."forums_threads T, ".TABLE_PREFIX."forums_courses F WHERE F.course_id=". //$_SESSION[course_id]." AND T.forum_id=F.forum_id ORDER  BY date DESC LIMIT 5";
$res = mysql_query($sql, $db);

while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = $data->title;
    $item->link = $_base_href."index.php?course=".$data->course_id;
    $item->description = $data->description;
    $item->descriptionTruncSize = 50;
    $item->date = strtotime($data->created_date);
    $item->source = $_base_href;
    $item->author = $data->first_name;
    $item->author .= ' '.$data->last_name;
    $rss->addItem($item);
}

$rss->saveFeed("RSS2.0", "../../../pub/feeds/browse_courses.xml", TRUE);
?>
