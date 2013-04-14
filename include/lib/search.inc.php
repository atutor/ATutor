<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id$

// NOTE! please see include/html/search.inc.php NOTE!

function score_cmp($a, $b) {
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] > $b['score']) ? -1 : 1;
}


function get_search_result($words, $predicate, $course_id, &$num_found, &$total_score){
	global $_pages, $moduleFactory; 

	$search_results			= array();
	$content_search_results = array();
	$forums_search_results	= array();
	$course_score			= 0;

	if (isset($_GET['search_within'])){
		if ($_GET['search_within'] == 'content'){
			$content_search_results = get_content_search_result($words, $predicate, $course_id, $total_score, $course_score);
			$search_results = $content_search_results;
		} elseif ($_GET['search_within'] == 'forums'){
			$forums_search_results = get_forums_search_result($words, $predicate, $course_id, $total_score, $course_score);
			// get all enabled modules
			$modules = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
			// if forum has been disabled, don't search in it. 
			if (isset($_SESSION['course_id']) && $_SESSION['course_id']==0){
				$search_results = $forums_search_results;
			} elseif (isset($modules['_standard/forums'])){
				$search_results = $forums_search_results;
			}
		} else {
			$content_search_results = get_content_search_result($words, $predicate, $course_id, $total_score, $course_score);
			$forums_search_results = get_forums_search_result($words, $predicate, $course_id, $total_score, $course_score);
			$search_results = array_merge($content_search_results, $forums_search_results);
		}		

		if ((count($search_results) == 0) && $course_score && ($_GET['display_as'] != 'pages')) {
				$num_found++;
		}
		$num_found += count($search_results);
	} 
	return $search_results;
}

function get_content_search_result($words, $predicate, $course_id, &$total_score, &$course_score) {
	global $addslashes, $db, $highlight_system_courses, $strlen, $substr, $strtolower;

	$search_results = array();
	$lower_words    = array();

	$predicate = " $predicate "; // either 'AND' or 'OR'

	$words = trim($words);
	$words = explode(' ',$words);
	$words = array_values(array_diff(array_unique($words), array('')));
	$num_words = count($words);
	$course_score = 0;
	$words_sql = '';

	for ($i=0; $i<$num_words; $i++) {
		$lower_words[$i] = $strtolower($words[$i]);

		if ($words_sql) {
			$words_sql .= $predicate;
		}
		$words[$i] = $addslashes($words[$i]);
		$words_sql .= ' (C.title LIKE "%'.$words[$i].'%" OR C.text LIKE "%'.$words[$i].'%" OR C.keywords LIKE "%'.$words[$i].'%")';

		/* search through the course title and description keeping track of its total */
		$course_score += 15 * substr_count($strtolower($highlight_system_courses[$course_id]['title']),       $lower_words[$i]);
		$course_score += 12 * substr_count($strtolower($highlight_system_courses[$course_id]['description']), $lower_words[$i]);

		$highlight_system_courses[$course_id]['title']       = highlight($highlight_system_courses[$course_id]['title'],       $words[$i]);
		$highlight_system_courses[$course_id]['description'] = highlight($highlight_system_courses[$course_id]['description'], $words[$i]);
	}
	if (!$words_sql) {
		return;
	}

	$sql =  'SELECT C.last_modified, C.course_id, C.content_id, C.title, C.text, C.keywords FROM '.TABLE_PREFIX.'content AS C WHERE C.course_id='.$course_id;
	$sql .= ' AND ('.$words_sql.') LIMIT 200';

	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($result)) {
		$score = 0;

		$row['title'] = strip_tags($row['title']);
		$row['text']  = strip_tags($row['text']);

		$lower_title     = $strtolower($row['title']);
		$lower_text		 = $strtolower($row['text']);
		$lower_keywords  = $strtolower($row['keywords']);

		if ($strlen($row['text']) > 270) {
			$row['text']  = $substr($row['text'], 0, 268).'...';
		}

		for ($i=0; $i<$num_words; $i++) {
			$score += 8 * substr_count($lower_keywords, $lower_words[$i]); /* keywords are weighed more */
			$score += 4 * substr_count($lower_title,    $lower_words[$i]);    /* titles are weighed more */
			$score += 1 * substr_count($lower_text,     $lower_words[$i]);

			$row['title']	  = highlight($row['title'],	$words[$i]);
			$row['text']	  = highlight($row['text'],		$words[$i]);
			$row['keywords']  = highlight($row['keywords'], $words[$i]);

		}
		if ($score != 0) {
			$score += $course_score;
		}
		$row['score'] = $score;
		$search_results[] = $row;

		$total_score += $score;
	}

	if ($total_score == 0) {
		$total_score = $course_score;
	}

	return $search_results;
}

