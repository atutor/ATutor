<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('search');

	$include_all = '';
	$include_one = '';
	if (!isset($_GET['include'])) {
		$include_all = ' checked="checked"';
	} else if ($_GET['include'] == 'all') {
		$include_all = ' checked="checked"';
	} else {
		$include_one = ' checked="checked"';
	}

	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'lib/format_content.inc.php');
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/?g=11"><img src="images/icons/default/square-large-tools.gif" vspace="2"  class="menuimageh2" width="41" height="40" border="0" alt="*" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/?g=11">'._AT('tools').'</a>';
	}
	echo '</h2>';

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/search-large.gif" class="menuimageh3" width="42" height="38" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('search');
	}
	echo '</h3>';

function score_cmp($a, $b) {
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] > $b['score']) ? -1 : 1;
}


if (isset($_GET['search']) && ($_GET['words'] != '')) {
	
	if ($_GET['include'] == 'all') {
		$predicate = ' AND ';
	} else {
		$predicate = ' OR ';
	}

	$words = explode(' ',$_GET['words']);
	$num_words = count($words);
	$where = '0';
	for ($i=0; $i<$num_words; $i++) {
		if ($words_sql) {
			$words_sql .= $predicate;
		}
		$words_sql .= ' (C.title LIKE "%'.$words[$i].'%" OR C.text LIKE "%'.$words[$i].'%" OR C.keywords LIKE "%'.$words[$i].'%")';
	}
	$sql = 'SELECT C.content_id, C.title, C.text, LENGTH(C.text) AS length, C.keywords FROM '.TABLE_PREFIX.'content AS C WHERE C.course_id='.$_SESSION['course_id'];
	$sql = $sql.' AND ('.$words_sql.')';

	$result = mysql_query($sql, $db);
	$search_results = array();
	while($row = mysql_fetch_assoc($result)) {
		$score = 0;
		$row['title'] = strip_tags($row['title']);
		$row['text']  = strip_tags($row['text']);

		$lower_title     = strtolower($row['title']);
		$lower_text		 = strtolower($row['text']);
		$lower_keywords  = strtolower($row['keywords']);

		$row['text']  = substr($row['text'], 0, 250).'...';
	
		for ($i=0; $i<$num_words; $i++) {
			$score += 8*substr_count($lower_keywords, strtolower($words[$i])); /* keywords are weighed more */
			$score += 4*substr_count($lower_title, strtolower($words[$i])); /* titles are weighed more */
			$score += substr_count($lower_text, strtolower($words[$i]));
			
			$row['title']	  = highlight($row['title'],	$words[$i]);
			$row['text']	  = highlight($row['text'],		$words[$i]);
			$row['keywords']  = highlight($row['keywords'], $words[$i]);
		}
		$row['score'] = $score;
		$search_results[] = $row;
	}

	$num_results = count($search_results);
} else if (isset($_GET['search'])) {
	$errors[] = AT_ERROR_SEARCH_TERM_REQUIRED;
	print_errors($errors);
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="get" name="form">
<input type="hidden" name="search" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2"><?php print_popup_help(AT_HELP_SEARCH); ?><?php echo _AT('search'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="words2"><?php echo _AT('search_words'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="words" class="formfield" size="40" id="words2" value="<?php echo stripslashes(htmlspecialchars($_GET['words'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('search_match'); ?>:</b></td>
	<td class="row1"><input type="radio" name="include" value="all" id="all"<?php echo $include_all; ?> /><label for="all"><?php echo _AT('search_all_words'); ?></label><br />
	<input type="radio" name="include" value="one" id="one"<?php echo $include_one; ?> /><label for="one"><?php echo _AT('search_any_word'); ?></label></td>
</tr>
<?php
/***
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('search_in'); ?>:</b></td>
	<td class="row1"><input type="checkbox" name="in[]" value="content" id="content" checked="checked" /><label for="content"><?php echo _AT('search_in_content'); ?></label><br />
	<input type="checkbox" name="in[]" value="forums" id="forums" /><label for="forums"><?php echo _AT('search_in_forums'); ?></label><br />
	<input type="checkbox" name="in[]" value="links" id="links" /><label for="links"><?php echo _AT('search_in_links'); ?></label><br />
	<input type="checkbox" name="in[]" value="news" id="news" /><label for="news"><?php echo _AT('search_in_news'); ?></label><br />
	</td>
</tr>
***/
?>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="  <?php echo _AT('search'); ?>  " class="button" /></td>
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

		$search_results = array_slice($search_results, ($page-1)*$results_per_page, $results_per_page);
		echo '<a name="search_results"></a>';
		echo '<h3>'._AT('search_results').'</h3>';
		echo _AT('page').': | ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<strong>'.$i.'</strong>';
			} else {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?search=1'.SEP.'words='.urlencode($_GET['words']).SEP.'include='.$_GET['include'].SEP.'p='.$i.'#search_results">'.$i.'</a>';
			}
			echo ' | ';
		}
		echo '<div class="results">';
		foreach ($search_results as $items) {
			echo '<h4>'.$count.'. <small>[';
			if ($max_score > 0) {
				echo number_format($items['score'] / $max_score * 100, 1);
			} else {
				echo _AT('na');
			}
			echo ' % | '.number_format($items['length']/AT_KBYTE_SIZE, 1).' KB]</small> <a href="?cid='.$items['content_id'].'">'.$items['title'].'</a></h4><p>';
			echo $items['text'];
			if ($items['keywords'] != '') {
				echo ' <small>['._AT('keywords').': '.$items['keywords'].']</small>';
			}
			echo '</p>';

			$count++;
		}
		echo '</div>';
		echo _AT('page').': | ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<strong>'.$i.'</strong>';
			} else {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?search=1'.SEP.'words='.urlencode($_GET['words']).SEP.'include='.$_GET['include'].SEP.'p='.$i.'#search_results">'.$i.'</a>';
			}
			echo ' | ';
		}
	} else if($_GET['submit']) {
		$infos[] = AT_INFOS_NO_SEARCH_RESULTS;
		print_infos($infos);
	}


	require(AT_INCLUDE_PATH.'footer.inc.php');
?>