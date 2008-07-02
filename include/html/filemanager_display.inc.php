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
// $Id: filemanager.php 5078 2005-07-06 14:16:53Z joel $

if (!defined('AT_INCLUDE_PATH')) { exit; }

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
	echo '<a href="'.$_SERVER['PHP_SELF'].'?popup=' . $popup . SEP . 'framed=' . $framed.'">'._AT('home').'</a> ';
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
	echo '<fieldset class="group_form"><legend class="group_form">'._AT('add').'</legend>';
	echo '<table cellspacing="1" cellpadding="0" border="0" summary="" align="center">';
	echo '<tr><td colspan="2">';
	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).SEP. 'popup='.$popup.'">';
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
	echo '</form></td></tr>';

	$my_MaxCourseSize = $system_courses[$_SESSION['course_id']]['max_quota'];

	// upload file 
	if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) 
		|| (($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) && ($course_total < $MaxCourseSize))
		|| ($my_MaxCourseSize-$course_total > 0)) {
		echo '<tr><td  colspan="1">';
		echo '<form onsubmit="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');" name="form1" method="post" action="tools/filemanager/upload.php?popup='.$popup.'" enctype="multipart/form-data">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
		echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
		echo '<input type="submit" name="submit" value="'._AT('upload').'" class="button" />';
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />  ';
		echo _AT('or'); 
		echo ' <a href="tools/filemanager/new.php?pathext=' . urlencode($pathext) . SEP . 'framed=' . $framed . SEP . 'popup=' . $popup . '">' . _AT('file_manager_new') . '</a>';

		if ($popup == TRUE) {
			echo '<input type="hidden" name="popup" value="1" />';
		}
		echo '</form>';
		echo '</td></tr></table></fieldset>';

	} else {
		echo '</table>';
		echo '</fieldset>';
		$msg->printInfos('OVER_QUOTA');
	}
	echo '<br />';
}
// Directory and File listing 

echo '<form name="checkform" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).SEP.'popup='.$popup .SEP. 'framed='.$framed.'" method="post">';
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />';
?>
<table class="data static" summary="" border="0" rules="groups" style="width: 90%">
<thead>
<tr>
	<th scope="col"><input type="checkbox" name="checkall" onclick="Checkall(checkform);" id="selectall" title="<?php echo _AT('select_all'); ?>" /></th>
	<th>&nbsp;</th>
	<th scope="col"><?php echo _AT('name');   ?></th>
	<th scope="col"><?php echo _AT('date');   ?></th>
	<th scope="col"><?php echo _AT('size');   ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5"><input type="submit" name="rename" value="<?php echo _AT('rename'); ?>" /> 
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
		<input type="submit" name="move"   value="<?php echo _AT('move'); ?>" /></td>
</tr>
<tr>
	<td colspan="4" align="right"><strong><?php echo _AT('directory_total'); ?>:</strong></td>
	<td align="right">&nbsp;<strong><?php echo get_human_size(dirsize($current_path.$pathext.$file.'/')); ?></strong>&nbsp;</td>
</tr>
<tr>
	<td colspan="4" align="right"><strong><?php echo _AT('course_total'); ?>:</strong></td>
	<td align="right">&nbsp;<strong><?php echo get_human_size($course_total); ?></strong>&nbsp;</td>
</tr>
<tr>
	<td colspan="4" align="right"><strong><?php echo _AT('course_available'); ?>:</strong></td>
	<td align="right"><strong><?php
		if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
			echo _AT('unlimited');
		} else if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
			echo get_human_size($MaxCourseSize-$course_total);
		} else {
			echo get_human_size($my_MaxCourseSize-$course_total);
		} ?></strong>&nbsp;</td>