/*
 * Get forum search results
 * @author Harris Wong
 */
function get_forums_search_result($words, $predicate, $course_id, &$total_score, &$course_score) {
	global $addslashes, $db, $highlight_system_courses, $strlen, $substr, $strtolower;

	$search_results = array();
	$lower_words    = array();

	$predicate = " $predicate "; // either 'AND' or 'OR'

	$words = trim($words);
	$words = explode(' ',$words);
	$words = array_values(array_diff(array_unique($words), array('')));
	$num_words = count($words);
	$course_score = 0;
	$words_sql = '';

	for ($i=0; $i<$num_words; $i++) {
		$lower_words[$i] = $strtolower($words[$i]);

		if ($words_sql) {
			$words_sql .= $predicate;
		}
		$words[$i] = $addslashes($words[$i]);
		$words_sql .= ' (course_group_forums.title LIKE "%'.$words[$i].'%" OR T.subject LIKE "%'.$words[$i].'%" OR T.body LIKE "%'.$words[$i].'%")';

		/* search through the course title and description keeping track of its total */
		$course_score += 15 * substr_count($strtolower($highlight_system_courses[$course_id]['title']),       $lower_words[$i]);
		$course_score += 12 * substr_count($strtolower($highlight_system_courses[$course_id]['description']), $lower_words[$i]);
	}

	if (!$words_sql) {
		return;
	}

	//forums sql
	// Wants to get course forums + "my" group forums 
	//if the search is performed outside of a course, do not search in any group forums
	// UNION on course_forums and group_forums
	// TODO: Simplify the query.
	((isset($_SESSION['is_admin']) && $_SESSION['is_admin'] > 0) ? $is_admin_string = '1 OR ' : $is_admin_string = '');
	(isset($_SESSION['member_id']) ? $member_id = $_SESSION['member_id'] : $member_id = 0);
	$sql =	'SELECT course_group_forums.title AS forum_title, course_group_forums.course_id, T.* FROM '.TABLE_PREFIX.'forums_threads T RIGHT JOIN ';
	
	//course forums
	$sql .=	'(	SELECT forum_id, course_id, title, description, num_topics, num_posts, last_post, mins_to_edit FROM '.TABLE_PREFIX.'forums_courses ';
	$sql .= '	NATURAL JOIN '.TABLE_PREFIX.'forums WHERE course_id='.$course_id;
	
	$sql .= '	UNION ';

	//group forums 
	$sql .= '	SELECT forum_id, course_id, title, description, num_topics, num_posts, last_post, mins_to_edit FROM '.TABLE_PREFIX.'forums_groups NATURAL JOIN ';
	$sql .= '	(SELECT forum_id, num_topics, num_posts, last_post, mins_to_edit FROM '.TABLE_PREFIX.'forums) AS f NATURAL JOIN ';
	$sql .= '	'.TABLE_PREFIX.'groups_members NATURAL JOIN ';
	$sql .= '	(SELECT g.*, gt.course_id FROM '.TABLE_PREFIX.'groups g INNER JOIN '.TABLE_PREFIX.'groups_types gt USING (type_id) WHERE ';
	$sql .=	'	course_id='.$course_id.') AS group_course WHERE	'.$is_admin_string .'member_id='.$member_id;
	$sql .=	') AS course_group_forums ';
	
	$sql .=	'USING (forum_id) ';
	$sql .= 'WHERE ' . $words_sql;

	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($result)) {
		$score = 0;

		$row['forum_title'] = strip_tags($row['forum_title']);
		$row['subject']  = strip_tags($row['subject']);
		$row['body']  = strip_tags($row['body']);
		
		$lower_forum_title	= $strtolower($row['forum_title']);
		$lower_subject		= $strtolower($row['subject']);
		$lower_body			= $strtolower($row['body']);
		$num_posts			= intval($row['num_comments']);

		if ($strlen($row['body']) > 270) {
			$row['body']  = $substr($row['body'], 0, 268).'...';
		}

		for ($i=0; $i<$num_words; $i++) {
			$score += 8 * substr_count($lower_forum_title,	$lower_words[$i]);		/* forum's title are weighed more */
			$score += 4 * substr_count($lower_subject,		$lower_words[$i]);		/* thread subject are weighed more */
			$score += 2 * substr_count($lower_body,			$lower_words[$i]);
			$score += 1 * $num_posts;

			$row['forum_title']	= highlight($row['forum_title'], $words[$i]);
			$row['subject']		= highlight($row['subject'], $words[$i]);
			$row['body']		= highlight($row['body'], $words[$i]);

		}
		if ($score != 0) {
			$score += $course_score;
		}
		$row['score'] = $score;
		$search_results[] = $row;

		$total_score += $score;
	}

	return $search_results;

}


