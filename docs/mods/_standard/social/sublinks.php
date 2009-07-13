<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
include(AT_INCLUDE_PATH.'../mods/_standard/social/lib/friends.inc.php');

global $db;

$link_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$cnt = 0;

$actvity_obj = new Activity();
$activities = $actvity_obj->getFriendsActivities($_SESSION['member_id']);

if (is_array($activities)) {
	foreach ($activities as $i => $activity) {
		if ($cnt >= $link_limit) break;
		$cnt++;
		
		$link_title = strip_tags(printSocialName($activity['member_id']).' '. $activity['title']);

		$list[] = '<span title="'.$link_title.'">'.validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY)."</span>";
	}
	return $list;	
} else {
	return 0;
}

?>