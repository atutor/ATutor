<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: filemanager_display_alternatives.inc.php 7208 2008-07-04 16:07:24Z silvia $

if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_INCLUDE_PATH', '../../include/');
//require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

global $db;


// from tools/filemanager/index.php
if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) && 
	(!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
	$popup = TRUE;
	$framed = FALSE;
} else if (isset($_REQUEST['framed']) && $_REQUEST['framed'] && isset($_REQUEST['popup']) && $_REQUEST['popup']) {
	$popup = TRUE;
	$framed = TRUE;
} else {
	$popup = FALSE;
	$framed = FALSE;
}
// end tools/filemanager/index.php

// tools/filemanager/top.php
//require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$MakeDirOn = true;

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize		= $row['max_file_size'];

if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
	$my_MaxCourseSize = $MaxCourseSize;
}
if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
	$my_MaxFileSize = $MaxFileSize;
} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
	$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
}

$MaxSubDirs  = 5;
$MaxDirDepth = 10;

if ($_GET['pathext'] != '') {
	$pathext = urldecode($_GET['pathext']);
} else if ($_POST['pathext'] != '') {
	$pathext = $_POST['pathext'];
}

if (strpos($pathext, '..') !== false) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('UNKNOWN');	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if($_GET['back'] == 1) {
	$pathext  = substr($pathext, 0, -1);
	$slashpos = strrpos($pathext, '/');
	if($slashpos == 0) {
		$pathext = '';
	} else {
		$pathext = substr($pathext, 0, ($slashpos+1));
	}

}

$start_at = 2;
/* remove the forward or backwards slash from the path */
$newpath = $current_path;
$depth = substr_count($pathext, '/');

if ($pathext != '') {
	$bits = explode('/', $pathext);
	foreach ($bits as $bit) {
		if ($bit != '') {
			$bit_path .= $bit;

			$_section[$start_at][0] = $bit;
			$_section[$start_at][1] = '../tools/filemanager/index.php?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed;

			$start_at++;
		}
	}
	$bit_path = "";
	$bit = "";
}

/* if upload successful, close the window */
if ($f) {
	$onload = 'closeWindow(\'progWin\');';
}

/* make new directory */
if (isset($_POST['mkdir'])) {
if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
	$_POST['dirname'] = trim($_POST['dirname']);

	/* anything else should be okay, since we're on *nix..hopefully */
	$_POST['dirname'] = ereg_replace('[^a-zA-Z0-9._]', '', $_POST['dirname']);

	if ($_POST['dirname'] == '') {
		$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
	} 
	else if (strpos($_POST['dirname'], '..') !== false) {
		$msg->addError('BAD_FOLDER_NAME');
	}	
	else {
		$result = @mkdir($current_path.$pathext.$_POST['dirname'], 0700);
		if($result == 0) {
			$msg->addError(array('FOLDER_NOT_CREATED', $_POST['dirname'] ));
		}
		else {
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
}
}
$newpath = substr($current_path.$pathext, 0, -1);


/* open the directory */
if (!($dir = @opendir($newpath))) {
	if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
		@mkdir($newpath);
		if (!($dir = @opendir($newpath))) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('CANNOT_CREATE_DIR');			
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		} else {
			$msg->addFeedback('CONTENT_DIR_CREATED');
		}
	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');

		$msg->printErrors('CANNOT_OPEN_DIR');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
}

//end header.inc.php


// upload.php



$_SESSION['done'] = 1;
$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

/* get this courses MaxQuota and MaxFileSize: */
$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$my_MaxCourseSize	= $row['max_quota'];
$my_MaxFileSize	= $row['max_file_size'];

	if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
		$my_MaxCourseSize = $MaxCourseSize;
	}
	if ($my_MaxFileSize == AT_FILESIZE_DEFAULT) {
		$my_MaxFileSize = $MaxFileSize;
	} else if ($my_MaxFileSize == AT_FILESIZE_SYSTEM_MAX) {
		$my_MaxFileSize = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));
	}

$path = AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$_POST['pathext'];

