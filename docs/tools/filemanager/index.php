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

define('AT_INCLUDE_PATH', '../../include/');
$_ignore_page = true; /* used for the close the page option */
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (!$_GET['f']) {
	$_SESSION['done'] = 0;
}
session_write_close();
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/index.php';

authenticate(AT_PRIV_FILES);

$msg->addHelp('FILEMANAGER2');
$msg->addHelp('FILEMANAGER3');
$msg->addHelp('FILEMANAGER4');

$_header_file = 'header.inc.php';
$_footer_file = 'footer.inc.php';


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
	$msg->printErrors('UNKNOWN');	
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
		$_section[$start_at][1] = 'tools/filemanager/index.php?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/';

		$start_at++;
	}
	$_section[$start_at][0] = $bits[count($bits)-2];
}

/* if upload successful, close the window */
if ($f) {
	$onload = 'onbeforeload="closeWindow(\'progWin\');"';
}

require(AT_INCLUDE_PATH.$_header_file);

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
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

/* make new directory */
if ($_POST['mkdir_value'] && ($depth < $MaxDirDepth) ) {
	$_POST['dirname'] = trim($_POST['dirname']);
	$_POST['dirname'] = str_replace(' ', '_', $_POST['dirname']);

	/* anything else should be okay, since we're on *nix.. hopefully */
	$_POST['dirname'] = ereg_replace('[^a-zA-Z0-9._]', '', $_POST['dirname']);

	$result = @mkdir($current_path.'/'.$pathext.$_POST['dirname'], 0700);
	if($result == 0) {
		$msg->printErrors('FOLDER_NOT_CREATED');
	}
}



$newpath = substr($current_path.$pathext, 0, -1);

/* open the directory */
if (!($dir = opendir($newpath))) {
	if (isset($_GET['create']) && ($newpath.'/' == $current_path)) {
		@mkdir($newpath);
		if (!($dir = @opendir($newpath))) {
			$msg->printErrors('CANNOT_CREATE_DIR');
			
			require(AT_INCLUDE_PATH.$_footer_file);
			exit;
		} else {
			$msg->addFeedback('CONTENT_DIR_CREATED');
		}
	} else {
		$msg->printErrors('CANNOT_OPEN_DIR');
		require(AT_INCLUDE_PATH.$_footer_file);
		exit;
	}
}



if (isset($_GET['overwrite'])) {
	// get file name, out of the full path
	$path_parts = pathinfo($current_path.$_GET['overwrite']);

	if (!file_exists($path_parts['dirname'].'/'.$pathext.$path_parts['basename'])
		|| !file_exists($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5))) {
		/* source and/or destination does not exist */
		$msg->addError('CANNOT_OVERWRITE_FILE');
	} else {
		@unlink($path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));
		$result = @rename($path_parts['dirname'].'/'.$pathext.$path_parts['basename'], $path_parts['dirname'].'/'.$pathext.substr($path_parts['basename'], 5));

		if ($result) {
			$msg->addFeedback('FILE_OVERWRITE');
		} else {
			$msg->addError('CANNOT_OVERWRITE_FILE');
		}
	}
}
if (isset($_POST['copyfile']) || isset($_POST['deletefiles'])  || 
	isset($_POST['movefilesub']) || isset($_POST['copyfilesub'])) {
		$msg->addError('NO_FILE_SELECT');
}
if (isset($_POST['editfile']) || isset($_POST['renamefile'])) {
	$msg->addError('SELECT_ONE_FILE');
}

$msg->printAll();
 

/* get the course total in Bytes */
$course_total = dirsize($current_path);
/* current path */
//echo '<hr />'."\n";
echo '<p />';
	echo '<p>'._AT('current_path').' ';
echo '<small>';
echo '<a href="'.$_SERVER['PHP_SELF'].'">'._AT('home').'</a> / ';
if ($pathext != '') {
	$bits = explode('/', $pathext);
	$bits_path = $bits[0];
	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		echo '<a href="'. $_SERVER['PHP_SELF'] .'?back=1'. SEP .'pathext='. $bits_path .'/'. $bits[$i+1] .'/">'.$bits[$i].'</a>'."\n";
		echo ' / ';
	}
	echo $bits[count($bits)-2];
}
echo '</small>'."\n";

$totalcol = 5;
$labelcol = 3;
$rowline = '<td height="1" class="row2" colspan="'.$totalcol.'">';
$buttons = '<td colspan="'.$totalcol.'" class="row1"> <input type="submit" onClick="setAction(checkform,0)" name="newfile" value='. _AT('new_file') .' class="button" /><input type="submit" onClick="setAction(checkform,1)" name="editfile" value='. _AT('edit') .' class="button" />&nbsp;<input type="submit" onClick="setAction(checkform,2)" name="copyfile" value='. _AT('copy') .' class="button" /><input type="submit" onClick="setAction(checkform,3)" name="renamefile" value='. _AT('rename') .' class="button" /><input type="submit" onClick="setAction(checkform,4)" name="deletefiles" value='. _AT('delete') .' class="button" />&nbsp;<font face=arial size=-2>'. _AT('selected_files') .'</font>&nbsp;&nbsp;<nobr><input type="submit" onClick="setAction(checkform,5)" name="movefilesub" value='. _AT('move') .' class="button" /><input type="submit" onClick="setAction(checkform,6)" name="copyfilesub" value='. _AT('copy') .' class="button" />&nbsp;<font face=arial size=-2>'. _AT('files_to_subdir') .'</font></nobr></td>';

