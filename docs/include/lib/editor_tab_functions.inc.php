<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }


function get_tabs() {
	//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name,                accesskey) */
	$tabs[0] = array('content',       'edit.inc.php',       'n');
	$tabs[1] = array('properties',    'properties.inc.php', 'p');
	//$tabs[2] = array('keywords',      'keywords.inc.php',   'k');
	$tabs[2] = array('glossary_terms','glossary.inc.php',   'g');
	$tabs[3] = array('preview',       'preview.inc.php',    'r');
	$tabs[4] = array('accessibility', 'accessibility.inc.php','a');	

	return $tabs;
}

function output_tabs($current_tab, $changes) {
	global $_base_path;
	$tabs = get_tabs();
	echo '<table cellspacing="0" cellpadding="0" width="90%" border="0" summary="" align="center"><tr>';
	echo '<td>&nbsp;</td>';
	$num_tabs = count($tabs);
	for ($i=0; $i < $num_tabs; $i++) {
		if ($current_tab == $i) {
			echo '<td class="etabself" width="20%" nowrap="nowrap">';
			if ($changes[$i]) {
				echo '<img src="'.$_base_path.'images/changes_bullet.gif" alt="'._AT('usaved_changes_made').'" height="12" width="15" />';
			}
			echo _AT($tabs[$i][0]).'</td>';
		} else {
			echo '<td class="etab" width="20%">';
			if ($changes[$i]) {
				echo '<img src="'.$_base_path.'images/changes_bullet.gif" alt="'._AT('usaved_changes_made').'" height="12" width="15" />';
			}
			echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'hand\';" /></td>';
		}	
		echo '<td>&nbsp;</td>';
	}	
	echo '</tr></table>';
}

// save all changes to the DB
function save_changes( ) {
	global $contentManager, $db;

	$_POST['pid']	= intval($_POST['pid']);
	$_POST['cid']	= intval($_POST['cid']);

	$_POST['title'] = trim($_POST['title']);
	$_POST['text']	= trim($_POST['text']);
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['keywords']	= trim($_POST['keywords']);
	$_POST['new_ordering']	= intval($_POST['new_ordering']);

	if (!($release_date = generate_release_date())) {
		$errors[] = AT_ERROR_BAD_DATE;
	}

	if ($_POST['title'] == '') {
		$errors[] = AT_ERROR_NO_TITLE;
	}
		
	if (!isset($errors)) {
		if ($_POST['cid']) {
			/* editing an existing page */

			$err = $contentManager->editContent($_POST['cid'], $_POST['title'], $_POST['text'], $_POST['keywords'], $_POST['new_ordering'], $_POST['related'], $_POST['formatting'], $_POST['new_pid'], $release_date);

			unset($_POST['move']);
			unset($_POST['new_ordering']);
		} else {
			/* insert new */
			
			$inherit_release_date = 0; // for now.

			$cid = $contentManager->addContent($_SESSION['course_id'],
												  $_POST['new_pid'],
												  $_POST['new_ordering'],
												  $_POST['title'],
												  $_POST['text'],
												  $_POST['keywords'],
												  $_POST['related'],
												  $_POST['formatting'],
												  $release_date,
												  $inherit_release_date);
			$_POST['cid']    = $cid;
			$_REQUEST['cid'] = $cid;
		}
	}

	/* insert glossary terms */
	if (is_array($_POST['glossary_defs']) && ($num_terms = count($_POST['glossary_defs']))) {
		global $glossary;

		foreach($_POST['glossary_defs'] as $w => $d) {
			$old_w = $w;
			$w = urldecode($w);

			if ($glossary[$old_w] && (($glossary[$old_w] != $d) || isset($_POST['related_term'][$old_w])) ) {
				$w = mysql_real_escape_string($w);
				$related_id = intval($_POST['related_term'][$old_w]);
				$sql = "UPDATE ".TABLE_PREFIX."glossary SET definition='$d', related_word_id=$related_id WHERE word='$w' AND course_id=$_SESSION[course_id]";
				$result = mysql_query($sql, $db);
				$glossary[$old_w] = $d;
			} else if (!$glossary[$old_w]) {
				$w = mysql_real_escape_string($w);
				$related_id = intval($_POST['related_term'][$old_w]);
				$sql = "INSERT INTO ".TABLE_PREFIX."glossary VALUES (0, $_SESSION[course_id], '$w', '$d', $related_id)";
				$result = mysql_query($sql, $db);
				$glossary[$old_w] = $d;
			}
		}
	}

	if (!isset($errors)) {
		header('Location: '.$_SERVER['PHP_SELF'].'?cid='.$_POST['cid'].SEP.'f='.AT_FEEDBACK_CONTENT_UPDATED.SEP.'tab='.$_POST['current_tab']);
		exit;
	} else {
		return $errors;
	}
}

function generate_release_date($now = false) {
	if ($now) {
		$day  = date('d');
		$month= date('m');
		$year = date('Y');
		$hour = date('H');
		$min  = 0;
	} else {
		$day	= intval($_POST['day']);
		$month	= intval($_POST['month']);
		$year	= intval($_POST['year']);
		$hour	= intval($_POST['hour']);
		$min	= intval($_POST['minute']);
	}

	if (!checkdate($month, $day, $year)) {
		return false;
	}

	if (strlen($month) == 1){
		$month = "0$month";
	}
	if (strlen($day) == 1){
		$day = "0$day";
	}
	if (strlen($hour) == 1){
		$hour = "0$hour";
	}
	if (strlen($min) == 1){
		$min = "0$min";
	}
	$release_date = "$year-$month-$day $hour:$min:00";
	
	return $release_date;
}

