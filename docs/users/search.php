<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
$page	 = 'search';
$_user_location = 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'html/feedback.inc.php');


$_section[0][0] = _AT('search_courses');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>'._AT('search_courses').'</h3>';

/* some error checking can go here: */
if (isset($_GET['search']) && !$_GET['words']) {
	$errors[] = AT_ERROR_SEARCH_TERM_REQUIRED;
	print_errors($errors);

} else if (isset($_GET['search'])) {
	if ($_GET['include'] == 'all') {
		$checked_include_all = ' checked="checked"';
	} else {
		// 'one'
		$checked_include_one = ' checked="checked"';
	}

	if ($_GET['find_in'] == 'this') {
		$checked_find_in_course = ' checked="checked"';
	} else if ($_GET['find_in'] == 'my') {
		$checked_find_in_my_courses = ' checked="checked"';
	} else {
		// 'all'
		$checked_find_in_all_courses = ' checked="checked"';
	}

	if ($_GET['display_as'] == 'pages') {
		$checked_display_as_pages = ' checked="checked"';
	} else {
		// 'courses'
		$checked_display_as_courses = ' checked="checked"';
	}

} else {
	// default values:
	$checked_include_all      = ' checked="checked"';

	if ($_SESSION['course_id']) {
		$checked_find_in_course   = ' checked="checked"';
	} else if ($_SESSION['valid_user']) {
		$checked_find_in_my_courses   = ' checked="checked"';
	} else {
		$checked_find_in_all_courses   = ' checked="checked"';
	}
	$checked_display_as_pages = ' checked="checked"';
}

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" name="form">
	<input type="hidden" name="search" value="1" />
	<table cellspacing="1" cellpadding="0" align="center" class="bodyline" summary="">
	<tr>
		<th colspan="2"  class="cyan"><?php echo _AT('search'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="keywords"><?php echo _AT('search_words'); ?>:</label></td>
		<td class="row1"><input type="text" name="words" class="formfield" size="30" id="keywords" value="<?php echo $_GET['words']; ?>" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><?php echo _AT('search_match'); ?>:</td>
		<td class="row1"><input type="radio" name="include" value="all" id="all" <?php echo $checked_include_all; ?> /><label for="all"><?php echo _AT('search_all_words'); ?></label><br />
	<input type="radio" name="include" value="one" id="one" <?php echo $checked_include_one; ?> /><label for="one"><?php echo _AT('search_any_word'); ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right">Find results in:</td>
		<td class="row1">
				<?php if ($_SESSION['course_id']) : ?>
					<input type="radio" name="find_in" value="this" id="f1" <?php echo $checked_find_in_course; ?> /><label for="f1">This course only</label><br />
				<?php endif; ?>

				<?php if ($_SESSION['valid_user']) : ?>
					<input type="radio" name="find_in" value="my" id="f2" <?php echo $checked_find_in_my_courses; ?> /><label for="f2">My enrolled courses</label><br />
				<?php endif; ?>

				<input type="radio" name="find_in" value="all" id="f3" <?php echo $checked_find_in_all_courses; ?> /><label for="f3">All available courses</label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right">Display:</td>
		<td class="row1"><input type="radio" name="display_as" value="pages" id="d1" <?php echo $checked_display_as_pages; ?> /><label for="d1">As individual content pages</label><br />
						<input type="radio" name="display_as" value="courses" id="d2" <?php echo $checked_display_as_courses; ?> /><label for="d2">Grouped by course</label><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="search" value=" <?php echo _AT('search'); ?> " class="button" /></td>
	</tr>
	</table>
</form>

<?php

function score_cmp($a, $b) {
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] > $b['score']) ? -1 : 1;
}

function get_search_result($words, $predicate, $course_id, &$num_found=0, &$total_score=0) {
	global $addslashes, $db;

	$search_results = array();
	$lower_words    = array();

	$predicate = " $predicate "; // either 'AND' or 'OR'

	$words = explode(' ',$words);
	$num_words = count($words);
	$where = '0';
	for ($i=0; $i<$num_words; $i++) {
		$lower_words[$i] = strtolower($words[$i]);

		if ($words_sql) {
			$words_sql .= $predicate;
		}
		$words[$i] = $addslashes($words[$i]);
		$words_sql .= ' (C.title LIKE "%'.$words[$i].'%" OR C.text LIKE "%'.$words[$i].'%" OR C.keywords LIKE "%'.$words[$i].'%")';
	}

	$sql = 'SELECT C.last_modified, C.course_id, C.content_id, C.title, C.text, LENGTH(C.text) AS length, C.keywords FROM '.TABLE_PREFIX.'content AS C WHERE C.course_id='.$course_id;
	$sql = $sql.' AND ('.$words_sql.')';
	
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($result)) {
		$score = 0;

		$row['title'] = strip_tags($row['title']);
		$row['text']  = strip_tags($row['text']);

		$lower_title     = strtolower($row['title']);
		$lower_text		 = strtolower($row['text']);
		$lower_keywords  = strtolower($row['keywords']);

		if (strlen($row['text']) > 270) {
			$row['text']  = substr($row['text'], 0, 268).'...';
		}

		for ($i=0; $i<$num_words; $i++) {
			$score += 8 * substr_count($lower_keywords, $lower_words[$i]); /* keywords are weighed more */
			$score += 4 * substr_count($lower_title,    $lower_words[$i]);    /* titles are weighed more */
			$score += 1 * substr_count($lower_text,     $lower_words[$i]);

			$row['title']	  = highlight($row['title'],	$words[$i]);
			$row['text']	  = highlight($row['text'],		$words[$i]);
			$row['keywords']  = highlight($row['keywords'], $words[$i]);

		}
		$row['score'] = $score;
		$search_results[] = $row;

		$total_score += $score;
	}

	$num_found += count($search_results);

	return $search_results;
}