/* filemanager listing table*/
/* make new directory */
echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">'."\n";
echo '<tr><td colspan="'.$totalcol.'" class="row1">';
echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'">'."\n";
if( $MakeDirOn ) {
	if ($depth < $MaxDirDepth) {
		echo '<input type="text" name="dirname" size="20" class="formfield" /> '."\n";
		echo '<input type="hidden" name="mkdir_value" value="true" /> '."\n";
		echo '<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" />&nbsp;<small class="spacer">'._AT('keep_it_short').'</small>'."\n";
	} else {
		echo _AT('depth_reached');
	}
}
echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
echo '</form>'."\n";
echo '<tr>'. $rowline .'</td></tr>'."\n";
echo '</td></tr>';
/* upload file */
if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) || ($my_MaxCourseSize-$course_total > 0)) {
echo '<tr><td colspan="'.$totalcol.'" class="row1">';
	echo '<form onsubmit="openWindow(\''.$_base_href.'tools/prog.php\');" name="form1" method="post" action="tools/upload.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
	echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
	echo ' <input type="submit" name="submit" value="'._AT('upload').'" class="button" />';
	echo '<input type="hidden" name="pathext" value="'.$pathext.'" />';
	echo '</form>';
echo '</td></tr>';

} else {
	$msg->addInfo('OVER_QUOTA');
}

echo '<tr>'. $rowline .'</td></tr>'."\n";
echo '<tr>'. $rowline .'</td></tr>'."\n";

/* Directory and File listing */
echo '<form name="checkform" action="" method="post">'."\n";
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />'."\n";

echo '<tr>'. $buttons .'</tr>'."\n";
// headings 
echo '<tr><th class="cat"></th><th class="cat">';			
print_popup_help('FILEMANAGER');
echo '&nbsp;</th>';
echo '<th class="cat" scope="col"><small>'._AT('name').'</small></th>';
echo '<th class="cat" scope="col"><small>'._AT('size').'</small></th>';
echo '<th class="cat" scope="col"><small>'._AT('date').'</small></th>';

echo '</tr>'."\n";

// if the current directory is a sub directory show a back link to get back to the previous directory
if($pathext) {
	echo '<tr><td class="row1" colspan="'.$totalcol.'"><a href="'.$_SERVER['PHP_SELF'].'?back=1'. SEP .'pathext='. $pathext.'"><img src="images/arrowicon.gif" border="0" height="" width="" class="menuimage13" alt="" /> '. _AT('back') .'</a></td></tr>'."\n";
	echo '<tr>'. $rowline .'</td></tr>'."\n";
} 

