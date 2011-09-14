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

// NOTE! please see include/lib/search.inc.php NOTE!

//initialize variables
$checked_include_one			= '';
$checked_include_all			= '';
$checked_find_in_course			= '';
$checked_find_in_my_courses		= '';
$checked_find_in_all_courses	= '';
$checked_sw_all					= '';
$checked_sw_content				= '';
$checked_sw_forums				= '';
$checked_display_as_courses		= '';
$checked_display_as_pages		= '';
$checked_display_as_summaries	= '';

/* some error checking can go here: */
if (isset($_GET['search'])) {
	$_GET['words'] = stripslashes($addslashes($_GET['words']));
	$_GET['words'] = str_replace(array('"', '\''), '', $_GET['words']);
	if ($_GET['include'] == 'all') {
		$checked_include_all = ' checked="checked"';
	} else {
		$_GET['include'] = 'one';
		// 'one'
		$checked_include_one = ' checked="checked"';
	}

	if ($_GET['find_in'] == 'this') {
		$checked_find_in_course = ' checked="checked"';
	} else if ($_GET['find_in'] == 'my') {
		$checked_find_in_my_courses = ' checked="checked"';
	} else {
		$_GET['find_in'] = 'all';
		// 'all'
		$checked_find_in_all_courses = ' checked="checked"';
	}

	if ($_GET['display_as'] == 'pages') {
		$checked_display_as_pages = ' checked="checked"';
	} else if ($_GET['display_as'] == 'courses') {
		$checked_display_as_courses = ' checked="checked"';
	} else {
		$_GET['display_as'] = 'summaries';
		// 'summaries'
		$checked_display_as_summaries = ' checked="checked"';
	}

	// search within options
	if ($_GET['search_within']=='content'){
		$checked_sw_content = ' checked="checked"';
	} elseif ($_GET['search_within']=='forums') {
		$checked_sw_forums = ' checked="checked"';
	} else {
		$checked_sw_all = ' checked="checked"';
	}

} else {
	// default values:
	$checked_include_all      = ' checked="checked"';
	$checked_sw_all			  = ' checked="checked"';

	if ($_SESSION['course_id'] > 0) {
		$checked_find_in_course   = ' checked="checked"';
		$checked_display_as_pages = ' checked="checked"';
	} else if ($_SESSION['valid_user']) {
		$checked_find_in_my_courses = ' checked="checked"';
		$checked_display_as_courses = ' checked="checked"';
	} else {
		$checked_find_in_all_courses  = ' checked="checked"';
		$checked_display_as_summaries = ' checked="checked"';
	}
}
if (isset($_GET['search']) && !$_GET['words']) {
	$msg->printErrors('SEARCH_TERM_REQUIRED');
	$_GET = array();
}

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" name="form">
<input type="hidden" name="search" value="1" />
<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="keywords"><?php echo _AT('search_words'); ?></label><br />
		<input type="text" name="words" size="30" id="keywords" value="<?php echo $_GET['words']; ?>" />
	</div>

	<div class="row">
		<?php echo _AT('search_match'); ?><br />
		<input type="radio" name="include" value="all" id="all" <?php echo $checked_include_all; ?> /><label for="all"><?php echo _AT('search_all_words'); ?></label><br />
		<input type="radio" name="include" value="one" id="one" <?php echo $checked_include_one; ?> /><label for="one"><?php echo _AT('search_any_word'); ?></label>
	</div>
	
	<div class="row">
		<?php echo _AT('find_results_in'); ?><br />
				<?php if ($_SESSION['course_id'] > 0) : ?>
					<input type="radio" name="find_in" value="this" id="f1" <?php echo $checked_find_in_course; ?> /><label for="f1"><?php echo _AT('this_course_only'); ?></label><br />
				<?php endif; ?>

				<?php if ($_SESSION['valid_user'] && ($_SESSION['course_id'] > -1)) : ?>
					<input type="radio" name="find_in" value="my" id="f2" <?php echo $checked_find_in_my_courses; ?> /><label for="f2"><?php echo _AT('my_enrolled_courses'); ?></label><br />
				<?php endif; ?>

				<input type="radio" name="find_in" value="all" id="f3" <?php echo $checked_find_in_all_courses; ?> /><label for="f3"><?php echo _AT('all_available_courses'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('search_in'); ?><br />
			<input type="radio" name="search_within" value="all" id="sw_all" <?php echo $checked_sw_all; ?> /><label for="sw_all"><?php echo _AT('all'); ?></label>	
			<input type="radio" name="search_within" value="content" id="sw_content" <?php echo $checked_sw_content; ?> /><label for="sw_content"><?php echo _AT('content'); ?></label>
			<input type="radio" name="search_within" value="forums" id="sw_forums" <?php echo $checked_sw_forums; ?> /><label for="sw_forums"><?php echo _AT('forums'); ?></label>	
	</div>

	<div class="row">
		<?php echo _AT('display'); ?><br />
		<input type="radio" name="display_as" value="pages" id="d1" <?php echo $checked_display_as_pages; ?> /><label for="d1"><?php echo _AT('as_individual_content'); ?></label><br />

		<input type="radio" name="display_as" value="courses" id="d2" <?php echo $checked_display_as_courses; ?> /><label for="d2"><?php echo _AT('grouped_by_course'); ?></label><br />

		<input type="radio" name="display_as" value="summaries" id="d3" <?php echo $checked_display_as_summaries; ?> /><label for="d3"><?php echo _AT('course_summaries'); ?></label><br /><br />
	</div>

	<div class="row buttons">
		<input type="submit" name="search" value="<?php echo _AT('search'); ?>" />
	</div>
</div>
</form>

<?php

/* search results go down here: */
if (isset($_GET['search']) && isset($_GET['words'])) {
	$search_results   = array(); // the content search results
	$search_totals    = array(); // total score per course
	$num_found        = 0;       // total results found
	$total_score      = 0;       // total score (temporary per course)
	$results_per_page = 10;      // number of results per page
	$highlight_system_courses = $system_courses;

	if ($_GET['include'] == 'all') {
		$predicate = 'AND';
	} else {
		$predicate = 'OR';
	}

	if (($_GET['find_in'] == 'this') && ($_SESSION['course_id'] > 0)) {
		if ($_GET['display_as'] == 'pages') {
			$search_results = get_search_result($_GET['words'], $predicate, $_SESSION['course_id'], $num_found, $total_score);
		} else { // 'courses' or 'summaries' :
			$search_results[$_SESSION['course_id']] = get_search_result($_GET['words'], $predicate, $_SESSION['course_id'], $num_found, $total_score);
			$search_totals[$_SESSION['course_id']]  = $total_score;
		}
	} else {
		if ($_GET['find_in'] == 'my') {
			$my_courses = get_my_courses($_SESSION['member_id']);
		} else { // $_GET['find_in'] == 'all' (or other). always safe to perform.
			$my_courses = get_all_courses($_SESSION['member_id']);
		}

		foreach ($my_courses as $tmp_course_id) {
			if ($_GET['display_as'] == 'pages') {
				// merge all the content results together
				$search_results = array_merge($search_results, get_search_result($_GET['words'], $predicate, $tmp_course_id, $num_found, $total_score));
			} else {
				// group by Course
				$total_score = 0;
				$search_results[$tmp_course_id] = get_search_result($_GET['words'], $predicate, $tmp_course_id, $num_found, $total_score);
				if ($total_score) {
					$search_totals[$tmp_course_id]  = $total_score;
				} // else: no content found in this course.
			}
		}
	}

	if ($_GET['display_as'] == 'summaries') {
		$num_found = count($search_totals);
	}

	echo '<a name="search_results"></a><h3>'.$num_found.' '._AT('search_results').'</h3>';

	if (!$num_found) {
		$msg->printInfos('NO_SEARCH_RESULTS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} else if (!$num_found && count($search_totals)) {
		// meaning: no pages were found, just courses:
		$num_found = count($search_totals);
	}

	$num_pages = ceil($num_found / $results_per_page);
			
	$page = isset($_GET['p']) ? intval($_GET['p']) : 0;
	if (!$page) {
		$page = 1;
	}
			
	$count = (($page-1) * $results_per_page) + 1;

	$pages_text = '<div class="paging">';
	$pages_text .= '<ul>';
	for ($i=1; $i<=$num_pages; $i++) {
		$pages_text .= '<li>';
		if ($i == $page) {
			$pages_text .= '<a class="current" href="'.$_SERVER['PHP_SELF'].'?search=1'.SEP.'words='.urlencode($_GET['words']).SEP.'include='.$_GET['include'].SEP.'find_in='.$_GET['find_in'].SEP.'search_within='.$_GET['search_within'].SEP.'display_as='.$_GET['display_as'].SEP.'p='.$i.'#search_results"><strong>'.$i.'</strong></a>';
		} else {
			$pages_text .= '<a href="'.$_SERVER['PHP_SELF'].'?search=1'.SEP.'words='.urlencode($_GET['words']).SEP.'include='.$_GET['include'].SEP.'find_in='.$_GET['find_in'].SEP.'search_within='.$_GET['search_within'].SEP.'display_as='.$_GET['display_as'].SEP.'p='.$i.'#search_results">'.$i.'</a>';
		}
		$pages_text .= '</li>';
	}
	$pages_text .= '</ul>';
	$pages_text .= '</div>';

	echo $pages_text;

	if ($_GET['display_as'] == 'pages') {
		uasort($search_results, 'score_cmp');

		$search_results = array_slice($search_results, ($page-1)*$results_per_page, $results_per_page);

		echo '<div class="results">';
		print_search_pages($search_results);
		echo '</div>'."\n";
	} else {
		arsort($search_totals);
		reset($search_totals);

		$skipped        = 0; // number that have been skipped
		$printed_so_far = 0; // number printed on this page

		foreach ($search_totals as $tmp_course_id => $score) {
			$total_here = 0;
			if ($printed_so_far == $results_per_page) {
				break;
			}
			
			$increment_count = false;
			if (count($search_results[$tmp_course_id]) && ($_GET['display_as'] == 'courses')) {
				uasort($search_results[$tmp_course_id], 'score_cmp');
				reset($search_results[$tmp_course_id]);

				$num_available = count($search_results[$tmp_course_id]); // total number available for this course
		
				if ($printed_so_far == $results_per_page) {
					break;
				}

				if ($skipped < $count) {
					// this course is being truncated
					// implies that it's at the start of the page
					$start = ($page -1) * $results_per_page - $skipped;

					$total_here = count($search_results[$tmp_course_id]);

					$search_results[$tmp_course_id] = array_slice($search_results[$tmp_course_id], $start, $results_per_page - $printed_so_far);

					$num_printing = count($search_results[$tmp_course_id]);

					$printed_so_far += $num_printing;
					$skipped += ($num_available - $num_printing);

					if ($num_printing == 0) {
						continue;
					}
					$increment_count = true;
				}
			} else {
				if ($printed_so_far == $results_per_page) {
					break;
				}

				$total_here = count($search_results[$tmp_course_id]);
				if (($total_here == 0) || ($_GET['display_as'] == 'summaries')) {
					if ($skipped < ($page-1) * $results_per_page) {
						$skipped++;
						continue;
					}
					$printed_so_far ++;
					$increment_count = true;
				} else {
					$printed_so_far += $total_here;
				}
			}
			echo '<h5 class="search-results"> '._AT('results_from', '<a href="bounce.php?course='.$tmp_course_id.'">'.$highlight_system_courses[$tmp_course_id]['title'] .'</a>').' - '._AT('pages_found', $total_here) . '</h5>';


			echo '<p class="search-description">';
			if ($highlight_system_courses[$tmp_course_id]['description']) {
				echo $highlight_system_courses[$tmp_course_id]['description'];
			} else {
				echo '<strong>'._AT('no_description').'</strong>';
			}

			echo '<br /><small class="search-info">[<strong>'._AT('Access').':</strong> ';

			switch ($highlight_system_courses[$tmp_course_id]['access']){
				case 'public':
					echo _AT('public');
					break;
				case 'protected':
					echo _AT('protected');
					break;
				case 'private':
					echo _AT('private');
					break;
			}
			$language =& $languageManager->getLanguage($highlight_system_courses[$tmp_course_id]['primary_language']);

			echo '. <strong>'._AT('primary_language').':</strong> ' . $language->getTranslatedName();
			
			echo ']</small>';

			echo '</p>';

			if ($_GET['display_as'] != 'summaries') {
				echo '<div class="results">';
				print_search_pages($search_results[$tmp_course_id]);
				echo '</div>';
			}			
		
		}
	}

	echo $pages_text;	
}

?>