// My Courses - All courses you're enrolled in (including hidden)
function get_my_courses($member_id) {
	global $db;

	$list = array();

	$sql = "SELECT course_id FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$member_id AND (approved='y' OR approved='a')";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = $row['course_id']; // list contains all the Course IDs
	}

	return $list;
}


// All courses (display hidden too if you're enrolled in it)
function get_all_courses($member_id) {
	global $system_courses, $db;

	$list = array();

	$num_courses = count($system_courses);

	// add all the courses that are not hidden,then find the hidden courses that you're enrolled in and then add that to array
	foreach ($system_courses as $course_id => $course_info) {
		if (!$course_info['hide']) {
			$list[] = $course_id;
		}
	}

	// if there aren't any hidden courses:
	if (count($system_courses) == count($list)) {
		return $list;
	}

	if ($_SESSION['valid_user']) {
		$my_courses = implode(',', get_my_courses($member_id));
		$sql = "SELECT course_id FROM ".TABLE_PREFIX."courses WHERE hide=1 AND course_id IN (0, $my_courses)";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$list[] = $row['course_id'];
		}
	}
	return $list;
}

function print_search_pages($result) {
	global $count;

	foreach ($result as $items) {
		uasort($result, 'score_cmp');

		echo '<h5>' . $count . '. ';
		
		if(isset($items['forum_title'])){
			//Forum
			if ($_SESSION['course_id'] != $items['course_id']) {
				echo '<a href="bounce.php?course='.$items['course_id'].SEP.'p='.urlencode('forum/view.php?fid='.$items['forum_id'].SEP.'pid='.$items['post_id'].SEP.'words='.$_GET['words']).'">'.$items['forum_title'].' - '.$items['subject'].'</a> ';
			} else {
				echo '<a href="'.url_rewrite('mods/_standard/forums/forum/view.php?fid='.$items['forum_id'].SEP.'pid='.$items['post_id'].SEP.'words='.$_GET['words']).'">'.$items['forum_title'].' - '.$items['subject'].'</a> ';
			}
			echo '</h5>'."\n";

			echo '<p><small>'.$items['body'];
		} else {
			//Content
			if ($_SESSION['course_id'] != $items['course_id']) {
				echo '<a href="bounce.php?course='.$items['course_id'].SEP.'p='.urlencode('content.php?cid='.$items['content_id'].SEP.'words='.$_GET['words']).'">'.$items['title'].'</a> ';
			} else {
				echo '<a href="'.url_rewrite('content.php?cid='.$items['content_id'].SEP.'words='.$_GET['words']).'">'.$items['title'].'</a> ';
			}
			echo '</h5>'."\n";

			echo '<p><small>'.$items['text'];
		}

		echo '<br /><small class="search-info">[<strong>'._AT('keywords').':</strong> ';
		if (isset($items['keywords'])) {
			echo $items['keywords'];
		} else {
			echo '<strong>'._AT('none').'</strong>';
		}
		echo '. <strong>'._AT('author').':</strong> ';
		if (isset($items['member_id'])) {
			echo AT_print(get_display_name($items['member_id']), 'members.login');
		} else {
			echo '<strong>'._AT('none').'</strong>';
		}
		echo '. <strong>'._AT('updated').':</strong> ';
		echo AT_date(_AT('inbox_date_format'), (isset($items['last_modified']) && $items['last_modified']!='')?$items['last_modified']:$items['last_comment'], AT_DATE_MYSQL_DATETIME);

		echo ']</small>';

		echo '</small></p>'."\n";
		$count++;
	}
}

?>