$totalBytes = 0;
$id = 0;
// loop through folder to get files and directory listing
while (false !== ($file = readdir($dir)) ) {

	/* if the name is not a directory */
	if( ($file == '.') || ($file == '..') ) {
		continue;
	}

	// get some info about the file
	$filedata = stat($current_path.$pathext.'/'.$file);
	$path_parts = pathinfo($file);
	$ext = $path_parts['extension'];

	$is_dir = false;

	// if it is a directory change the file name to a directory link 
	if(is_dir($current_path.$pathext.$file)) {
		$size = dirsize($current_path.$pathext.$file.'/');
		$totalBytes += $size;
		$filename = '<label for="'.$id.'" ><small><a href="'. $_SERVER['PHP_SELF'] .'?pathext='. urlencode($pathext.$file.'/').'">'. $file .'</a></small></label>';
		$fileicon = '<small>&nbsp;<img src="images/folder.gif" alt="'. _AT('folder') .'" height="18" width="20"  class="menuimage4" />&nbsp;</small>';
		if(!$MakeDirOn) {
			$deletelink = '';
		}

		$is_dir = true;
	} else if ($ext == 'zip') {

		$totalBytes += $filedata[7];
		$filename = '<label for="'.$id.'" >'.$file.'</label>';
		$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'. _AT('zip_archive') .'" height="16" width="16" border="0" class="menuimage4s" />&nbsp;';

	} else {
		$totalBytes += $filedata[7];
		$filename = '<label for="'.$id.'" >'.$file.'</label>';
		$fileicon = '<small>&nbsp;<img src="images/icon_minipost.gif" alt="'. _AT('file') .'" height="11" width="16"  class="menuimage5" />&nbsp;</small>';
	}
	// create listing for dirctor or file
	if ($is_dir) {
		 
		$dirs[strtolower($file)] .= '<tr><td class="row1" align="center"><input type="checkbox" id="'.$id.'" value="'. $file .'" name="check[]"/> </td>
			<td class="row1" align="center"><small>'. $fileicon .'</small></td>
			<td class="row1"><small>&nbsp;<a href="'. $pathext.urlencode($filename) .'">'. $filename .'</a>&nbsp;</small></td>'."\n";

			
			$dirs[strtolower($file)] .= '<td class="row1" align="right"><small>'. number_format($size/AT_KBYTE_SIZE, 2) .' KB&nbsp;</small></td>';
			$dirs[strtolower($file)] .= '<td class="row1"><small>&nbsp;';

			$dirs[strtolower($file)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);

			$dirs[strtolower($file)] .= '&nbsp;</small></td>';
			

			$dirs[strtolower($file)] .= '</tr><tr><td height="1" class="row2" colspan="'.$totalcol.'"></td></tr>';
			$dirs[strtolower($file)] .= "\n";
	} else {
		$files[strtolower($file)] .= '<tr> <td class="row1" align="center"> <input type="checkbox" id="'.$id.'" value="'. $file .'" name="check[]"/> </td>
			<td class="row1" align="center"><small>'. $fileicon .'</small></td>
			<td class="row1"><small>&nbsp;<a href="get.php/'. $pathext.urlencode($filename) .'">'. $filename.'</a>';

			if ($ext == 'zip') {
				$files[strtolower($file)] .= ' <a href="tools/zip.php?pathext='. $pathext.$file.'">';
				$files[strtolower($file)] .= '<img src="images/archive.gif" border="0" alt="'. _AT('extract_archive') .'" title="'. _AT('extract_archive') .'"height="16" width="11" class="menuimage6s" />';
				$files[strtolower($file)] .= '</a>';
			}
			$files[strtolower($file)] .= '&nbsp;</small></td>';

			
			$files[strtolower($file)] .= '<td class="row1" align="right"><small>'. number_format($filedata[7]/AT_KBYTE_SIZE, 2) .' KB&nbsp;</small></td>';
			$files[strtolower($file)] .= '<td class="row1"><small>&nbsp;';
			
			$files[strtolower($file)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);

			$files[strtolower($file)] .= '&nbsp;</small></td>';
			
			$files[strtolower($file)] .= '</tr><tr><td height="1" class="row2" colspan="'.$totalcol.'"></td></tr>';
			$files[strtolower($file)] .= "\n";
	}
} // end while

// sort listing and output directoies
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

echo '<tr> <td class="row1" colspan="'. $labelcol .'"><input type="checkbox" name="checkall" onClick="Checkall(checkform);" /> <small>'. _AT('select_all') .' </small></td><td class="row1" colspan="2"> </td></tr>'."\n";
echo '<tr>'. $rowline .'</td></tr>'."\n";

echo '<tr> '.$buttons.'</tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";


echo '<tr><td class="row1" colspan="'. $labelcol .'" align="right"><small><b>'. _AT('directory_total') .':</b><br /><br /></small></td><td align="right" class="row1"><small>&nbsp;<b>'. number_format($totalBytes/AT_KBYTE_SIZE, 2) .'</b> KB&nbsp;<br /><br /></small></td><td class="row1" colspan="1"><small>&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'.$labelcol.'" align="right"><small><b>'. _AT('course_total') .':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>'. number_format($course_total/AT_KBYTE_SIZE, 2) .'</b> KB&nbsp;</small></td><td class="row1" colspan="1"><small>&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'. $labelcol .'" align="right"><small><b>'. _AT('course_available') .':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>'."\n";
if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
	echo _AT('unlimited');
} else {
	echo number_format(($my_MaxCourseSize-$course_total)/AT_KBYTE_SIZE, 2);
}
echo '</b> KB&nbsp;</small></td><td class="row1" colspan="1"><small>&nbsp;</small></td></tr>'."\n";

echo '</table></form>'."\n";
closedir($dir);

?>
<script type="text/javascript">
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
function setAction(form,target){
	if (target == 0) form.action="tools/filemanager/file_manager_new.php";
	else if ((target == 1) && (target == 3)) {
		var checked = 0;
		for (var i = 0; i < form.elements.length; i++) {
			e = form.elements[i];
			if ((e.name=="check[]") && (e.type=="checkbox") && e.checked) { 
				checked++;
			}
		}
		if (checked > 1) {
			form.action = "tools/filemanger/index.php";
		} else {
			if (target == 1) form.action="tools/filemanager/file_manager_edit.php";
			if (target == 3) form.action="tools/filemanager/file_manager_rename.php";
		}
	} else {
		var checked = false;
		for (var i = 0; i <form.elements.length; i++) {
			e = form.elements[i];
			if ((e.name=="check[]") && (e.type=="checkbox") && e.checked) { 
					checked = true;
					break;
			}
		}
		if (checked) {	
			if (target == 2) form.action="tools/filemanager/file_manager_copy.php";
			if (target == 4) form.action="tools/filemanager/file_manager_delete.php"; 
			if (target == 5) form.action="tools/filemanager/file_manager_movesub.php";
			if (target == 6) form.action="tools/filemanager/file_manager_copysub.php";
		} else form.action="tools/filemanager/index.php";
	}
} 

</script>
<?php
	require(AT_INCLUDE_PATH.$_footer_file);
?>