<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'file_manager';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/file_manager.php';

authenticate(AT_PRIV_FILES);

$_header_file = 'html/frameset/header.inc.php';
$_footer_file = 'html/frameset/footer.inc.php';

$start_at = 2;

$current_path = '../../../content/'.$_SESSION['course_id'].'/';
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
$MaxDirDepth = 3;

if ($_GET['pathext'] != '') {
	$pathext = urldecode($_GET['pathext']);
}

if (strpos($pathext, '..') !== false) {
	require(AT_INCLUDE_PATH.$_header_file);
	$errors[]=AT_ERROR_UNKNOWN;
	print_errors($errors);
	require(AT_INCLUDE_PATH.$_footer_file);
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


/* remove the forward or backwards slash from the path */
$newpath = $current_path;
$depth = substr_count($pathext, '/');

if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];

	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		$_section[$start_at][0] = $bits[$i];
		$_section[$start_at][1] = 'tools/file_manager.php?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/';

		$start_at++;
	}
	$_section[$start_at][0] = $bits[count($bits)-2];
}

/* if upload successful, close the window */
if ($f) {
	$onload = 'onbeforeload="closeWindow(\'progWin\');"';
}


if (!defined('AT_INCLUDE_PATH')) { exit; }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>" lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title>ATutor - <?php echo $_SESSION['course_title'];
	if ($cid != 0) {
		$myPath = $path;
		$num_path = count($myPath);
		for ($i =0; $i<$num_path; $i++) {
			echo ' - ';
			echo $myPath[$i]['title'];
		}
	} else if (is_array($_section) ) {
		$num_sections = count($_section);
		for($i = 0; $i < $num_sections; $i++) {
			echo ' - ';
			echo $_section[$i][0];
		}
	}
	?></title>
	<base href="<?php echo $_base_href; ?>" />
	<link rel="stylesheet" href="<?php echo $_base_path; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<?php
		
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
</head>
<body onload="Init()" bgcolor="#FFFFFF">
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<script type="javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no,scrollbar=auto");
	newWindow.focus();
}
</script>



<script type="text/javascript" src="<?php echo $_base_href.'/jscripts/htmlarea/popups/popup.js'; ?>"></script>
<script type="text/javascript">
window.resizeTo(400, 500);

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  if (param) {
      document.getElementById("f_href").value = param["choosfile"];
  }
  document.getElementById("f_href").focus();
};

function onOK() {
  /* specifically for "f_href" radio buttons */
  var el = document.forms["files"].f_href;
  var param = new Object();
  for (var i=0; i < el.length; i++) {  // find selected radio button for list of files
	  if (el[i].checked) {
		  param["f_href"] = el[i].value;
		  param["f_title"] = el[i].value;
		  __dlg_close(param);
		  return false;
	  }
  }
  if (el.checked) {   // get value if there's only one file and the radio button is selected
	  param["f_href"] = el.value;
	  param["f_title"] = el.value;
	  __dlg_close(param);
	  return false;
  }
  alert ("You must select a file to import");
};


function onCancel() {
  __dlg_close(null);
  return false;
};

</script>






<?php

