<?php
// get the course total in Bytes 
$course_total = dirsize($current_path);

echo '<p>'._AT('current_path').' ';
echo '<small>';
echo '<a href="'.$_SERVER['PHP_SELF'].'">'._AT('home').'</a> / '."\n";

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

$totalcol = 5;
$labelcol = 3;
$rowline = '<td height="1" class="row2" colspan="'.$totalcol.'">';

$dir_pull_down_top = '<select name="dir_list_top"><option value="" > Home </option> ';
$dir_pull_down_top .= "\n".output_dirs($current_path,""," ").'</select>';

$dir_pull_down_bottom = '<select name="dir_list_bottom"><option value="/" > Home </option> ';
$dir_pull_down_bottom .= "\n".output_dirs($current_path,""," ").'</select>';

$buttons_top = '<td colspan="'.$totalcol.'" class="row1">';
$buttons_top .= '<input type="submit" name="newfile" value="'._AT('new_file').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="editfile" value="'._AT('edit').'" class="button" />&nbsp;'."\n";
$buttons_top .= '<input type="submit" name="copyfile" value="'._AT('copy').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="renamefile" value="'._AT('rename').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="deletefiles" value="'._AT('delete').'" class="button" />';
$buttons_top .= ' <small>'._AT('selected_files').'</small>'."\n".$dir_pull_down_top;
$buttons_top .= '<input type="submit" name="movefilesub" value="'._AT('move').'" class="button" />'."\n";
$buttons_top .= '<input type="submit" name="copyfilesub" value="'._AT('copy').'" class="button" /></td>';

$buttons_bottom = '<td colspan="'.$totalcol.'" class="row1">';
$buttons_bottom .= '<input type="submit" name="newfile" value="'._AT('new_file').'" class="button" />'."\n";
$buttons_bottom .= '<input type="submit" name="editfile" value="'._AT('edit').'" class="button" />&nbsp;'."\n";
$buttons_bottom .= '<input type="submit" name="copyfile" value="'._AT('copy').'" class="button" />'."\n";
$buttons_bottom .= '<input type="submit" name="renamefile" value="'._AT('rename').'" class="button" />'."\n";
$buttons_bottom .= '<input type="submit" name="deletefiles" value="'._AT('delete').'" class="button" />';
$buttons_bottom .= '&nbsp;<small>'._AT('selected_files').'</small>&nbsp;&nbsp;'."\n".$dir_pull_down_bottom;
$buttons_bottom .= '<input type="submit" name="movefilesub" value="'._AT('move').'" class="button" />'."\n";
$buttons_bottom .= '<input type="submit" name="copyfilesub" value="'._AT('copy').'" class="button" /></td>';



// filemanager listing table
// make new directory 
echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">'."\n";
echo '<tr><td class="row1">';
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
echo '<tr><td class="row2" height="1"></td></tr>'."\n";

