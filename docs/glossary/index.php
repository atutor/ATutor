<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/


define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('glossary');

global $savant;
$msg =& new Message($savant);

require (AT_INCLUDE_PATH.'header.inc.php');
	
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif" class="menuimage" border="0" vspace="2" width="41" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/index.php?g=11">'._AT('tools').'</a>';
	}
	echo '</h2>';

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/glossary-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('glossary');
	}
	echo '</h3>';
	
	$msg->printAll();
	
	echo '<br />';

	/* admin editing options: */
	if (authenticate(AT_PRIV_GLOSSARY, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
		unset($editors);
		$editors[] = array('priv' => AT_PRIV_GLOSSARY, 'title' => _AT('add_glossary'), 'url' => $_base_path.'editor/add_new_glossary.php');
		print_editor($editors , $large = true);
	}

		$sql	= "SELECT word_id, related_word_id FROM ".TABLE_PREFIX."glossary WHERE related_word_id>0 AND course_id=$_SESSION[course_id] ORDER BY related_word_id";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$glossary_related[$row['related_word_id']][] = $row['word_id'];			
		}
		
		$sql	= "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] ORDER BY word";			
		$result= mysql_query($sql, $db);

		if(mysql_num_rows($result) > 0){		

			$gloss_results = array();
			while ($row = mysql_fetch_assoc($result)) {
				$gloss_results[] = $row;
			}
			$num_results = count($gloss_results);
			$results_per_page = 25;
			$num_pages = ceil($num_results / $results_per_page);
			$page = intval($_GET['p']);
			if (!$page) {
				$page = 1;
			}
			
			$count = (($page-1) * $results_per_page) + 1;
			$gloss_results = array_slice($gloss_results, ($page-1)*$results_per_page, $results_per_page);
			
			for ($i=1; $i<=$num_pages; $i++) {
				if ($i == 1) {
					echo _AT('page').': | ';
				}
				if ($i == $page) {
					echo '<strong>'.$i.'</strong>';
				} else {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
				}
				echo ' | ';
			}
			echo '<br /><br /><a name="list"></a>';
			$current_letter = '';
			foreach ($gloss_results as $item) {
				$item['word'] = AT_print($item['word'], 'glossary.word');

				if ($current_letter != strtoupper(substr($item['word'], 0, 1))) {
					$current_letter = strtoupper(substr($item['word'], 0, 1));
					echo '<h3><a name="'.$current_letter.'"></a>- '.$current_letter.' -</h3>';
				}
				echo '<p>';
				echo '<a name="'.urlencode($item['word']).'"></a>';

				echo '<b>'.stripslashes($item['word']);

				if (($item['related_word_id'] != 0) || (is_array($glossary_related[urlencode($item['word_id'])]) )) {

					echo ' ('._AT('see').': ';

					$output = false;

					if ($item['related_word_id'] != 0) {
						echo '<a href="'.$_SERVER['PHP_SELF'].'#'.urlencode($glossary_ids[$item['related_word_id']]).'">'.urldecode($glossary_ids[$item['related_word_id']]).'</a>';
						$output = true;
					}

					if (is_array($glossary_related[urlencode($item['word_id'])]) ) {
						$my_related = $glossary_related[$item['word_id']];

						$num_related = count($my_related);
						for ($i=0; $i<$num_related; $i++) {
							if ($glossary_ids[$my_related[$i]] == $glossary_ids[$item['related_word_id']]) {
								continue;
							}
							if ($output) {
								echo ', ';
							}

							echo '<a href="'.$_SERVER['PHP_SELF'].'#'.urlencode($glossary_ids[$my_related[$i]]).'">'.urldecode($glossary_ids[$my_related[$i]]).'</a>';

							$output = true;
						}
					}
					echo ')';
				}
				echo '</b>';

				/* admin editing options: */
				unset($editors);
				$editors[] = array('priv' => AT_PRIV_GLOSSARY, 'title' => _AT('edit_this_term'), 'url' => $_base_path.'editor/edit_glossary.php?gid='.$item['word_id']);
				$editors[] = array('priv' => AT_PRIV_GLOSSARY, 'title' => _AT('delete_this_term'), 'url' => $_base_path.'editor/delete_glossary.php?gid='.$item['word_id'].SEP.'t='.urlencode($item['word']));
				print_editor($editors , $large = false);

				echo '<br />';
				echo AT_print($item['definition'], 'glossary.definition');
				echo '</p>';
				echo '<br />';
			}
		} else {
			$msg->printInfos('NO_TERMS');
		}
			for ($i=1; $i<=$num_pages; $i++) {
				if ($i == 1) {
					echo _AT('page').': | ';
				}
				if ($i == $page) {
					echo '<strong>'.$i.'</strong>';
				} else {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
				}
				echo ' | ';
			}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>