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

// get the course total in Bytes 
$course_total = dirsize($current_path);

echo '<p>'._AT('current_path').' ';
echo '<small>';
echo '<a href="'.$_SERVER['PHP_SELF'].'">'._AT('home').'</a> / '."\n";

if ($_GET['popup'] == TRUE) {
	$popup = TRUE;
}

if ($pathext == '') {
	$pathext = urlencode($_POST['pathext']);
}

if ($pathext != '') {
	

	//debug($pathext);

	$bits = explode('/', $pathext);
	$bits_path = $bits[0];
	for ($i=0; $i<count($bits)-2; $i++) {
		if ($bits_path != $bits[0]) {
			$bits_path .= '/'.$bits[$i];
		}
		echo '<a href="'.$_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$bits_path.'/'.$bits[$i+1].'/">'.$bits[$i].'</a>'."\n";
		echo ' / ';
	}
	echo $bits[count($bits)-2];
}
echo '</small>'."\n";
echo '</p>'."\n";

if ($popup == TRUE) {
	$totalcol = 6;
} else {
	$totalcol = 5;
}
$labelcol = 3;
$rowline = '<td height="1" class="row2" colspan="'.$totalcol.'">';

//debug(output_dirs($current_path,""," "));

$buttons_top  = '<td colspan="'.$totalcol.'" class="row1">';
$buttons_top .= '<input type="submit" name="edit" value="'._AT('edit').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="rename" value="'._AT('rename').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="delete" value="'._AT('delete').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="move"   value="'._AT('move').'"   class="button" /></td>';

if ($framed != TRUE) {
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
	// filemanager listing table
	// make new directory 
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">'."\n";
	echo '<tr><td class="row1"colspan="2">';
	echo '<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).'">'."\n";
	if( $MakeDirOn ) {
		if ($depth < $MaxDirDepth) {
			echo '<input type="text" name="dirname" size="20" class="formfield" /> '."\n";
			echo '<input type="hidden" name="mkdir_value" value="true" /> '."\n";
			echo '<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" />';
			echo '&nbsp;<small class="spacer">'._AT('keep_it_short').'</small>'."\n";
		} else {
			echo _AT('depth_reached');
		}
	}
	echo '<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
	echo '</form></td></tr>'."\n";
	echo '<tr><td class="row2" height="1" colspan="2"></td></tr>'."\n";

	// upload file 
	if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) || ($my_MaxCourseSize-$course_total > 0)) {
		echo '<tr><td class="row1" colspan="1">';
		echo '<form onsubmit="openWindow(\''.$_base_href.'tools/prog.php\');" name="form1" method="post" action="tools/upload.php?popup='.$popup.'" enctype="multipart/form-data">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
		echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
		echo '<input type="submit" name="submit" value="'._AT('upload').'" class="button" />';
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />  ';
		echo _AT('or'); 
		echo ' <a href="'.$_SERVER['PHP_SELF'].'?action=new'.SEP.'pathext='.urlencode($pathext).'">' . _AT('file_manager_new') . '</a>';
		if ($popup == TRUE) {
			echo '<input type="hidden" name="popup" value="1" />';
		}
		echo '</form>';
		echo '</td></tr>';

	} else {
		$msg->addInfo('OVER_QUOTA');
	}
	
	echo '</table>';
	echo '<p /><p />';
}
// Directory and File listing 
echo '<form name="checkform" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).'" method="post">'."\n";
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />'."\n";
if ($popup == TRUE) {
	echo '<table width="99%"cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">';
}
else {
	echo '<table width="80%"cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">';
}
echo '<tr>'.$buttons_top.'</tr>'."\n";
// headings 
echo '<tr><th width="5%" class="cat" scope="col"><input type="checkbox" name="checkall" onclick="Checkall(checkform);" id="selectall" title="' . _AT('select_all') . '" /></th><th width="5%" class="cat">';
print_popup_help('FILEMANAGER');
echo '&nbsp;</th>';
if ($popup == TRUE) {
	echo '<th width="40%" class="cat" scope="col"><small>'._AT('name').'</small></th>';
	echo '<th width="15%" class="cat" scope="col"><small>'._AT('action').'</small></th>';
	echo '<th width="25%" class="cat" scope="col"><small>'._AT('date').'</small></th>';
	echo '<th width="10%" class="cat" scope="col"><small>'._AT('size').'</small></th>';
}
else {
	echo '<th width="50%" class="cat" scope="col"><small>'._AT('name').'</small></th>';
	echo '<th width="25%" class="cat" scope="col"><small>'._AT('date').'</small></th>';
	echo '<th width="15%" class="cat" scope="col"><small>'._AT('size').'</small></th>';
}


