<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/cats_categories/lib/admin_categories.inc.php');
require_once(AT_INCLUDE_PATH.'lib/tinymce.inc.php');
//require_once(AT_INCLUDE_PATH.'lib/course_icon.inc.php');

$_GET['show_courses'] = $addslashes(intval($_GET['show_courses']));
$_GET['current_cat'] = $addslashes(intval($_GET['current_cat']));

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

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor(false, 'banner');
}

if (!isset($isadmin, $course, $db)) {
	return;	
}

if (isset($_POST['form_course'])) {

	$row['course_id']			= $_POST['course'];
	$row['title']				= $_POST['title'];
	$row['primary_language']	= $_POST['primary_language'];
	$row['member_id']			= $_POST['member_id'];
	$row['description']			= $_POST['description'];
	$row['course_dir_name']		= $_POST['course_dir_name'];
	$row['cat_id']				= $_POST['cat_id'];
	$row['content_packaging']	= $_POST['content_packaging'];

	$row['access']				= $_POST['access'];
	$row['notify']				= $_POST['notify'];

	$row['max_quota']			= $_POST['max_quota'];
	$row['max_file_size']		= $_POST['max_file_size'];

	$row['created_date']		= date('Y-m-d H:i:s');
	$row['primary_language']    = $_POST['pri_lang'];
	$row['rss']                 = $_POST['rss'];

	$row['copyright']			= $_POST['copyright'];
	$row['icon']				= $_POST['icon'];
	$row['banner']              = stripcslashes($_POST['banner']);

	if (intval($_POST['release_date'])) {
		$day_release	= intval($_POST['day_release']);
		$month_release	= intval($_POST['month_release']);
		$year_release	= intval($_POST['year_release']);
		$hour_release	= intval($_POST['hour_release']);
		$min_release	= intval($_POST['min_release']);

		if (strlen($month_release) == 1){
			$month_release = "0$month_release";
		}
		if (strlen($day_release) == 1){
			$day_release = "0$day_release";
		}
		if (strlen($hour_release) == 1){
			$hour_release = "0$hour_release";
		}
		if (strlen($min_release) == 1){
			$min_release = "0$min_release";
		}
		$row['release_date'] = "$year_release-$month_release-$day_release $hour_release:$min_release:00";
	} else {
		$row['release_date'] = 0;
	}

	if (intval($_POST['end_date'])) {
		$day_end	= intval($_POST['day_end']);
		$month_end	= intval($_POST['month_end']);
		$year_end	= intval($_POST['year_end']);
		$hour_end	= intval($_POST['hour_end']);
		$min_end	= intval($_POST['min_end']);

		if (strlen($month_end) == 1){
			$month_end = "0$month_end";
		}
		if (strlen($day_end) == 1){
			$day_end = "0$day_end";
		}
		if (strlen($hour_end) == 1){
			$hour_end = "0$hour_end";
		}
		if (strlen($min_end) == 1){
			$min_end = "0$min_end";
		}
		$row['end_date'] = "$year_end-$month_end-$day_end $hour_end:$min_end:00";
	} else {
		$row['end_date'] = 0;
	}

} else if ($course) {
	$sql	= "SELECT *, DATE_FORMAT(release_date, '%Y-%m-%d %H:%i:00') AS release_date, DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:00') AS end_date  FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	if (!($row	= mysql_fetch_assoc($result))) {
		echo _AT('no_course_found');
		return;
	}

} else {
	//new course defaults
	$row['content_packaging']	= 'top';
	$row['access']				= 'protected';
	$row['notify']				= '';
	$row['hide']				= '';

	$row['max_quota']			= AT_COURSESIZE_DEFAULT;
	$row['max_file_size']		= AT_FILESIZE_DEFAULT;

	$row['primary_language']	= $_SESSION['lang'];
	$row['created_date']		= date('Y-m-d H:i:s');
	$row['rss']                 = 0; // default to off
	$row['release_date']		= '0';
	$row['end_date']            = '0';
}
/*
if (($_POST['setvisual'] || $_POST['settext']) && !$_POST['submit']){
	$anchor =  "#banner";
} */

if ($_POST['customicon']) {
    echo "<script language='javascript' type='text/javascript'>document.getElementById('uploadform').focus();</script>";
}
$savant->assign('row', $row);
$savant->display('admin/courses/edit_course.tmpl.php');
?>

<script language="javascript" type="text/javascript">
<!--
function enableNotify() {
	document.course_form.notify.disabled = false;
	document.course_form.hide.disabled = false;
}

function disableNotify() {
	document.course_form.notify.disabled = true;
	document.course_form.hide.disabled = true;
}

function enableOther()		{ document.course_form.quota_entered.disabled = false; }
function disableOther()		{ document.course_form.quota_entered.disabled = true; }
function enableOther2()		{ document.course_form.filesize_entered.disabled = false; }
function disableOther2()	{ document.course_form.filesize_entered.disabled = true; }

function enableRelease() { 
	document.course_form.day_release.disabled = false; 
	document.course_form.month_release.disabled = false; 
	document.course_form.year_release.disabled = false; 
	document.course_form.hour_release.disabled = false; 
	document.course_form.min_release.disabled = false; 
}
function disableRelease() { 
	document.course_form.day_release.disabled = true; 
	document.course_form.month_release.disabled = true; 
	document.course_form.year_release.disabled = true; 
	document.course_form.hour_release.disabled = true; 
	document.course_form.min_release.disabled = true; 
}

function SelectImg() {
    // UPDATED by Martin Turlej - for custom course icon

    var boolForce = document.getElementById('boolForce').value;
	if (document.course_form.icon.options[document.course_form.icon.selectedIndex].value == "") {
		document.getElementById('i0').src = "images/clr.gif";
		document.getElementById('i0').alt = "";
	} else {
        var iconIndx = document.course_form.icon.selectedIndex;
        var custIndx = document.getElementById('custOptCount').value;
        var courseId = document.getElementById('courseId').value;

        // if icon is part of custom icons choose corresponding directory
        if (iconIndx <= custIndx && boolForce != '') {			
            var dir = (boolForce == 1) ? "get_course_icon.php/?id="+courseId : "/content/"+courseId+"/custom_icons/";
        } else {
            var dir = "images/courses/";
        }

		document.getElementById('i0').src = dir + document.course_form.icon.options[iconIndx].value;
		document.getElementById('i0').alt = document.course_form.icon.options[iconIndx].value;
	}
}

function toggleFrm(id) {
    if (document.getElementById(id).style.display == "none") {
		//show
		document.getElementById(id).style.display='';	

		if (id == "c_folder") {
			document.form0.new_folder_name.focus();
		} else if (id == "upload") {
			document.form0.file.focus();
		}

	} else {
		//hide
		document.getElementById(id).style.display='none';
	}
}
// -->
</script>