// upload file 
if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) || ($my_MaxCourseSize-$course_total > 0)) {
echo '<tr><td  class="row1">';
echo '<form onsubmit="openWindow(\''.$_base_href.'tools/prog.php\');" name="form1" method="post" action="tools/upload.php" enctype="multipart/form-data">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
echo '<input type="file" name="uploadedfile" class="formfield" size="20" />';
echo '<input type="submit" name="submit" value="'._AT('upload').'" class="button" />';
echo '<input type="hidden" name="pathext" value="'.$pathext.'" />';
echo '</form>';
echo '</td></tr>';

} else {
	$msg->addInfo('OVER_QUOTA');
}
echo '</table>';
echo '<p /><p />';
// Directory and File listing 
echo '<form name="checkform" action="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext).'" method="post">'."\n";
echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">';
echo '<tr><td colspan="'.$totalcol.'"><input type="hidden" name="pathext" value ="'.$pathext.'" /></td></tr>'."\n";
echo '<tr>'.$buttons_top.'</tr>'."\n";
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
		$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').'" height="16" width="16" border="0" class="menuimage4s" />&nbsp;';

	} else {
		$totalBytes += $filedata[7];
		$filename = $file;
		$fileicon = '<small>&nbsp;<img src="images/icon_minipost.gif" alt="'._AT('file').'" height="11" width="16"  class="menuimage5" />&nbsp;</small>';
	} 
	$file1 = strtolower($file);
	// create listing for dirctor or file
	if ($is_dir) {
		
		$dirs[$file1] .= '<tr><td class="row1" align="center">';
		$dirs[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/></td>';//<span class=invisible>$file</span
		$dirs[$file1] .= '<td class="row1" align="center"><small><label for="'.$file.'" >'.$fileicon.'</label></small></td>';
		$dirs[$file1] .= '<td class="row1"><small>&nbsp;';
		$dirs[$file1] .= '<a href="'.$pathext.urlencode($filename).'">'.$filename.'</a>&nbsp;</small></td>'."\n";
			
		$dirs[$file1] .= '<td class="row1" align="right">';
		$dirs[$file1] .= '<small>'.number_format($size/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';
		$dirs[$file1] .= '<td class="row1"><small>&nbsp;';
		$dirs[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$dirs[$file1] .= '&nbsp;</small></td>';
		
		$dirs[$file1] .= '</tr>'."\n".'<tr><td height="1" class="row2" colspan="'.$totalcol.'"></td></tr>';
		$dirs[$file1] .= "\n";
	} else {
		$files[$file1] .= '<tr> <td class="row1" align="center">';
		$files[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/> </td>';
		$files[$file1] .= '<td class="row1" align="center"><small>'.$fileicon.'</small></td>';
		$files[$file1] .= '<td class="row1"><small>&nbsp;<label for="'.$file.'">';
		$files[$file1] .= '<a href="get.php/'.$pathext.urlencode($filename).'">'.$filename.'</a></label>';

		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="tools/zip.php?pathext='.$pathext.$file.'">';
			$files[$file1] .= '<img src="images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="menuimage6s" />';
			$files[$file1] .= '</a>';
		}
		$files[$file1] .= '&nbsp;</small></td>';

		
		$files[$file1] .= '<td class="row1" align="right">';
		$files[$file1] .= '<small>'.number_format($filedata[7]/AT_KBYTE_SIZE, 2).' KB&nbsp;</small></td>';
		$files[$file1] .= '<td class="row1"><small>&nbsp;';
		$files[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$files[$file1] .= '&nbsp;</small></td>';
		
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

echo '<tr> <td class="row1" colspan="'.$labelcol.'">';
echo '<input type="checkbox" name="checkall" onclick="Checkall(checkform);" id="selectall" /><small>';
echo '<label for="selectall">'._AT('select_all').'</label></small></td><td class="row1" colspan="2"></td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr> '.$buttons_bottom.'</tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";


echo '<tr><td class="row1" colspan="'.$labelcol.'" align="right">'."\n";
echo '<small><strong>'._AT('directory_total').':</strong><br /><br /></small></td>'."\n";
echo '<td align="right" class="row1"><small>&nbsp;<strong>'.number_format($totalBytes/AT_KBYTE_SIZE, 2).'</strong> KB&nbsp;<br /><br /></small></td>'."\n";
echo '<td class="row1" colspan="1"><small>&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";
echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'.$labelcol.'" align="right"><small><strong>'._AT('course_total').':</strong></small></td>'."\n";
echo '<td align="right" class="row1"><small>&nbsp;<strong>'.number_format($course_total/AT_KBYTE_SIZE, 2).'</strong> KB&nbsp;</small></td>'."\n";
echo '<td class="row1" colspan="1"><small>&nbsp;</small></td></tr>'."\n";

echo '<tr>'.$rowline.'</td></tr>'."\n";

echo '<tr><td class="row1" colspan="'.$labelcol.'" align="right"><small><strong>'._AT('course_available').':</strong></small></td>'."\n";
echo '<td align="right" class="row1"><small>&nbsp;<strong>'."\n";
if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
	echo _AT('unlimited');
} else {
	echo number_format(($my_MaxCourseSize-$course_total)/AT_KBYTE_SIZE, 2);
}
echo '</strong> KB&nbsp;</small></td>'."\n";
echo '<td class="row1" colspan="1">&nbsp;</td></tr>'."\n";

echo '</table></form>'."\n";
?>