// My Courses - All courses you're enrolled in (including hidden)
function get_my_courses($member_id) {
	global $db;

	$list = array();

	$sql = "SELECT course_id FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$member_id AND approved='y'";
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

	
	$my_courses = implode(',', my_courses($member_id));
	$sql = "SELECT course_id FROM ".TABLE_PREFIX."courses WHERE hide=1 AND course_id IN (0, $my_courses)";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = $row['course_id'];
	}
	return $list;
}

function print_search_pages($result) {
	static $count;
	static $max_score;

	if (!isset($count)) {
		$count = 1;
	}
	if (!isset($max_score)) {
		$max_score = current($result);
		$max_score = $max_score['score'];
	}

	foreach ($result as $items) {
		uasort($result, 'score_cmp');

		echo '<h5>' . $count . '. ';
		
		echo '<a href="?cid='.$items['content_id'].'">'.$items['title'].'</a> ';
		echo '<small>[';
		if ($max_score > 0) {
			echo number_format($items['score'] / $max_score * 100, 1);
		} else {
			echo _AT('na');
		}
		echo ' % ]</small> ';

		//echo '[ score: '.$items['score'].' | course_id: '.$items['course_id'].' ]</h5>';
		echo '</h5>';

		echo '<p><small>'.$items['text'];

		echo '<br /><small class="search-info">[<strong>'._AT('keywords').':</strong> ';
		if ($items['keywords']) {
			echo $items['keywords'];
		} else {
			echo '<em>'._AT('none').'</em>';
		}


		echo '. <strong>'._AT('updated').':</strong> ';
		echo AT_date(_AT('inbox_date_format'), $items['last_modified'], AT_DATE_MYSQL_DATETIME);


		echo ']</small>';

		echo '</small></p>';
		$count++;
	}
}

/* search results go down here: */
if (isset($_GET['search']) && $_GET['words']) {
	$search_results   = array(); // the content search results
	$search_totals    = array(); // total score per course
	$num_found        = 0;       // total results found
	$total_score      = 0;       // total score (temporary per course)
	$results_per_page = 10;      // number of results per page

	if ($_GET['include'] == 'all') {
		$predicate = 'AND';
	} else {
		$predicate = 'OR';
	}

	if ($_GET['find_in'] == 'this') {
		if ($_GET['display_as'] == 'pages') {
			$search_results = get_search_result($_GET['words'], $predicate, $_SESSION['course_id'], $num_found, $total_score);
		} else {
			$search_results[$_SESSION['course_id']] = get_search_result($_GET['words'], $predicate, $_SESSION['course_id'], $num_found, $total_score);
			$search_totals[$_SESSION['course_id']]  = $total_score;
		}
	} else {
		if ($_GET['find_in'] == 'my') {
			$my_courses = get_my_courses($_SESSION['member_id']);
		} else {
			// $_GET['find_in'] == 'all' (or other). always safe to perform.
			$my_courses = get_all_courses($_SESSION['member_id']);
		}

		foreach ($my_courses as $course_id) {
			if ($_GET['display_as'] == 'pages') {
				$search_results = array_merge($search_results, get_search_result($_GET['words'], $predicate, $course_id, $num_found));
			} else {
				$total_score = 0;
				$search_results[$course_id] = get_search_result($_GET['words'], $predicate, $course_id, $num_found, $total_score);
				// course_search_results = get_search_course_result, $total);
				if ($total_score) {
					$search_totals[$course_id]  = $total_score;
				}
			}
		}
	}
	
	echo '<a name="search_results"></a><h3>'.$num_found.' '._AT('search_results').'</h3>';

	$num_pages = ceil($num_found / $results_per_page);
			
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}
			
	$count = (($page-1) * $results_per_page) + 1;

	$pages_text = _AT('page').': | ';
	for ($i=1; $i<= $num_pages; $i++) {
		if ($i == $page) {
			$pages_text .= '<strong>'.$i.'</strong>';
		} else {
			$pages_text .= '<a href="'.$_SERVER['PHP_SELF'].'?search=1'.SEP.'words='.urlencode($_GET['words']).SEP.'include='.$_GET['include'].SEP.'find_in='.$_GET['find_in'].SEP.'display_as='.$_GET['display_as'].SEP.'p='.$i.'#search_results">'.$i.'</a>';
		}
		$pages_text .= ' | ';
	}
	echo $pages_text;

	if ($_GET['display_as'] == 'pages') {
		uasort($search_results, 'score_cmp');

		$search_results = array_slice($search_results, ($page-1)*$results_per_page, $results_per_page);

		echo '<div class="results">';
		print_search_pages($search_results);
		echo '</div>';
	} else {
		arsort($search_totals);
		reset($search_totals);

		foreach ($search_totals as $course_id => $score) {
			uasort($search_results[$course_id], 'score_cmp');
			reset($search_results[$course_id]);
			//$search_results = array_slice($search_results, ($page-1)*$results_per_page, $results_per_page);

			echo '<h5 class="search-results">Results from <a href="">'. $system_courses[$course_id]['title'] .'</a></h5><div class="results">';
			print_search_pages($search_results[$course_id]);
			echo '</div>';
		}
	}

	echo $pages_text;	
}


require(AT_INCLUDE_PATH.'footer.inc.php');
?>