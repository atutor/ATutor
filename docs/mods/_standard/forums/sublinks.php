<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

global $db;
global $_base_path;

$record_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$cnt = 0;               // count number of returned forums

$all_forums = get_forums($_SESSION['course_id']);

foreach ($all_forums as $shared => $forums) {
	if (is_array($forums)) {
		foreach($forums as $row) {
			if ($cnt >= $record_limit) break 2;
			$cnt++;
			$list[] = array('sub_url' => $_base_path.url_rewrite('forum/index.php?fid='.$row['forum_id']), 'sub_text' => $row['title']);
		}
	}
}

if (count($list) > 0) {
	return $list;
} else {
	return 0;
}
?>