if (isset($_POST['upload'])) {

	if($_FILES['uploadedfile']['name'])	{

		$_FILES['uploadedfile']['name'] = trim($_FILES['uploadedfile']['name']);
		$_FILES['uploadedfile']['name'] = str_replace(' ', '_', $_FILES['uploadedfile']['name']);

		$path_parts = pathinfo($_FILES['uploadedfile']['name']);
		$ext = $path_parts['extension'];

		/* check if this file extension is allowed: */
		/* $IllegalExtentions is defined in ./include/config.inc.php */
		if (in_array($ext, $IllegalExtentions)) {
			$errors = array('FILE_ILLEGAL', $ext);
			$msg->addError($errors);
			header('Location: index.php?pathext='.$_POST['pathext']);
			exit;
		}

		/* also have to handle the 'application/x-zip-compressed'  case	*/
		if (   ($_FILES['uploadedfile']['type'] == 'application/x-zip-compressed')
			|| ($_FILES['uploadedfile']['type'] == 'application/zip')
			|| ($_FILES['uploadedfile']['type'] == 'application/x-zip')){
			$is_zip = true;						
		}

	
		/* anything else should be okay, since we're on *nix.. hopefully */
		$_FILES['uploadedfile']['name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_FILES['uploadedfile']['name']);


		/* if the file size is within allowed limits */
		if( ($_FILES['uploadedfile']['size'] > 0) && ($_FILES['uploadedfile']['size'] <= $my_MaxFileSize) ) {

			/* if adding the file will not exceed the maximum allowed total */
			$course_total = dirsize($path);

			if ((($course_total + $_FILES['uploadedfile']['size']) <= ($my_MaxCourseSize + $MaxCourseFloat)) || ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED)) {

				/* check if this file exists first */
				if (file_exists($path.$_FILES['uploadedfile']['name'])) {
					/* this file already exists, so we want to prompt for override */

					/* save it somewhere else, temporarily first			*/
					/* file_name.time ? */
					$_FILES['uploadedfile']['name'] = substr(time(), -4).'.'.$_FILES['uploadedfile']['name'];

					$f = array('FILE_EXISTS',
									substr($_FILES['uploadedfile']['name'], 5), 
									$_FILES['uploadedfile']['name'],
									$_POST['pathext'],
									$_GET['popup'],
									SEP);
					$msg->addFeedback($f);
				}

				/* copy the file in the directory */
				$result = move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $path.$_FILES['uploadedfile']['name'] );

				if (!$result) {
					require(AT_INCLUDE_PATH.'header.inc.php');
					$msg->printErrors('FILE_NOT_SAVED');
					echo '<a href="tools/filemanager/index.php?pathext=' . $_POST['pathext'] . SEP . 'popup=' . $_GET['popup'] . '">' . _AT('back') . '</a>';
					require(AT_INCLUDE_PATH.'footer.inc.php');
					exit;
				} else {
					if ($is_zip) {
						$f = array('FILE_UPLOADED_ZIP',
										urlencode($_POST['pathext']), 
										urlencode($_FILES['uploadedfile']['name']), 
										$_GET['popup'],
										SEP);
						$msg->addFeedback($f);
		
					} /* else */

					$msg->addFeedback('FILE_UPLOADED');
				}
			} else {
				$msg->addError(array('MAX_STORAGE_EXCEEDED', get_human_size($my_MaxCourseSize)));
			}
		} else {
			$msg->addError(array('FILE_TOO_BIG', get_human_size($my_MaxFileSize)));
		}
	} else {
		$msg->addError('FILE_NOT_SELECTED');
	}
}

// end upload.php



// end tools/filemanager/top.php		
		

//require(AT_INCLUDE_PATH.'html/filemanager_display.inc.php');

		
function get_file_extension($file_name) {
	$ext = pathinfo($file_name);
	return $ext['extension'];
}

function get_file_type_icon($file_name) {
	static $mime;

	$ext = get_file_extension($file_name);

	if (!isset($mime)) {
		require(AT_INCLUDE_PATH .'lib/mime.inc.php');
	}

	if (isset($mime[$ext]) && $mime[$ext][1]) {
		return $mime[$ext][1];
	}
	return 'generic';
}

function get_relative_path($src, $dest) {
	if ($src == '') {
		$path = $dest;
	} else if (substr($dest, 0, strlen($src)) == $src) {
		$path = substr($dest, strlen($src) + 1);
	} else {
		$path = '../' . $dest;
	}

	return $path;
}

