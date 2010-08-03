<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
$_user_location='public';
define(AT_INCLUDE_PATH, '../../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
include(AT_JB_INCLUDE.'classes/Employer.class.php');
require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet
$_custom_head .= '
    <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-layout.css" />
    <link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-text.css" />
    <script type="text/javascript" src="'.$_base_path.'mods/job_board/include/js/edit.js"></script>
    ';

if (!Employer::authenticate()){
	$msg->addError('ACCESS_DENIED');
	header('Location: ../index.php');
	exit;
}

/* 
 * Add the submenu on this page so that user can go back to the listing.
 * Reason why this is not included in module.php is because we don't want the 
 * 'edit_post' submenu to show on job_board/index.php
 */
$_pages[AT_JB_BASENAME.'index_admin.php']['children'] = array(AT_JB_BASENAME.'admin/edit_post.php');

$jid = intval($_GET['jid']);
$job = new Job();
$job_post = $job->getJob($jid);

//visual editor
if ((!$_POST['setvisual'] && $_POST['settext']) || !$_GET['setvisual']){
	$onload = 'document.form.title.focus();';
}

//handle edit
if(isset($_POST['submit'])){
	//concat the closing date values
	$year = intval($_POST['year_jb_closing_date']);
	$month = intval($_POST['month_jb_closing_date']);
	$month = ($month < 10)?'0'.$month:$month;
	$day = intval($_POST['day_jb_closing_date']);
	$day = ($day < 10)?'0'.$day:$day;
	$hour = intval($_POST['hour_jb_closing_date']);
	$hour = ($hour < 10)?'0'.$hour:$hour;
	$min = intval($_POST['min_jb_closing_date']);
	$min = ($min < 10)?'0'.$min:$min;
	$jb_closing_date = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':00';

	//approval state.
	$approval_state = ($_config['jb_posting_approval']==1)?AT_JB_POSTING_STATUS_UNCONFIRMED:AT_JB_POSTING_STATUS_CONFIRMED;	

	$job->updateJob($jid, $_POST['title'], $_POST['jb_description'], $_POST['jb_categories'], $_POST['jb_is_public'], $jb_closing_date, $approval_state);
	$msg->addFeedback('JB_POST_UPDATED_SUCCESS');
	header('Location: home.php');
	exit;
}

//load visual editor base on personal preferences
if (!isset($_REQUEST['setvisual']) && !isset($_REQUEST['settext'])) {
	if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 1) {
		$_POST['formatting'] = 1;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;

	} else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) {
		$_POST['formatting'] = 1;
		$_POST['settext'] = 0;
		$_POST['setvisual'] = 1;

	} else { // else if == 0
		$_POST['formatting'] = 0;
		$_REQUEST['settext'] = 0;
		$_REQUEST['setvisual'] = 0;
	}
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('employer/jb_employer_header.tmpl.php');
$savant->assign('job', $job);
$savant->assign('categories', $job->getCategories());
$savant->assign('job_post', $job_post);
$savant->display('employer/jb_edit_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
