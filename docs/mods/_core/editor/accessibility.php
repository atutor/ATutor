<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: accessibility.inc.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/editor/editor_tab_functions.inc.php');

$cid = intval($_POST['cid']);

if ($cid == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$missing_fields[] = _AT('content_id');
	$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$result = $contentManager->getContentPage($cid);

if (!($content_row = @mysql_fetch_assoc($result))) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('PAGE_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$course_base_href = '';
$content_base_href = '';

//make decisions
if ($_POST['make_decision']) 
{
	//get list of decisions	
	$desc_query = '';
	if (is_array($_POST['d'])) {
		foreach ($_POST['d'] as $sequenceID => $decision) {
			$desc_query .= '&'.$sequenceID.'='.$decision;
		}
	}

	$checker_url = AT_ACHECKER_URL. 'decisions.php?'
				.'uri='.urlencode($_POST['pg_url']).'&id='.AT_ACHECKER_WEB_SERVICE_ID
				.'&session='.$_POST['sessionid'].'&output=html'.$desc_query;

	if (@file_get_contents($checker_url) === false) {
		$msg->addInfo('DECISION_NOT_SAVED');
	}
} 
else if (isset($_POST['reverse'])) 
{
	$reverse_url = AT_ACHECKER_URL. 'decisions.php?'
				.'uri='.urlencode($_POST['pg_url']).'&id='.AT_ACHECKER_WEB_SERVICE_ID
				.'&session='.$_POST['sessionid'].'&output=html&reverse=true&'.key($_POST['reverse']).'=N';
	
	if (@file_get_contents($reverse_url) === false) {
		$msg->addInfo('DECISION_NOT_REVERSED');
	} else {
		$msg->addInfo('DECISION_REVERSED');
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>
	<div class="row">
		<?php 					
			echo '<input type="hidden" name="body_text" value="'.htmlspecialchars(stripslashes($_POST['body_text'])).'" />';

			if (!$cid) {
				$msg->printInfos('SAVE_CONTENT');

				echo '</div>';

				return;
			}

		$msg->printInfos();
		if ($_POST['body_text'] != '') {
			//save temp file
			$_POST['content_path'] = $content_row['content_path'];
			write_temp_file();

			$pg_url = AT_BASE_HREF.'get_acheck.php/'.$_POST['cid'] . '.html';
			$checker_url = AT_ACHECKER_URL.'checkacc.php?uri='.urlencode($pg_url).'&id='.AT_ACHECKER_WEB_SERVICE_ID
							. '&guide=WCAG2-L2&output=html';

			$report = @file_get_contents($checker_url);

			if (stristr($report, '<div id="error">')) {
				$msg->printErrors('INVALID_URL');
			} else if ($report === false) {
				$msg->printInfos('SERVICE_UNAVAILABLE');
			} else {
				echo '<input type="hidden" name="pg_url" value="'.$pg_url.'" />';
				echo $report;	

				echo '<p>'._AT('access_credit').'</p>';
			}
			//delete file
			@unlink(AT_CONTENT_DIR . $_POST['cid'] . '.html');
		
		} else {
			$msg->printInfos('NO_PAGE_CONTENT');
		} 

	?>
	</div>
<?php 
require(AT_INCLUDE_PATH.'footer.inc.php');
?>