<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg & Boon-Hau Teh */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
$page	 = 'search_courses';
$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

require(AT_INCLUDE_PATH.'html/feedback.inc.php');


$_SECTION[0][0] = 'Home';
$_SECTION[0][1] = '/index.php';
$_SECTION[1][0] = _AT('course_search');


echo '<h3>'._AT('course_search').'</h3>';


function score_cmp($a, $b) {
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] > $b['score']) ? -1 : 1;
}

if (isset($_GET['search']) && ($_GET['keywords'] != '')) {
	$words = explode(' ',$_GET['keywords']);
	$num_words = count($words);
	$num_courses = count($system_courses);
	$count = 0;
	$list_of_possible_courses;
	for ($j = 0; $count < $num_courses; $j++) {
		/* array index may not necessarily be a smaller value than $num_courses if some courses have been deleted so instead we loop through according to the number of existing courses we've actually found.  */
		if (array_key_exists($j, $system_courses)) {
			$count++;
			$value = $system_courses[$j];
			if ((isset($_GET['public']) && $system_courses[$j]['access']=="public") || (isset($_GET['protected']) && $system_courses[$j]['access']=="protected") || (isset($_GET['private']) && $system_courses[$j]['access']=="private" && $system_courses[$j]['hide']==0)) {
				$row = array();
				$tracker = array();  // use clean array to keep track of which words have been found
				$row['title']  = strip_tags($value['title']);
				$row['description'] = strip_tags($value['description']);
				$lower_title = strtolower($row['title']);
				$lower_description = strtolower($row['description']);
				$row['description']  = substr($row['description'], 0, 250).'...';

				// loop through words to check for 'title' and 'description'
				for ($i = 0; $i < $num_words; $i++) {
					$score = 0;
					if (isset($_GET['title'])) {
						if (($found_words = substr_count($lower_title, strtolower($words[$i]))) > 0) {
							$tracker[$i] = 1;
							$row['score'] += 8*$found_words;
							$row['title'] = highlight($row['title'], $words[$i]);
						}						
					}
					if (isset ($_GET['description'] )) {
						if (($found_words = substr_count($lower_description, strtolower($words[$i]))) > 0) {
							$tracker[$i] = 1;
							$row['score'] += 4*$found_words;
							$row['description'] = highlight($row['description'], $words[$i]);
						}						
					}
				}

				// if looking through 'content' and 'content title', use SQL
				if (isset ($_GET['content'])) {
					$sql = "SELECT title, text FROM ".TABLE_PREFIX."content WHERE course_id=$j";
					$result = mysql_query($sql, $db);
					$match = 0;
					while (($content = mysql_fetch_array($result)) && $match < 101) {
						$lower_content = strtolower(strip_tags($content['text']));
						$lower_content_title = strtolower(strip_tags($content['title']));
						for ($i = 0; $i < $num_words && $match < 101; $i++) {
							if (($found_words = substr_count($lower_content, strtolower($words[$i]))) > 0) {
								$row['score'] += 2*$found_words;
								$match += $found_words;
								$tracker[$i] = 1;
							}
							if (($found_words = substr_count($lower_content_title, strtolower($words[$i]))) > 0) {
								$row['score'] += $found_words;
								$match += $found_words;
								$tracker[$i] = 1;
							}
						}
					}
				}

				// count the words in $tracker to make sure all search terms were found
				if (count ($tracker) >= $num_words) {
					$row['course_id'] = $j;
					$search_results[] = $row;
				}
			}
		}
	}
	$num_results = count($search_results);
} else if (isset($_GET['search'])) {
	$errors[] = AT_ERROR_SEARCH_TERM_REQUIRED;
	print_errors($errors);
}


?>

