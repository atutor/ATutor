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

$page = 'file_manager2';

define('AT_INCLUDE_PATH', '../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager2');
$_section[1][1] = 'tools/file_manager2.php';

authenticate(AT_PRIV_FILES);

$help[]=AT_HELP_FILEMANAGER2;
$help[]=AT_HELP_FILEMANAGER3;
$help[]=AT_HELP_FILEMANAGER4;

if ($_GET['frame'] == 1) {
	$_header_file = 'html/frameset/header.inc.php';
	$_footer_file = 'html/frameset/footer.inc.php';
} else {
	$_header_file = 'header.inc.php';
	$_footer_file = 'footer.inc.php';
}


$start_at = 2;

	$current_path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';
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

require(AT_INCLUDE_PATH.$_header_file);

if ($_GET['frame']) {
	echo '<table width="100%" cellpadding="0" cellspacing="0"><tr><td class="cat2"></td></tr></table>'."\n";
	echo '<div align="center"><small>(<a href="close_frame.php" target="_top">'._AT('close_frame').'</a>)</small></div>'."\n";
}


	if($_GET['frame'] == 1){
		echo '<h2>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo ' <a href="tools/" class="hide" target="content">'._AT('tools').'</a>';
		}
		echo '</h2>'."\n";
	}else{
		echo '<h2>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
		}
		echo '</h2>'."\n";
	}


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
			unset($errors);
		}
	}


	if ($_GET['delete'] && !$_GET['d']) {
		/* check that at least one checkbox checked */


		if(is_dir($current_path.$pathext.$_GET['delete'])) {
			$warnings[]=array(AT_WARNING_CONFIRM_DIR_DELETE, $_GET['delete']);
		} else {
			$warnings[]=array(AT_WARNING_CONFIRM_FILE_DELETE, $_GET['delete']);
		}
		print_warnings($warnings);
		echo '<p><a href="tools/file_manager.php?delete='.urlencode($_GET['delete']).SEP.'pathext='.$_GET['pathext'].SEP.'d=1'.SEP.'frame='.$_GET['frame'].'">'._AT('yes_delete').'</a>, <a href="tools/file_manager.php?pathext='.$_GET['pathext'].SEP.'frame='.$_GET['frame'].'">'._AT('no_cancel').'</a></p>'."\n";
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	} else if (isset($_GET['rename'])) {
		/* check that one checkbox checked */


		echo '<h3>'._AT('rename_file_dir').'</h3>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="get"><p>';
		echo '<input type="hidden" name="frame" value="'.$_GET['frame'].'" />';
		echo '<input type="hidden" name="pathext" value="'.$_GET['pathext'].'" />';
		echo '<input type="hidden" name="old_name" value="'.urlencode($_GET['rename']).'" />';

		echo $_GET['pathext'] . '<input type="text" name="new_name" value="'.$_GET['rename'].'" class="formfield" size="30" /> ';
		echo '<input type="submit" name="rename_action" value="'._AT('rename').'" class="button" />';
		echo ' - <input type="submit" name="cancel" value="'._AT('cancel').'" class="button" />';
		echo '</p></form>';
		echo '<hr />';

	} else if (isset($_GET['rename_action'])) {
		$_GET['new_name'] = trim($_GET['new_name']);
		$_GET['new_name'] = str_replace(' ', '_', $_GET['new_name']);
		$_GET['new_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_GET['new_name']);

		$_GET['old_name'] = trim($_GET['old_name']);
		$_GET['old_name'] = str_replace(' ', '_', $_GET['old_name']);
		$_GET['old_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_GET['old_name']);

		if (file_exists($current_path.$pathext.$_GET['new_name']) || !file_exists($current_path.$pathext.$_GET['old_name'])) {
			$errors[] = AT_ERROR_CANNOT_RENAME;
			print_errors($errors);
			unset($errors);
		} else {
			@rename($current_path.$pathext.$_GET['old_name'], $current_path.$pathext.$_GET['new_name']);
			print_feedback(AT_FEEDBACK_RENAMED);
		}
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
		/* check that at least one checkbox checked */
		
		if(is_dir($current_path.$pathext.$_GET['delete'])) {
			
			if (strpos($_GET['delete'], '..') !== false) {
				$errors[] = AT_ERROR_UNKNOWN;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}

			if (!is_dir($current_path.$pathext.$_GET['delete'])) {
				$errors[]=AT_ERROR_DIR_NOT_DELETED;
				print_errors($errors);
				require(AT_INCLUDE_PATH.$_footer_file);
				exit;
			}

			$result = clr_dir($current_path.$pathext.$_GET['delete']);
			if (!$result) {
				$errors[]   = AT_ERROR_DIR_NO_PERMISSION;
			} else {
				$feedback[] = AT_FEEDBACK_DIR_DELETED;
			}
		} else {
			@unlink($current_path.$pathext.$_GET['delete']);
			$feedback[]=AT_FEEDBACK_FILE_DELETED;
		}
	}

	if (isset($_GET['overwrite'])) {
		// get file name, out of the full path
		$path_parts = pathinfo($current_path.$_GET['overwrite']);

		if (!file_exists($path_parts['dirname'].'/'.$pathext.$path_parts['basename'])
			|| !file_exists($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5))) {
			/* source and/or destination does not exist */
			$errors[]	= AT_ERROR_CANNOT_OVERWRITE_FILE;
		} else {
			@unlink($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));
			$result = @rename($path_parts['dirname'].'/'.$pathext.$path_parts['basename'], $path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));

			if ($result) {
				$feedback[] = AT_FEEDBACK_FILE_OVERWRITE;
			} else {
				$errors[]	= AT_ERROR_CANNOT_OVERWRITE_FILE;
			}
		}
	}

	require(AT_INCLUDE_PATH.'html/feedback.inc.php');

	if ($_GET['frame'] != 1) {
		print_help($help);
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
		echo '<form onsubmit="openWindow(\''.$_base_href.'tools/prog.php\');" name="form1" method="post" action="tools/upload.php?frame='.$_GET['frame'].'" enctype="multipart/form-data">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
		echo '<br />';
		echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
		echo ' <input type="submit" name="submit" value="'._AT('upload').'"  />';
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />';
		echo '</form>';
	} else {
		print_infos(AT_INFOS_OVER_QUOTA);
	}
	echo '<hr />'."\n";
	if ($_GET['frame'] != 1) {
		echo '<p><a href="frame.php?p='.urlencode($_my_uri).'">'._AT('open_frame').'</a>.</p>'."\n";
		echo '<p>'._AT('current_path').' ';
	}
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

	echo '</small>';

	echo '</p><br /><form name="checkform"><table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">'."\n";
	echo '<tr> <td colspan="6"> <input type="submit" name="newfile" value='._AT('new').'><input type="submit" name="editfile" value='._AT('edit').'>&nbsp;<input type="submit" name="copyfile" value='._AT('copy').'><input type="submit" name="renamefile" value='._AT('rename').'><input type="submit" name="deletefiles" value='._AT('delete').'><font face=arial size=-2>'._AT('checked_files').'</font>&nbsp;&nbsp;<nobr><input type="submit" name="movefilesub" value='._AT('move').'><input type="submit" name="copyfilesub" value='._AT('copy').'><font face=arial size=-2>'._AT('files_to_subdirectory').'</font></nobr></td></tr>';
	echo '<tr><th class="cat"></th>
			<th class="cat">';
			if($_GET['frame']){
				print_popup_help(AT_HELP_FILEMANAGER1);
			}else{
				print_popup_help(AT_HELP_FILEMANAGER);
			}

			echo '&nbsp;</th>
			<th class="cat"><small>'._AT('name').'</small></th>';
	if ($_GET['frame'] != 1) {
		echo '<th class="cat"><small>'._AT('size').'</small></th>';
		echo '<th class="cat"><small>'._AT('date').'</small></th>';
		echo '<th class="cat"><small>&nbsp;</small></th>';
	}
	echo '</tr>'."\n";

	$rowline = '<td height="1" class="row2" colspan="6">';
	/* if the current directory is a sub directory show a back link to get back to the previous directory */
	if($pathext) {
		echo '<tr><td class="row1" colspan="6"><a href="'.$_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$pathext.SEP.'frame='.$_GET['frame'].'"><img src="images/arrowicon.gif" border="0" height="" width="" class="menuimage13" alt="" /> '._AT('back').'</a></td></tr>'."\n";

		echo '<tr>'.$rowline.'</td></tr>'."\n";
	}

	$totalBytes = 0;
	while (false !== ($file = readdir($dir)) ) {

		/* if the name is not a directory */
		if( ($file == '.') || ($file == '..') ) {
			continue;
		}

		/* get some info about the file */
		$filedata = stat($current_path.$pathext.'/'.$file);

		/* create some html for a link to delete files/directories */
		$deletelink = '<a href="'.$_SERVER['PHP_SELF'].'?delete='.urlencode($file).SEP.'pathext='.$pathext.SEP.'frame='.$_GET['frame'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete').'" height="16" width="15" class="menuimage4s" /></a>';

		$renamelink = '<a href="'.$_SERVER['PHP_SELF'].'?rename='.urlencode($file).SEP.'pathext='.$pathext.SEP.'frame='.$_GET['frame'].'"><img src="images/icon_rename.gif" border="0" alt="'._AT('rename').'" height="16" width="15" class="menuimage4s" /></a>';

		/* if it is a directory change the file name to a directory link */
		$path_parts = pathinfo($file);
		$ext = $path_parts['extension'];

		$is_dir = false;
		
		if(is_dir($current_path.$pathext.$file)) {
			$filename = '<small><a href="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext.$file.'/').SEP.'frame='.$_GET['frame'].'">'.$file.'</a></small>';
			$fileicon = '<small>&nbsp;<img src="images/folder.gif" alt="'._AT('folder').'" height="18" width="20"  class="menuimage4" />&nbsp;</small>';
			if(!$MakeDirOn) {
				$deletelink = '';
			}

			$is_dir = true;
		} else if ($ext == 'zip') {

			$totalBytes += $filedata[7];
			$filename = $file;
			$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').'" height="16" width="16" border="0" class="menuimage4s" />&nbsp;';

		} else {
			$totalBytes += $filedata[7];
			$filename = $file;
			$fileicon = '<small>&nbsp;<img src="images/icon_minipost.gif" alt="'._AT('file').'" height="11" width="16"  class="menuimage5" />&nbsp;</small>';
		}

		if ($is_dir) {
			$dirs[strtolower($file)] .= '<tr><td class="row1" align="center"> <input type="checkbox" /> </td>
				<td class="row1" align="center"><small>'.$fileicon.'</small></td>
				<td class="row1"><small>&nbsp;<a href="'.$pathext.urlencode($filename).'">'.$filename.'</a>&nbsp;</small></td>'."\n";

				if ($_GET['frame'] != 1) {
					$dirs[strtolower($file)] .= '<td class="row1" align="right"><small>'.number_format($filedata[7]/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';
					$dirs[strtolower($file)] .= '<td class="row1"><small>&nbsp;';

					$dirs[strtolower($file)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);

					$dirs[strtolower($file)] .= '&nbsp;</small></td>
					<td class="row1"><small>&nbsp;'.$deletelink.$renamelink.'&nbsp;</small></td>';
				}

				$dirs[strtolower($file)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="6"></td>
				</tr>'."\n";
		} else {
			$files[strtolower($file)] .= '<tr> <td class="row1" align="center"> <input type="checkbox" /> </td>
				<td class="row1" align="center"><small>'.$fileicon.'</small></td>
				<td class="row1"><small>&nbsp;<a href="get.php/'.$pathext.urlencode($filename).'">'.$filename.'</a>';

				if (($ext == 'zip') && (!$_GET['frame'])) {
					$files[strtolower($file)] .= ' <a href="tools/zip.php?pathext='.$pathext.$file.SEP.'frame='.$_GET[frame].'">';
					$files[strtolower($file)] .= '<img src="images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="menuimage6s" />';
					$files[strtolower($file)] .= '</a>';
				}
				$files[strtolower($file)] .= '&nbsp;</small></td>';

				if ($_GET['frame'] != 1) {
					$files[strtolower($file)] .= '<td class="row1" align="right"><small>'.number_format($filedata[7]/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';
					$files[strtolower($file)] .= '<td class="row1"><small>&nbsp;';
					
					$files[strtolower($file)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);

					$files[strtolower($file)] .= '&nbsp;</small></td>
					<td class="row1"><small>&nbsp;'.$deletelink.$renamelink.'&nbsp;</small></td>';
				}
				$files[strtolower($file)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="6"></td>
				</tr>';
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

	if ($_GET['frame'] != 1) {
		echo '<tr> <td class="row1" colspan="6"><input type="checkbox" onClick="Checkall(this.checkform);" /> <small>'._AT('check all').' </small></td></tr>';
		echo '<tr>'.$rowline.'</td></tr>'."\n";

		echo '<tr> <td colspan="6"> <input type="submit" name="newfile" value="New"><input type="submit" name="editfile" value='._AT('edit').'>&nbsp;<input type="submit" name="copyfile" value="Copy"><input type="submit" name="renamefile" value='._AT('rename').'><input type="submit" name="deletefiles" value='._AT('delete').'><font face=arial size=-2>'._AT('checked_files').'</font>&nbsp;&nbsp;<nobr><input type="submit" name="movefilesub" value='._AT('move').'><input type="submit" name="copyfilesub" value="Copy"><font face=arial size=-2>'._AT('files_to_subdirectory').'</font></nobr></td></tr>';

		echo '<tr>'.$rowline.'</td></tr>';
		echo '<tr>'.$rowline.'</td></tr>';

		echo '<tr><td class="row1" colspan="3" align="right"><small><b>'._AT('directory_total').':</b><br /><br /></small></td><td align="right" class="row1"><small>&nbsp;<b>'.number_format($totalBytes/AT_KBYTE_SIZE, 2).'</b> KB&nbsp;<br /><br /></small></td><td class="row1" colspan="2"><small>&nbsp;</small></td></tr>';

		echo '<tr>'.$rowline.'</td></tr>';
		echo '<tr>'.$rowline.'</td></tr>';

		echo '<tr><td class="row1" colspan="3" align="right"><small><b>'._AT('course_total').':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>'.number_format($course_total/AT_KBYTE_SIZE, 2).'</b> KB&nbsp;</small></td><td class="row1" colspan="2"><small>&nbsp;</small></td></tr>';

		echo '<tr>'.$rowline.'</td></tr>';

		echo '<tr><td class="row1" colspan="3" align="right"><small><b>'._AT('course_available').':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>';
		if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
			echo _AT('unlimited');
		} else {
			echo number_format(($my_MaxCourseSize-$course_total)/AT_KBYTE_SIZE, 2);
		}
		echo '</b> KB&nbsp;</small></td><td class="row1" colspan="3"><small>&nbsp;</small></td></tr>';
	}
	echo '</table></form>';
	closedir($dir);

?>
<script type="text/javascript">
function Checkall(form){ 
  for (var i = 0; i < form.elements.length-1; i++){    
    eval("form.elements[" + i + "].checked = form.elements[form.elements.length-1].checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>
<?php
	require(AT_INCLUDE_PATH.$_footer_file);
?>