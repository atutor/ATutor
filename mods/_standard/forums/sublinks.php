<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

global $db;

$record_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$cnt = 0;               // count number of returned forums



$all_forums = get_forums($_SESSION['course_id']);

foreach ($all_forums as $shared => $forums) {
    if (is_array($forums)) {

        foreach($forums as $row) {
            if ($cnt >= $record_limit) break 2;
            $cnt++;

            $link_title = AT_print($row['title'], 'forums.title').' ('.AT_DATE('%F %j, %g:%i',$row['last_post'],AT_DATE_MYSQL_DATETIME).')';
            $list[] = '<a href="'.url_rewrite('mods/_standard/forums/forum/index.php?fid='.$row['forum_id'], AT_PRETTY_URL_IS_HEADER).'"'.
                      (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.$link_title.'"' : '') .'>'. 
                      validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
        }
    }
}

if (count($list) > 0) {
    return $list;
} else {
    return 0;
}
?>