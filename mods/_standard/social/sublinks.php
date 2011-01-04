<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
include_once(AT_INCLUDE_PATH.'../mods/_standard/social/lib/friends.inc.php');

function substring($str, $length)
{
	preg_match_all("|(.*)((<[^>]+>)(.*)(</[^>]+>))|U", $str, $matches, PREG_SET_ORDER);
//	debug($matches);exit;
	
	$rtn = '';
	if (is_array($matches))
	{
		$curr_len = 0;
		foreach ($matches as $i => $tag)
		{
			if (($curr_len + strlen($tag[1])) > $length)
			{
				$rtn .= substr($tag[1], 0, ($length - $curr_len)) . ' ...';
				return $rtn;
			}
			else
			{
				$rtn .= $tag[1];
				$curr_len += strlen($tag[1]);
			}
			
			if (($curr_len + strlen($tag[4])) > $length)
			{
				$rtn .= $tag[3].substr($tag[4], 0, ($length - $curr_len)).'...'.$tag[5];
				return $rtn;
			}
			else
			{
				$rtn .= $tag[2];
				$curr_len += strlen($tag[4]);
			}
		}
		
		$pos_after_last_match = strpos($str, $tag[0]) + strlen($tag[0]);
		$str_after_last_match = substr($str, $pos_after_last_match);

		if (($curr_len + strlen($str_after_last_match)) > $length)
			$rtn .= substr($str_after_last_match, 0, ($length - $curr_len)).' ...';
		else
			$rtn = $str;
	}
	else
		$rtn = substr($str, 0, $length);
	
	return $rtn;
}

global $db;

$link_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$cnt = 0;

$actvity_obj = new Activity();
$activities = $actvity_obj->getFriendsActivities($_SESSION['member_id']);

if (is_array($activities)) {
	foreach ($activities as $i => $activity) {
		if ($cnt >= $link_limit) break;
		$cnt++;
		
		$link_title = printSocialName($activity['member_id']).' '. $activity['title'];

		$list[] = '<span title="'.strip_tags($link_title).'">'.substring($link_title, SUBLINK_TEXT_LEN)."</span>";
	}
	return $list;	
} else {
	return 0;
}

?>