<br /><br />
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" name="form">
	<input type="hidden" name="search" value="1" />
	<table cellspacing="1" cellpadding="0" border="0" align="center" class="bodyline" summary="">
	<tr>
		<th colspan="2"  class="cyan2"><?php echo _AT('search'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="keywords"><?php echo _AT('search_words'); ?>:</label></td>
		<td class="row1"><input type="text" name="keywords" class="formfield" size="30" id="keywords" value="<?php echo $_GET['keywords']; ?>" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><?php echo _AT('search_by'); ?>:</td>
		<td class="row1">
			<input type="checkbox" class="input" name="content" id="content" value="1" <?php if(isset($_GET['content']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="content"> <?php echo _AT('content'); ?></label><br />
			<input type="checkbox" class="input" name="title" id="title" value="1" <?php if(isset($_GET['title']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="title"> <?php echo _AT('title'); ?></label><br />
			<input type="checkbox" class="input" name="description" id="description" value="1" <?php if(isset($_GET['description']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="description"> <?php echo _AT('description'); ?></label>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><?php echo _AT('access'); ?>:</td>
		<td class="row1">
			<input type="checkbox" class="input" name="public" id="public" value="1" <?php if (isset($_GET['public']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="public"> <?php echo _AT('public'); ?></label><br />
			<input type="checkbox" class="input" name="protected" id="protected" value="1" <?php if(isset($_GET['protected']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="protected"> <?php echo _AT('protected'); ?></label><br />
			<input type="checkbox" class="input" name="private" id="private" value="1" <?php if(isset($_GET['private']) || !isset($_GET['search'])){ echo 'checked="checked"'; } ?>/><label for="private"> <?php echo _AT('private'); ?></label><br />
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="right"><input type="submit" name="search" value=" <?php echo _AT('search'); ?> " class="button" /></td>
	</tr>
	</table>
</form>


<br />
<?php
	if ($num_results != '') {

		usort($search_results, 'score_cmp');

		$results_per_page = 10;
		$max_score = current($search_results);
		$max_score = $max_score['score'];
		$num_pages = ceil($num_results / $results_per_page);

		$page = intval($_GET['p']);
		if (!$page) {
			$page = 1;
		}
		
		$count = (($page-1) * 10) + 1;

		$path = $_SERVER['PHP_SELF'].'?search=1'.SEP.'keywords='.urlencode($_GET['keywords']);
		if (isset($_GET['content'])) {
			$path .= SEP.'content=1';
		}
		if (isset($_GET['title'])) {
			$path .= SEP.'title=1';
		}
		if (isset($_GET['description'])) {
			$path .= SEP.'description=1';
		}
		if (isset($_GET['public'])) {
			$path .= SEP.'public=1';
		}
		if (isset($_GET['protected'])) {
			$path .= SEP.'protected=1';
		}
		if (isset($_GET['private'])) {
			$path .= SEP.'private=1';
		}

		$search_results = array_slice($search_results, ($page-1)*$results_per_page, $results_per_page);
		echo '<a name="search_results"></a>';
		if ($num_results > 1) {
			echo '<h3>'.$num_results.' '._AT('search_results').'</h3>';
		} else {
			echo '<h3>'.$num_results.' '._AT('search_result').'</h3>';
		}
		echo _AT('page').': | ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<strong>'.$i.'</strong>';
			} else {
				echo '<a href="'.$path.SEP.'p='.$i.'">'.$i.'</a>';
			}
			echo ' | ';
		}
		echo '<div class="results">';
		echo '<ol start="'.$count.'">';
		foreach ($search_results as $items) {
			$sql = "SELECT * FROM courses WHERE course_id='$items[course_id]'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			echo '<li><h4><small>[';
			if ($max_score > 0) {
				echo number_format($items['score'] / $max_score * 100, 1);
			} else {
				echo _AT('na');
			}
			echo ' %]</small> <a href="bounce.php?course='.$items['course_id'].'">'.$items['title'].'</a></h4>';
			echo '<small>'.$items['description'].'<br />';
			echo _AT('created').': '.$row['created_date'].'</small><br /><br /></li>';
		}
		echo '</ol>';
		echo '</div>';
		echo _AT('page').': | ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<strong>'.$i.'</strong>';
			} else {
				echo '<a href="'.$path.SEP.'p='.$i.'">'.$i.'</a>';
			}
			echo ' | ';
		}
	} else if (isset($_GET['search']) && ($_GET['keywords'] != '')) {
		$infos[] = AT_INFOS_NO_SEARCH_RESULTS;
		print_infos($infos);
	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>