</tr>
</tfoot>
<?php if($pathext) : ?>
	<tr>
		<td colspan="5"><a href="<?php echo $_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$pathext.SEP. 'popup=' . $popup .SEP. 'framed=' . $framed .SEP.'cp='.$_GET['cp']; ?>"><img src="images/arrowicon.gif" border="0" height="11" width="10" alt="" /> <?php echo _AT('back'); ?></a></td>
	</tr>
<?php endif; ?>
<?php
$totalBytes = 0;

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
		$filename = '<a href="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext.$file.'/'). SEP . 'popup=' . $popup . SEP . 'framed='. $framed . SEP.'cp='.$_GET['cp'].'">'.$file.'</a>';
		$fileicon = '&nbsp;';
		$fileicon .= '<img src="images/folder.gif" alt="'._AT('folder').':'.$file.'" height="18" width="20" class="img-size-fm1" />';
		$fileicon .= '&nbsp;';
		if(!$MakeDirOn) {
			$deletelink = '';
		}

		$is_dir = true;
	} else if ($ext == 'zip') {

		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').':'.$file.'" height="16" width="16" border="0" class="img-size-fm2" />&nbsp;';

	} else {
		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="images/file_types/'.get_file_type_icon($filename).'.gif" height="16" width="16" alt="" title="" class="img-size-fm2" />&nbsp;';
	} 
	$file1 = strtolower($file);
	// create listing for dirctor or file
	if ($is_dir) {
		
		$dirs[$file1] .= '<tr><td  align="center" width="0%">';
		$dirs[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/></td>';
		$dirs[$file1] .= '<td  align="center"><label for="'.$file.'" >'.$fileicon.'</label></td>';
		$dirs[$file1] .= '<td >&nbsp;';
		$dirs[$file1] .= $filename.'</td>';

		$dirs[$file1] .= '<td  align="right">&nbsp;';
		$dirs[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$dirs[$file1] .= '&nbsp;</td>';

		$dirs[$file1] .= '<td  align="right">';
		$dirs[$file1] .= get_human_size($size).'</td>';
		
	} else {
		$files[$file1] .= '<tr> <td  align="center">';
		$files[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/> </td>';
		$files[$file1] .= '<td  align="center"><label for="'.$file.'">'.$fileicon.'</label></td>';
		$files[$file1] .= '<td >&nbsp;';

		if ($framed) {
			$files[$file1] .= '<a href="'.$get_file.$pathext.urlencode($filename).'">'.$filename.'</a>';
		} else {
			$files[$file1] .= '<a href="tools/filemanager/preview.php?file='.$pathext.$filename.SEP.'pathext='.urlencode($pathext).SEP.'popup='.$popup.'">'.$filename.'</a>';
		}

		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="tools/filemanager/zip.php?pathext=' . urlencode($pathext) . SEP . 'file=' . urlencode($file) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed .'">';
			$files[$file1] .= '<img src="images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="img-size-fm3" />';
			$files[$file1] .= '</a>';
		}

		if (in_array($ext, $editable_file_types)) {
			$files[$file1] .= ' <a href="tools/filemanager/edit.php?pathext=' . urlencode($pathext) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'file=' . $file . '">';
			$files[$file1] .= '<img src="images/edit.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('edit').'" height="15" width="18" class="img-size-fm4" />';
			$files[$file1] .= '</a>';
		}

		$files[$file1] .= '&nbsp;</td>';

		$files[$file1] .= '<td  align="right" style="white-space:nowrap">';

		if ($popup == TRUE) {
			$files[$file1] .= '<input class="button" type="button" name="insert" value="' ._AT('insert') . '" onclick="javascript:insertFile(\'' . $file . '\', \'' . get_relative_path($_GET['cp'], $pathext) . '\', \'' . $ext . '\');" />&nbsp;';
		}

		$files[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$files[$file1] .= '&nbsp;</td>';
		
		$files[$file1] .= '<td  align="right" style="white-space:nowrap">';
		$files[$file1] .= get_human_size($filedata[7]).'</td>';
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


echo '</table></form>';
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