// get the course total in Bytes 
$course_total = dirsize($current_path);

$framed = intval($_GET['framed']);
$popup = intval($_GET['popup']);


if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$get_file = 'get.php/';
} else {
	$get_file = 'content/' . $_SESSION['course_id'] . '/';
}


echo '<p>'._AT('current_path').' ';

if ($pathext != '') {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$cid. SEP . 'popup=' . $popup . SEP . 'framed=' . $framed .SEP. 'tab='.$current_tab.'">'._AT('home').'</a> ';
}
else {
	echo _AT('home');
}



if ($pathext == '') {
	$pathext = urlencode($_POST['pathext']);
}

if ($pathext != '') {
	$bits = explode('/', $pathext);

	foreach ($bits as $bit) {
		if ($bit != '') {
			$bit_path .= $bit . '/';
			echo ' / ';

			if ($bit_path == $pathext) {
				echo $bit;
			} else {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . '">' . $bit . '</a>';
			}
		}
	}
	$bit_path = "";
	$bit = "";
}
echo '</p>';



if ($popup == TRUE) {
	$totalcol = 6;
} else {
	$totalcol = 5;
}
$labelcol = 3;

if (TRUE || $framed != TRUE) {

	if ($_GET['overwrite'] != '') {
		// get file name, out of the full path
		$path_parts = pathinfo($current_path.$_GET['overwrite']);

		if (!file_exists($path_parts['dirname'].'/'.$pathext.$path_parts['basename'])
			|| !file_exists($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5))) {
			/* source and/or destination does not exist */
			$msg->addErrors('CANNOT_OVERWRITE_FILE');
		} else {
			@unlink($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));
			$result = @rename($path_parts['dirname'].'/'.$pathext.$path_parts['basename'], $path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));

			if ($result) {
				$msg->addFeedback('FILE_OVERWRITE');
			} else {
				$msg->addErrors('CANNOT_OVERWRITE_FILE');
			}
		}
	}
	
	// filemanager listing table
	// make new directory 
	echo '<fieldset><legend class="group_form">'._AT('add').'</legend>';
	echo '<table cellspacing="1" cellpadding="0" border="0" summary="" align="center">';
	echo '<tr><td colspan="2">';
	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).SEP. 'popup='.$popup.SEP. 'tab='.$current_tab.'">';


if( $MakeDirOn ) {
		if ($depth < $MaxDirDepth) {
			echo '<input type="text" name="dirname" size="20" /> ';
			echo '<input type="hidden" name="mkdir_value" value="true" /> ';
			echo '<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" />';
			echo '&nbsp;<small class="spacer">'._AT('keep_it_short').'';
		} else {
			echo _AT('depth_reached');
		}
	}
	echo '<input type="hidden" name="pathext" value="'.$pathext.'" />';
	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';
//	echo '</form>';
	echo '</td></tr>';

	$my_MaxCourseSize = $system_courses[$_SESSION['course_id']]['max_quota'];

//	$alter=TRUE;
	// upload file 
	if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) 
		|| (($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) && ($course_total < $MaxCourseSize))
		|| ($my_MaxCourseSize-$course_total > 0)) {
		echo '<tr><td  colspan="1">';
//		echo '<form onsubmit="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');" name="form1" method="post" action="../tools/filemanager/upload.php?alter='.$alter. SEP .'cid='.$cid. SEP . 'tab='.$current_tab.'" enctype="multipart/form-data">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
		echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
		echo '<input type="submit" name="upload" value="'._AT('upload').'" onClick="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');" class="button" />';
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />  ';
		echo '<input type="hidden" name="alter" value="TRUE" />  ';
	//	echo _AT('or'); 
	//	echo ' <a href="../tools/filemanager/new.php?pathext=' . urlencode($pathext) . SEP . 'framed=' . $framed . SEP . 'popup=' . $popup . '">' . _AT('file_manager_new') . '</a>';

		if ($popup == TRUE) {
			echo '<input type="hidden" name="popup" value="1" />';
		}
//		echo '</form>';
		echo '</td></tr></table></fieldset>';

	} else {
		echo '</table>';
		echo '</fieldset>';
		$msg->printInfos('OVER_QUOTA');
	}
	echo '<br />';
}
// Directory and File listing 


//echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?cid='.$cid. SEP.'pathext='.urlencode($pathext).SEP. 'popup='.$popup.SEP. 'tab='.$current_tab.'">';
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />';



?>
<table class="data static" summary="" border="0" rules="groups" style="width: 90%">
<thead>
<tr>
<!--	<th scope="col"><input type="checkbox" name="checkall" onclick="Checkall(checkform);" id="selectall" title="<?php echo _AT('select_all'); ?>" /></th>
<th>&nbsp;</th>-->	
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th scope="col"><?php echo _AT('name');   ?></th>
	
<!--	<th scope="col"><?php //echo _AT('date');   ?></th>
	<th scope="col"><?php //echo _AT('size');   ?></th>-->
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="3" align="right">
		<input class="button" type="submit" name="add" value="Add"/>
	</td>
</tr>
</tfoot>
<!--
<tr>
	<td colspan="4" align="right"><strong><?php //echo _AT('directory_total'); ?>:</strong></td>
	<td align="right">&nbsp;<strong><?php //echo get_human_size(dirsize($current_path.$pathext.$file.'/')); ?></strong>&nbsp;</td>
</tr>

<tr>
	<td colspan="4" align="right"><strong><?php // echo _AT('course_total'); ?>:</strong></td>
	<td align="right">&nbsp;<strong><?php // echo get_human_size($course_total); ?></strong>&nbsp;</td>
</tr>
<tr>
	<td colspan="4" align="right"><strong><?php //echo _AT('course_available'); ?>:</strong></td>
	<td align="right"><strong><?php /*
		if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
			echo _AT('unlimited');
		} else if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
			echo get_human_size($MaxCourseSize-$course_total);
		} else {
			echo get_human_size($my_MaxCourseSize-$course_total);
		} */ ?></strong>&nbsp;</td>
</tr>
</tfoot>-->
<?php


if($pathext) : ?>
	<tr>
		<td colspan="3"><a href="<?php echo $_SERVER['PHP_SELF'].'?cid='.$cid. SEP .'back=1'.SEP.'pathext='.$pathext.SEP. 'popup=' . $popup .SEP. 'framed=' . $framed .SEP.'cp='.$_GET['cp'] . SEP. 'tab='.$current_tab; ?>"><img src="<?php echo$_base_href;?>images/arrowicon.gif" border="0" height="11" width="10" alt="" /> <?php echo _AT('back'); ?></a></td>
	</tr>
<?php endif; ?>
<?php
$totalBytes = 0;

if ($dir == '')
	$dir=opendir($current_path);

// loop through folder to get files and directory listing
while (false !== ($file = readdir($dir)) ) {

	// if the name is not a directory 
	if( ($file == '.') || ($file == '..') ) {
		continue;
	}

	// get some info about the file
	$filedata = stat($current_path.$pathext.$file);
	$path_parts = pathinfo($file);
	$ext = strtolower($path_parts['extension']);

	$is_dir = false;

	// if it is a directory change the file name to a directory link 
	if(is_dir($current_path.$pathext.$file)) {
		$size = dirsize($current_path.$pathext.$file.'/');
		$totalBytes += $size;
		$filename = '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$cid. SEP .'pathext='.urlencode($pathext.$file.'/'). SEP . 'popup=' . $popup . SEP . 'framed='. $framed . SEP.'cp='.$_GET['cp']. SEP. 'tab='.$current_tab. SEP. 'alternatives='.$_POST['alternatives'].'">'.$file.'</a>';
		$fileicon = '&nbsp;';
		$fileicon .= '<img src="'.$_base_href.'images/folder.gif" alt="'._AT('folder').':'.$file.'" height="18" width="20" class="img-size-fm1" />';
		$fileicon .= '&nbsp;';
		if(!$MakeDirOn) {
			$deletelink = '';
		}

		$is_dir = true;
	} else if ($ext == 'zip') {

		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="'.$_base_href.'images/icon-zip.gif" alt="'._AT('zip_archive').':'.$file.'" height="16" width="16" border="0" class="img-size-fm2" />&nbsp;';

	} else {
		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="'.$_base_href.'images/file_types/'.get_file_type_icon($filename).'.gif" height="16" width="16" alt="" title="" class="img-size-fm2" />&nbsp;';
	} 
	$file1 = strtolower($file);
	// create listing for dirctor or file
	if ($is_dir) {
		
		$dirs[$file1] .= '<tr><td>&nbsp;</td>';
		$dirs[$file1] .= '<td>'.$fileicon.'</td>';
		$dirs[$file1] .= '&nbsp;';
		$dirs[$file1] .= '<td>'.$filename.'</td>';
		$dirs[$file1] .= '</tr>';
	
	} else {
		
		$files[$file1] .= '<tr>';
		$files[$file1] .= '<tr> <td  align="center">';
		$files[$file1] .= '<input type="radio" id="'.$file.'" value="'.$file.'" name="radio_alt"/> </td>';
		$files[$file1] .= '<td align="center">'.$fileicon.'</td>';

		$files[$file1] .= '<td ><label for="'.$file.'">&nbsp;';

		$files[$file1] .= '<a href="'.$get_file.$pathext.urlencode($filename).'">'.$file.'</a>';

		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="tools/filemanager/zip.php?pathext=' . urlencode($pathext) . SEP . 'file=' . urlencode($file) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed .'">';
			$files[$file1] .= '<img src="'.$_base_href.'images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="img-size-fm3" />';
			$files[$file1] .= '</a>';
		}

		if (in_array($ext, $editable_file_types)) {
			$files[$file1] .= ' <a href="tools/filemanager/edit.php?pathext=' . urlencode($pathext) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'file=' . $file . '">';
			$files[$file1] .= '<img src="'.$_base_href.'images/edit.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('edit').'" height="15" width="18" class="img-size-fm4" />';
			$files[$file1] .= '</a>';
		}

		$files[$file1] .= '&nbsp;</label></td></tr>';
		
	}
} // end while