function check_for_changes($row) {
	global $contentManager, $cid, $glossary, $glossary_ids_related;

	$changes = array();

	if ($row && strcmp(trim($_POST['title']), $row['title'])) {
		$changes[0] = true;
	} else if (!$row && $_POST['title']) {
		$changes[0] = true;
	}

	if ($row && strcmp(trim($_POST['text']), trim($row['text']))) {
		$changes[0] = true;
	} else if (!$row && $_POST['text']) {
		$changes[0] = true;
	}

	/* formatting: */
	if ($row && strcmp(trim($_POST['formatting']), $row['formatting'])) {
		$changes[0] = true;
	} else if (!$row && $_POST['formatting']) {
		$changes[0] = true;
	}

	/* release date: */
	if ($row && strcmp(substr(generate_release_date(), 0, -2), substr($row['release_date'], 0, -2))) {
		/* the substr was added because sometimes the release_date in the db has the seconds field set, which we dont use */
		/* so it would show a difference, even though it should actually be the same, so we ignore the seconds with the -2 */
		/* the seconds gets added if the course was created during the installation process. */
		$changes[1] = true;
		debug('x');
	} else if (!$row && strcmp(generate_release_date(), generate_release_date(true))) {
		$changes[1] = true;
	}

	/* related content: */
	$row_related = $contentManager->getRelatedContent($cid);

	if (is_array($_POST['related']) && is_array($row_related)) {
		$sum = array_sum(array_diff($_POST['related'], $row_related));
		$sum += array_sum(array_diff($row_related, $_POST['related']));
		if ($sum > 0) {
			$changes[1] = true;
			debug('w');
		}
	} else if (!is_array($_POST['related']) && !empty($row_related)) {
		$changes[1] = true;
		debug('y');
	}

	/* ordering */
	if ($cid && isset($_POST['move']) && ($_POST['move'] != -1) && ($_POST['move'] != $row['content_parent_id'])) {
		$changes[1] = true;
		debug('z');
	}

	if ($cid && (($_POST['new_ordering'] != $_POST['ordering']) || ($_POST['new_pid'] != $_POST['pid']))) {
		$changes[1] = true;
		debug('q');
	}

	/* keywords */
	if ($row && strcmp(trim($_POST['keywords']), $row['keywords'])) {
		$changes[1] = true;
	}  else if (!$row && $_POST['keywords']) {
		$changes[1] = true;
	}

	/* glossary */
	if (is_array($_POST['glossary_defs'])) {
		$diff = array_diff(array_keys($_POST['glossary_defs']), array_keys($glossary));
		if ($diff) {
			/* new terms added */
			$changes[2] = true;
		} else {

			/* check if added terms have changed */
			foreach ($_POST['glossary_defs'] as $w => $d) {
				if ($d != $glossary[$w]) {
					/* an existing term has been changed */
					$changes[2] = true;
					break;
				}
			}
		}

		if (is_array($_POST['related_term'])) {
			foreach($_POST['related_term'] as $term => $r_id) {
				if ($glossary_ids_related[$term] != $r_id) {
					$changes[2] = true;
					break;
				}
			}
		}
	}
	
	return $changes;
}

function paste_from_file(&$errors, &$feedback) {
	if ($_FILES['uploadedfile']['name'] == '')	{
		$errors = AT_ERROR_FILE_NOT_SELECTED;
		return;
	}
	if ($_FILES['uploadedfile']['name']
		&& (($_FILES['uploadedfile']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile']['type'] == 'text/html')) )
		{

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = strtolower($path_parts['extension']);

		if (in_array($ext, array('html', 'htm'))) {
			$_POST['text'] = file_get_contents($_FILES['uploadedfile']['tmp_name']);

			/* get the <title></title> of this page				*/

			$start_pos	= strpos(strtolower($_POST['text']), '<title>');
			$end_pos	= strpos(strtolower($_POST['text']), '</title>');

			if (($start_pos !== false) && ($end_pos !== false)) {
				$start_pos += strlen('<title>');
				$_POST['title'] = trim(substr($_POST['text'], $start_pos, $end_pos-$start_pos));
			}

			unset($start_pos);
			unset($end_pos);

			$_POST['text'] = get_html_body($_POST['text']);

			$feedback[]=AT_FEEDBACK_FILE_PASTED;
		} else if ($ext == 'txt') {
			$_POST['text'] = file_get_contents($_FILES['uploadedfile']['tmp_name']);
			$feedback[]=AT_FEEDBACK_FILE_PASTED;
		}
	} else {
		$errors[] = AT_ERROR_BAD_FILE_TYPE;
	}

	return;
}

function write_temp_file() {
	global $db;

	$temp_file = 'acheck_'.time().'.html';

	if ($handle = fopen('../content/'.$temp_file, 'wb+')) {
		$temp_content = '<h2>'.AT_print(stripslashes($_POST['title']), 'content.title').'</h2>';

		if ($_POST['text'] != '') {
			$temp_content .= format_content(stripslashes($_POST['text']), $_POST['formatting'], $_POST['glossary_defs']);
		}
		$temp_title = $_POST['title'];

		$html_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>{TITLE}</title>
			<meta name="Generator" content="ATutor - this file is safe to delete">
		</head>
		<body>{CONTENT}</body>
		</html>';

		$page_html = str_replace(	array('{TITLE}', '{CONTENT}'),
									array($temp_title, $temp_content),
									$html_template);
		
		if (!fwrite($handle, $page_html)) {
		   $errors[] = AT_ERROR_FILE_NOT_SAVED;       
	   }
	} else {
		$errors[] = AT_ERROR_FILE_NOT_SAVED;
	}
	return $temp_file;
}
?>