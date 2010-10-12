<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

// At vitals 
include_once('../../include/config.inc.php');
include_once('../../include/lib/mysql_connect.inc.php');

// load ATutor-OpenCaps Module Vitals 
include_once('include/vitals.inc.php');


//echo '<br/>AT_CONTENT_DIR: '.AT_CONTENT_DIR;
//echo '<br/>AT_BASE_HREF: '.AT_BASE_HREF;

// check if is GET or POST requet

// initialize vars
$method = '';
$action = '';
$projectId = '';
$captionData = '';

if($ocAtSettings['atWebPath'] == '')
{
	$atWebPath_replace = str_replace('mods/AtOpenCaps/service.php','',$_SERVER['SCRIPT_NAME']);
	$ocAtSettings['atWebPath'] = 'http://'.$_SERVER['HTTP_HOST'].''.$atWebPath_replace;
}

if (isset($_GET['action']) && $_GET['action'] !='')
{
	$action = $_GET['action'];
	
		if (isset($_GET['id']) && $_GET['id'] !='')
		{
		$projectId = $_GET['id'];
		}
} 

if (isset($_POST['action']) && $_POST['action'] !='')
{
	$action = $_POST['action'];
	
		if (isset($_POST['id']) && $_POST['id'] !='')
		{
		$projectId = $_POST['id'];
		}
} 

// Get media data and return JSon

if ($action=='getMedia' && $projectId !='')
{
	$myProjectManager = new ATOCProjectManager();
	
	$activeProjectJson = $myProjectManager->_getProjecDataJson($projectId,$ocAtSettings['atWebPath']);
	echo $activeProjectJson;
	
	// start OC Json class
} else if ($action=='getMedia') {
	echo "Invalid request";
	
} 


// save caption data

if ($action=='putCaps' && $projectId != '' &&  isset($_POST['cc']) && $_POST['cc']!='')
{
	$captionData = $_POST['cc'];
	
	if(isset($_POST['width']))
	{
		$theWidth = $_POST['width'];
	} else {
		$theWidth = '';
	}
	if(isset($_POST['height']))
	{
		$theHeight = $_POST['height'];
	} else {
		$theHeight = '';
	}
	$myProjectManager = new ATOCProjectManager();

	$saveMSG = $myProjectManager->_saveCaptionData($projectId,$captionData,$theWidth,$theHeight);

	//echo $saveMSG;

} 
?>