// sort listing and output directories
if (is_array($dirs)) {
	ksort($dirs, SORT_STRING);
	foreach($dirs as $x => $y) {
		echo $y;
	}
}

//sort listing and output files
if (is_array($files)) {
	ksort($files, SORT_STRING);
	foreach($files as $x => $y) {
		echo $y;
	}
}


echo '</table>';
echo '</form>';



?>

<script type="text/javascript">
//<!--
function insertFile(fileName, pathTo, ext) { 

	// pathTo + fileName should be relative to current path (specified by the Content Package Path)

	if (ext == "gif" || ext == "jpg" || ext == "jpeg" || ext == "png") {
		var info = "<?php echo _AT('alternate_text'); ?>";
		var html = '<img src="' + pathTo+fileName + '" border="0" alt="' + info + '" />';

		if (window.opener.document.form.setvisual.value == 1) {
			if (window.parent.tinyMCE)
				window.parent.tinyMCE.execCommand('mceInsertContent', false, html);

			if (window.opener.tinyMCE)
				window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
		} else {
			insertAtCursor(window.opener.document.form.body_text, html);
		}
	} else if (ext == "mpg" || ext == "avi" || ext == "wmv" || ext == "mov" || ext == "swf" || ext == "mp3" || ext == "wav" || ext == "ogg" || ext == "mid") {
		var html = '[media]'+ pathTo + fileName + '[/media]';
		if (window.opener.document.form.setvisual.value == 1) {
			if (window.parent.tinyMCE)
				window.parent.tinyMCE.execCommand('mceInsertContent', false, html);

			if (window.opener.tinyMCE)
				window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
		} else {
			insertAtCursor(window.opener.document.form.body_text, html);
		}
	} else {
		var info = "<?php echo _AT('put_link'); ?>";
		var html = '<a href="' + pathTo+fileName + '">' + info + '</a>';

		if (window.opener.document.form.setvisual.value == 1) {
			if (window.parent.tinyMCE)
				window.parent.tinyMCE.execCommand('mceInsertContent', false, html);

			if (window.opener.tinyMCE)
				window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
		} else {
			insertAtCursor(window.opener.document.form.body_text, html);
		}
	}
}
function insertAtCursor(myField, myValue) {
	//IE support
	if (window.opener.document.selection) {
		myField.focus();
		sel = window.opener.document.selection.createRange();
		sel.text = myValue;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
		myField.focus();
	} else {
		myField.value += myValue;
		myField.focus();
	}
}
//-->
</script>
		

<?php


closedir($dir);

?>
<script type="text/javascript">
//<!--
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
//-->
</script>
	