echo '</tr>'."\n";

// if the current directory is a sub directory show a back link to get back to the previous directory
if($pathext) {
	echo '<tr><td class="row1" colspan="'.$totalcol.'">';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$pathext.'">';
	echo '<img src="images/arrowicon.gif" border="0" height="" width="" class="menuimage13" alt="" /> ';
	echo _AT('back').'</a></td></tr>'."\n";
	echo '<tr>'.$rowline.'</td></tr>'."\n";
} 

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
	$ext = $path_parts['extension'];

	$is_dir = false;

	// if it is a directory change the file name to a directory link 
	if(is_dir($current_path.$pathext.$file)) {
		$size = dirsize($current_path.$pathext.$file.'/');
		$totalBytes += $size;
		$filename = '<small><a href="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext.$file.'/').'">'.$file.'</a></small>';
		$fileicon = '<small>&nbsp;';
		$fileicon .= '<img src="images/folder.gif" alt="'._AT('folder').':'.$file.'" height="18" width="20"  class="menuimage4" />';
		$fileicon .= '&nbsp;</small>';
		if(!$MakeDirOn) {
			$deletelink = '';
		}

		$is_dir = true;
	} else if ($ext == 'zip') {

		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').':'.$file.'" height="16" width="16" border="0" class="menuimage4s" />&nbsp;';

	} else {
		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '<small>&nbsp;<img src="images/icon_minipost.gif" alt="'._AT('file').':'.$file.'" height="11" width="16"  class="menuimage5" />&nbsp;</small>';
	} 
	$file1 = strtolower($file);
	// create listing for dirctor or file
	if ($is_dir) {
		
		$dirs[$file1] .= '<tr><td class="row1" align="center">';
		$dirs[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/></td>';
		$dirs[$file1] .= '<td class="row1" align="center"><small><label for="'.$file.'" >'.$fileicon.'</label></small></td>';
		$dirs[$file1] .= '<td class="row1"><small>&nbsp;';
		$dirs[$file1] .= '<a href="'.$pathext.urlencode($filename).'">'.$filename.'</a>&nbsp;</small></td>'."\n";

		if ($popup == TRUE) {
			$dirs[$file1] .= '<td class="row1" align="center">';
			$dirs[$file1] .= '<small>'._AT('na').'</small></td>';
		}
		
		$dirs[$file1] .= '<td class="row1" align="center"><small>&nbsp;';
		$dirs[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$dirs[$file1] .= '&nbsp;</small></td>';

		$dirs[$file1] .= '<td class="row1" align="center"><small>';
		$dirs[$file1] .= number_format($size/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';
		
		$dirs[$file1] .= '</tr>'."\n".'<tr><td height="1" class="row2" colspan="'.$totalcol.'"></td></tr>';
		$dirs[$file1] .= "\n";
	} else {
		$files[$file1] .= '<tr> <td class="row1" align="center">';
		$files[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/> </td>';
		$files[$file1] .= '<td class="row1" align="center"><small><label for="'.$file.'">'.$fileicon.'</label></small></td>';
		$files[$file1] .= '<td class="row1"><small>&nbsp;';
		$files[$file1] .= '<a href="get.php/'.$pathext.urlencode($filename).'">'.$filename.'</a>';

		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="tools/zip.php?pathext='.$pathext.$file.SEP.'popup='.$popup.'">';
			$files[$file1] .= '<img src="images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="menuimage6s" />';
			$files[$file1] .= '</a>';
		}

		if ($ext == 'txt' || $ext == 'html') {
			$files[$file1] .= ' <a href="' . $_SERVER['PHP_SELF'] . '?pathext=' . urlencode($pathext) . SEP . 'action=edit' . SEP . 'file=' . $file . '">';
			$files[$file1] .= '<img src="images/edit.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('edit').'"height="16" width="16" />';
			$files[$file1] .= '</a>';
		}

		$files[$file1] .= '&nbsp;</small></td>';

		if ($popup == TRUE) {
			//debug($current_path);
			$files[$file1] .= '<td class="row1" align="center">';
			$files[$file1] .= '<small><input type="button" name="insert" value="' . _AT('insert') . '" onclick="javascript:insertFile(\'' . $file1 . '\', \'' . $pathext . '\', \'' . $ext . '\');" class="button" /></small></td>';
		}


		$files[$file1] .= '<td class="row1" align="center"><small>&nbsp;';
		$files[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$files[$file1] .= '&nbsp;</small></td>';
		
		$files[$file1] .= '<td class="row1" align="center">';
		$files[$file1] .= '<small>'.number_format($filedata[7]/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';

		$files[$file1] .= '</tr>'."\n".'<tr><td height="1" class="row2" colspan="'.$totalcol.'"></td></tr>';
		$files[$file1] .= "\n";
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

//echo '<tr> <td class="row1" colspan="'.$labelcol.'">';
//echo '<input type="checkbox" name="checkall" onclick="Checkall(checkform);" id="selectall" /><small><label for="selectall">'._AT('select_all').'</label></small>';
//echo '</td><td class="row1" colspan="'.($totalcol-$labelcol).'"></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr> '.$buttons_top.'</tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";

//echo '<td class="row1" colspan="'.($totalcol-$labelcol-1).'"><small>&nbsp;</small></td></tr>'."\n";
echo '<tr><td class="row1" colspan="'.($totalcol-1).'" align="right">'."\n";
echo '<small><strong>'._AT('directory_total').':</strong></small></td>'."\n";
echo '<td align="right" class="row1"><small>&nbsp;<strong>'.number_format($totalBytes/AT_KBYTE_SIZE, 2).'</strong> KB&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'.($totalcol-1).'" align="right"><small><strong>'._AT('course_total').':</strong></small></td>'."\n";
echo '<td align="right" class="row1"><small>&nbsp;<strong>'.number_format($course_total/AT_KBYTE_SIZE, 2).'</strong> KB&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'.($totalcol-1).'" align="right"><small><strong>'._AT('course_available').':</strong></small></td>'."\n";
echo '<td align="right" class="row1"><small><strong>';
if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
	echo _AT('unlimited');
} else {
	echo number_format(($my_MaxCourseSize-$course_total)/AT_KBYTE_SIZE, 2);
}
echo '</strong> KB&nbsp;</small></td></tr>';

echo '</table></form>'."\n";
?>

<script type="text/javascript">
<!--
function insertFile(fileName, pathTo, ext) { 

	if (ext == "gif" || ext == "jpg" || ext == "jpeg" || ext == "png") {
		var info = '<?=_AT('alternate_text')?>';
		var imageString = '<img src="'+ pathTo+fileName + '" alt="'+ info +'" />';

		if (window.parent.editor) {
			if (window.parent.editor._editMode == "textmode") {
				insertAtCursor2(window.parent.document.form.body_text, imageString);
			}
			else {
				window.parent.editor.insertHTML(imageString)
			}
		}
		else if (window.opener.editor) {
			if (window.opener.editor._editMode != "textmode") {
				window.opener.editor.insertHTML(imageString)
			}
			else {
				insertAtCursor(window.opener.document.form.body_text, imageString);
			}
		}
		else {
			insertAtCursor(window.opener.document.form.body_text, imageString);
		}
	}
	
	else {
		var info = '<?=_AT('put_link')?>';
		var fileString  = '<a href="' + pathTo+fileName + '">' + info + '</a>';

		if (window.parent.editor) {
			if (window.parent.editor._editMode == "textmode") {
				insertAtCursor2(window.parent.document.form.body_text, fileString);
			}
			else {
				window.parent.editor.insertHTML(fileString)
			}
		}
		else if (window.opener.editor) {
			if (window.opener.editor._editMode != "textmode") {
				window.opener.editor.insertHTML(fileString)
			}
			else {
				insertAtCursor(window.opener.document.form.body_text, fileString);
			}
		}
		else {
			insertAtCursor(window.opener.document.form.body_text, fileString);
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
	} else {
		myField.value += myValue;
	}
}

function insertAtCursor2(myField, myValue) {
	//IE support
	if (window.parent.document.selection) {
		myField.focus();
		sel = window.parent.document.selection.createRange();
		sel.text = myValue;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
}
-->
</script>