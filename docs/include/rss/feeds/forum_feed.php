<?php
define('AT_INCLUDE_PATH' , '../../include/');
//require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_INCLUDE_PATH."rss/feedcreator.class.php");

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = "ATutor Community Posts";
$rss->description = "ATutor community discussions";
$rss->link = $_base_href;
$rss->syndicationURL = "http://www.atutor.ca/".$PHP_SELF;
$image = new FeedImage();
$image->title = "ATutor Logo";
$image->url = "http://www.atutor.ca/images/at-logo.v.3.gif";
$image->link = "http://www.atutor.ca";
$image->description = "Feed from the ATutor Community Discussions Forum.";
$rss->image = $image;
;

$sql = "SELECT T.*, F.* from ".TABLE_PREFIX."forums_threads T, ".TABLE_PREFIX."forums_courses F WHERE F.course_id=". $_SESSION[course_id]." AND T.forum_id=F.forum_id ORDER  BY date DESC LIMIT 5";
$res = mysql_query($sql, $db);

while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = $data->subject;
    $item->link = $_base_href."forum/view.php?fid=".$data->forum_id.SEP."pid=".$data->post_id;
    $item->description = $data->body;
    $item->descriptionTruncSize = 50;
    $item->date = strtotime($data->last_comment);
    $item->source = "http://www.atutor.ca/";
    $item->author = $data->login;
    $rss->addItem($item);
}

$rss->saveFeed("RSS2.0", AT_CONTENT_DIR.$_SESSION['course_id']."/feeds/cache/forum_feed.xml", FALSE);
?>