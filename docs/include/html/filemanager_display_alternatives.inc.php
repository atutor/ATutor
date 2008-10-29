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
//require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

global $db;


//from tools/filemanager/index.php
if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) && 
	(!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
	$popup = TRUE;
	$framed = FALSE;
} else if (isset($_REQUEST['framed']) && $_REQUEST['framed'] &&
	 isset($_REQUEST['popup']) && $_REQUEST['popup']) {
	$popup = TRUE;
	$framed = TRUE;
} else {
	$popup = FALSE;
	$framed = FALSE;
}
// end tools/filemanager/index.php


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
//	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).SEP. 'popup='.$popup.SEP. 'tab='.$current_tab.'" enctype="multipart/form-data">';

//	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?cid='.$cid.'" enctype="multipart/form-data">';

	
//	echo 'ecco';
//	echo '<input type="hidden" name="body_text" value="'.htmlspecialchars($stripslashes($_POST['body_text'])).'" />';
	
	echo '<fieldset><legend class="group_form">'._AT('add').'</legend>';
	echo '<table cellspacing="1" cellpadding="0" border="0" summary="" align="center">';
	echo '<tr><td colspan="2">';


if( $MakeDirOn ) {
		if ($depth < $MaxDirDepth) {
			echo '<input type="text" name="dirname" size="20" /> ';
			echo '<input type="hidden" name="mkdir_value" value="true" /> ';
			echo '<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" />';
			echo '&nbsp;<small class="spacer">'._AT('keep_it_short').'</small>';
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
		echo '<input type="file" name="uploadedfile" id="uploadedfile" class="formfield" size="20" />';
		echo '<input type="submit" name="upload" value="'._AT('upload').'" class="button" />';
		//onClick="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');"
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />  ';
		echo '<input type="hidden" name="alter" value="TRUE" />  ';

		if ($popup == TRUE) {
			echo '<input type="hidden" name="popup" value="1" />';
		}
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
		<?php echo '<input class="button" type="submit" name="add" value="'._AT('add').'" class="button"/>';?>
		
	</td>
</tr>
</tfoot>

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
		$filename = '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$cid.SEP.'pathext='.urlencode($pathext.$file.'/').SEP.'popup='.$popup.SEP.'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'tab='.$current_tab.SEP.'alternatives='.$_POST['alternatives'].'">'.$file.'</a>';
		$fileicon = '&nbsp;';
		$fileicon .= '<img src="'.$_base_href.'images/folder.gif" alt="'._AT('folder').':'.$file.'" height="18" width="20" class="img-size-fm1" />'."\n";
		$fileicon .= '&nbsp;';
		if(!$MakeDirOn) {
			$deletelink = '';
		}

		$is_dir = true;
	} else if ($ext == 'zip') {

		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="'.$_base_href.'images/icon-zip.gif" alt="'._AT('zip_archive').':'.$file.'" height="16" width="16" border="0" class="img-size-fm2" />&nbsp;'."\n";

	} else {
		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="'.$_base_href.'images/file_types/'.get_file_type_icon($filename).'.gif" height="16" width="16" alt="" title="" class="img-size-fm2" />&nbsp;'."\n";
	} 
	$file1 = strtolower($file);
	// create listing for dirctor or file
	if ($is_dir) {
		
		$dirs[$file1] .= '<tr><td>&nbsp;</td>'."\n";
		$dirs[$file1] .= '<td>'.$fileicon.'</td>'."\n";
		$dirs[$file1] .= '&nbsp;';
		$dirs[$file1] .= '<td>'.$filename.'</td>'."\n";
		$dirs[$file1] .= '</tr>'."\n";
	
	} else {
		
	//	$files[$file1] .= '<tr>';
		$files[$file1] .= '<tr> <td  align="center">';
		$files[$file1] .= '<input type="radio" id="'.$pathext.$file.'" value="'.$pathext.$file.'" name="radio_alt"/> </td>'."\n";
		$files[$file1] .= '<td align="center">'.$fileicon.'</td>'."\n";

		$files[$file1] .= '<td ><label for="'.$file.'">&nbsp;';

		$files[$file1] .= '<a href="'.$get_file.$pathext.urlencode($filename).'">'.$file.'</a>';

		
/*		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="tools/filemanager/zip.php?pathext=' . urlencode($pathext) . SEP . 'file=' . urlencode($file) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed .'">'."\n";
			$files[$file1] .= '<img src="'.$_base_href.'images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="img-size-fm3" />'."\n";
			$files[$file1] .= '</a>';
		}

		if (in_array($ext, $editable_file_types)) {
			$files[$file1] .= ' <a href="tools/filemanager/edit.php?pathext=' . urlencode($pathext) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'file=' . $file . '">'."\n";
			$files[$file1] .= '<img src="'.$_base_href.'images/edit.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('edit').'" height="15" width="18" class="img-size-fm4" />'."\n";
			$files[$file1] .= '</a>';
		}
*/
		$files[$file1] .= '&nbsp;</label></td></tr>'."\n";
		
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


echo '</table>'."\n";
//echo '</form>';

closedir($dir);

?>
<script type="text/javascript">
//<!--

function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
//-->
</script>
	