echo '<table width="100%" cellpadding="0" cellspacing="0"><tr><td class="cat2"></td></tr></table>'."\n";

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" target="content">'._AT('tools').'</a>';
	}
	echo '</h2>'."\n";


	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('file_manager');
	}
	echo '</h3>'."\n";

	if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
		$_POST['dirname'] = trim($_POST['dirname']);
		$_POST['dirname'] = str_replace(' ', '_', $_POST['dirname']);

		/* anything else should be okay, since we're on *nix.. hopefully */
		$_POST['dirname'] = ereg_replace('[^a-zA-Z0-9._]', '', $_POST['dirname']);

		$result = @mkdir($current_path.'/'.$pathext.$_POST['dirname'], 0700);
		if($result == 0) {
			$errors[]=AT_ERROR_FOLDER_NOT_CREATED;
			print_errors($errors);
		}
	}


	if ($_GET['delete'] && !$_GET['d']) {
		if(is_dir($current_path.$pathext.$_GET['delete'])) {
			$warnings[]=array(AT_WARNING_CONFIRM_DIR_DELETE, $_GET['delete']);
		} else {
			$warnings[]=array(AT_WARNING_CONFIRM_FILE_DELETE, $_GET['delete']);
		}
		print_warnings($warnings);
		echo '<p><a href="tools/file_manager.php?delete='.$_GET['delete'].SEP.'pathext='.$_GET['pathext'].SEP.'d=1'.SEP.'frame='.$_GET[frame].'">'._AT('yes_delete').'</a>, <a href="tools/file_manager.php?pathext='.$_GET['pathext'].SEP.'frame='.$_GET[frame].'">'._AT('no_cancel').'</a></p>'."\n";
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}

	$newpath = substr($current_path.$pathext, 0, -1);

	/* open the directory */
	if (!($dir = opendir($newpath))) {
		if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
			@mkdir($newpath);
			if (!($dir = @opendir($newpath))) {
				$errors[] = AT_ERROR_CANNOT_CREATE_DIR;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			} else {
				print_feedback(AT_FEEDBACK_CONTENT_DIR_CREATED);
			}
		} else {
			$errors[] = AT_ERROR_CANNOT_OPEN_DIR;
			print_errors($errors);
			require(AT_INCLUDE_PATH.$_footer_file);
			exit;
		}
	}

	/* delete the file or empty directory */
	if (($_GET['delete'] != '') && ($_GET['d'])) {
		if(is_dir($current_path.$pathext.$_GET['delete'])) {
			
			if (strpos($_GET['delete'], '..') !== false) {
				$errors[]=$AT_ERROR_UNKNOWN;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}

			if (!($tempdir = @opendir($current_path.$pathext.$_GET['delete']))) {
				$errors[]=AT_ERROR_DIR_NOT_DELETED;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}
			
			/* check if this dir is empty or not */
			$count =0;
			while (false !== ($tempfile = @readdir($tempdir)) ) {
				$count++;
				if ($count > 2) {
					break;
				}
			}
			@closedir($tempdir);

			if ($count > 2) {
				$errors[]=AT_ERROR_DIR_NOT_EMPTY;
				print_errors($errors);
			} else {
				$result = @rmdir($current_path.$pathext.$_GET['delete']);
				if (!$result) {
					$errors[]=AT_ERROR_DIR_NO_PERMISSION;
					print_errors($errors);
				} else {
					$feedback[]=AT_FEEDBACK_DIR_DELETED;
					print_feedback($feedback);
				}
			}
		} else {
			@unlink($current_path.$pathext.$_GET['delete']);
			$feedback[]=AT_FEEDBACK_FILE_DELETED;
			print_feedback($feedback);
		}
	}

	if (isset($_GET['overwrite'])) {
		// get file name, out of the full path
		$path_parts = pathinfo($current_path.$_GET['overwrite']);

		if (!file_exists($path_parts['dirname'].'/'.$pathext.$path_parts['basename'])
			|| !file_exists($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5))) {
			/* source and/or destination does not exist */
			$errors[]	= AT_ERROR_CANNOT_OVERWRITE_FILE;
			print_errors($errors);
		} else {
			$result = @rename($path_parts['dirname'].'/'.$pathext.$path_parts['basename'],
								$path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));

			if ($result) {
				$feedback[] = AT_FEEDBACK_FILE_OVERWRITE;
				print_feedback($feedback);
			} else {
				$errors[]	= AT_ERROR_CANNOT_OVERWRITE_FILE;
				print_errors($errors);
			}
		}
	}


	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'">'."\n";
	if( $MakeDirOn ) {
		if ($depth < $MaxDirDepth) {
			echo '<input type="text" name="dirname" size="20" class="formfield" /> '."\n";
			echo '<input type="hidden" name="mkdir_value" value="true" /> '."\n";
			echo '<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" /><br /><small class="spacer">'._AT('keep_it_short').'</small>'."\n";
		} else {
			echo _AT('depth_reached');
		}
	}
	echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
	echo '</form>'."\n";

	/* get the course total in Bytes */
	$course_total = dirsize($current_path);

	if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) || ($my_MaxCourseSize-$course_total > 0)) {
		echo '<form onsubmit="openWindow(\''.$_base_href.'tools/prog.php\');" name="form1" method="post" action="tools/upload.php?frame=1" enctype="multipart/form-data">'."\n";
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />'."\n";
		echo '<br />';
		echo '<input type="file" name="uploadedfile" class="formfield" size="20" />'."\n";

		echo ' <input type="submit" name="submit" value="'._AT('upload').'" class="button" />'."\n";
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
		echo '</form>'."\n";
	} else {
		print_infos(AT_INFOS_OVER_QUOTA);
	}
	
	echo '<hr />'."\n";
	echo '<small>';
	
	echo '<a href="'.$_SERVER['PHP_SELF'].'?frame='.$_GET['frame'].'">'._AT('home').'</a> / ';
	if ($pathext != '') {
		$bits = explode('/', $pathext);
		$bits_path = $bits[0];
		for ($i=0; $i<count($bits)-2; $i++) {
			if ($bits_path != $bits[0]) {
				$bits_path .= '/'.$bits[$i];
			}
			echo '<a href="'.$_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/'.SEP.'frame='.$_GET[frame].'">'.$bits[$i].'</a>'."\n";
			echo ' / ';
		}
		echo $bits[count($bits)-2];
	}

	echo '</small><br />';

	echo '<form name="files" action="" method="get"><table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">'."\n";
	echo '<tr><th class="cat"></th>
			<th class="cat">';
			if($_GET['frame']){
				print_popup_help(AT_HELP_FILEMANAGER1);
			}else{
				print_popup_help(AT_HELP_FILEMANAGER);
			}

			echo '&nbsp;</th>
			<th class="cat"><small>'._AT('name').'</small></th>';
	echo '</tr>'."\n";

	/* if the current directory is a sub directory show a back link to get back to the previous directory */
	if($pathext) {
		echo '<tr><td class="row1" colspan="5"><a href="'.$_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$pathext.SEP.'frame='.$_GET['frame'].'"><img src="images/arrowicon.gif" border="0" height="" width="" class="menuimage13" alt="" /> '._AT('back').'</a></td></tr>'."\n";

		echo '<tr><td height="1" class="row2" colspan="5"></td></tr>'."\n";
	}

	$totalBytes = 0;

	/* Display list of files/directories */
	while (false !== ($file = readdir($dir)) ) {

		/* if the name is not a directory */
		if( ($file == '.') || ($file == '..') ) {
			continue;
		}

		/* get some info about the file */
		$filedata = stat($current_path.$pathext.'/'.$file);

		/* create some html for a link to delete files */
		$deletelink = '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$file.SEP.'pathext='.$pathext.SEP.'frame='.$_GET['frame'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete').'" height="16" width="15" class="menuimage4s" /></a>'."\n";

		/* if it is a directory change the file name to a directory link */
		$path_parts = pathinfo($file);
		$ext = $path_parts['extension'];

		$is_dir = false;

		if(is_dir($current_path.$pathext.$file)) {
			$filename = '<small><a href="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext.$file.'/').SEP.'frame='.$_GET['frame'].'">'.$file.'</a></small>'."\n";
			$fileicon = '<small>&nbsp;<img src="images/folder.gif" alt="'._AT('folder').'" height="18" width="20"  class="menuimage4" />&nbsp;</small>'."\n";
			if(!$MakeDirOn) {
				$deletelink = '';
			}

			$is_dir = true;
		} else if ($ext == 'zip') {

			$totalBytes += $filedata[7];
			$filename = $file;
			$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').'" height="16" width="16" border="0" class="menuimage4s" />&nbsp;'."\n";

		} else {
			$totalBytes += $filedata[7];
			$filename = $file;
			$fileicon = '<small>&nbsp;<img src="images/icon_minipost.gif" alt="'._AT('file').'" height="11" width="16"  class="menuimage5" />&nbsp;</small>'."\n";
		}

		if ($is_dir) {
			$dirs[strtolower($file)] .= '<tr><td class="row1"></td>
				<td class="row1" align="center"><small>'.$fileicon.'</small></td>
				<td class="row1"><small>&nbsp;<a href="'.$pathext.urlencode($filename).'">'.$filename.'</a>&nbsp;</small></td>'."\n";

				$dirs[strtolower($file)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="5"></td>
				</tr>'."\n";
		} else {
			$files[strtolower($file)] .= '<tr>
				<td class="row1"><input type="radio" name="f_href" id="f_href" value="'.$_base_href.'content/'.$_SESSION['course_id'].'/'.$pathext.urlencode($filename).'" /></td>
				<td class="row1" align="center"><small>'.$fileicon.'</small></td>
				<td class="row1"><small>&nbsp;<a href="content/'.$_SESSION['course_id'].'/'.$pathext.urlencode($filename).'">'.$filename.'</a>'."\n";

				$files[strtolower($file)] .= '&nbsp;</small></td>'."\n";

				$files[strtolower($file)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="5"></td>
				</tr>'."\n";
		}
	}

	if (is_array($dirs)) {
		ksort($dirs, SORT_STRING);
		foreach($dirs as $x => $y) {
			echo $y;
		}
	}

	if (is_array($files)) {
		ksort($files, SORT_STRING);
		foreach($files as $x => $y) {
			echo $y;
		}
	}

	echo '</table>'."\n";
	closedir($dir);


	echo '<br /><div id="buttons"><button type="button" name="import" class="button" onclick="return onOK();">Import</button> &nbsp; <button type="button" name="cancel" class="button" onclick="return onCancel();">Cancel</button></div>';
	echo '<br /><br /></form>';

?>

</body>
</html>