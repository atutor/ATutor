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
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('file_manager');
$_section[1][1] = 'tools/filemanager/zip.php';
$_section[2][0] = _AT('zip_manager');

authenticate(AT_PRIV_FILES);

if ($_GET['popup'] || $_GET['framed']) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';	
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'popup='.$_POST['popup'].SEP.'framed='.$_POST['framed']);
	exit;
}

	$path = AT_CONTENT_DIR . $_SESSION['course_id'].'/';

	if ($_REQUEST['pathext'] != '') {
		$pathext = $_REQUEST['pathext'];
	}
	if ($_REQUEST['file'] != '') {
		$file = $_REQUEST['file'];
	}

	if (strpos($file, '..') !== false) {
		require($_header_file);
		$msg->printErrors('UNKNOWN');
		require($_footer_file);
		exit;
	}

	$path_parts = pathinfo($pathext.$file);

	$temp_name = substr($pathext.$file, 0, -strlen('.'.$path_parts['extension']));

	$zip = new PclZip($path.$pathext.$file);

	if (($list = $zip->listContent()) == 0) {
		die("Error : ".$zip->errorInfo(true));
	}

/*****************************************************************/
	$totalBytes = 0;
	$translated_file_names = array();

	for ($i=0; $i<sizeof($list); $i++) {
		$path_parts = pathinfo($list[$i]['stored_filename']);
		if ($path_parts['dirname'] == '.') {
			$path_parts['dirname'] = '';
		} else {
			$path_parts['dirname'] .= '/';
		}
		$ext = $path_parts['extension'];

		$is_dir = false;
		if($list[$i]['folder']) {

			$filename = $list[$i]['stored_filename'];
			$fileicon = '&nbsp;<img src="images/folder.gif" alt="'._AT('folder').'" />&nbsp;';

			$is_dir = true;

		} else if ($ext == 'zip') {

			$totalBytes += $list[$i]['size'];
			$filename = $list[$i]['stored_filename'];
			$fileicon = '&nbsp;<img src="images/icon-zip.gif" alt="'._AT('zip_archive').'" height="16" width="16" border="0" />&nbsp;';

		} else {
			$totalBytes += $list[$i]['size'];
			$filename = $list[$i]['stored_filename'];
			$fileicon = '&nbsp;<img src="images/icon_minipost.gif" alt="'._AT('file').'" height="11" width="16" />&nbsp;';
		}
		
		if ($is_dir) {
			$dirs[strtolower($filename)] .= '<tr>
				<td class="row1" align="center">'.$fileicon.'</td>
				<td class="row1"><small>&nbsp;'.$filename.'&nbsp;</small></td>';

				$dirs[strtolower($filename)] .= '<td class="row1" align="right"><small>'.get_human_size($list[$i]['size']).' </small></td>';
				$dirs[strtolower($filename)] .= '<td class="row1"><small>&nbsp;';
				
				$dirs[strtolower($filename)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
					
				$dirs[strtolower($filename)] .= '&nbsp;</small></td>';

				$dirs[strtolower($filename)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="5"></td>
				</tr>';
		} else {

			$files[strtolower($filename)] .= '<tr>
				<td class="row1" align="center">'.$fileicon.'</td>
				<td class="row1"><small>&nbsp;';

				if (in_array($ext, $IllegalExtentions)) {
					$files[strtolower($filename)] .=  '<span style="text-decoration: line-through;" title="'._AT('illegal_file').'">'.$filename.'</span>';
				} else {
					$files[strtolower($filename)] .= $filename;
					
					$trans_name = str_replace(' ', '_', $path_parts['basename']);
					$trans_name = ereg_replace("[^A-Za-z0-9._]", '', $trans_name);

					if (in_array($path_parts['dirname'].$trans_name, $translated_file_names)) {
						$trans_count = 2;
						while (in_array($trans_name, $translated_file_names)) {
							$part = substr($trans_name, 0, -strlen($ext)- 1 - (2*($trans_count-2)));
							$trans_name = $part.'_'.$trans_count.'.'.$ext;
							$trans_count++;
							if ($trans_count>15){
								exit; // INF loop safety thing..
							}
						}
					}
					
					$translated_file_names[$list[$i]['index']] = $path_parts['dirname'].$trans_name;

					if ($path_parts['dirname'].$trans_name != $filename) {
						$files[strtolower($filename)] .= ' => '.$trans_name;
					}
					
				}
					
				$files[strtolower($filename)] .= '&nbsp;</small></td>';

				$files[strtolower($filename)] .= '<td class="row1" align="right"><small>'.get_human_size($list[$i]['size']).' </small></td>';
				$files[strtolower($filename)] .= '<td class="row1"><small>&nbsp;';
				
				$files[strtolower($filename)] .= AT_date(_AT('filemanager_date_format'), $list[$i]['mtime'], AT_DATE_UNIX_TIMESTAMP);
					
				$files[strtolower($filename)] .= '&nbsp;</small></td>';
		
				$files[strtolower($filename)] .= '</tr>
				<tr>
				<td height="1" class="row2" colspan="5"></td>
				</tr>';
		}
	}

	$sql	= "SELECT max_quota, max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);
	$my_MaxCourseSize	= $row['max_quota'];
	$my_MaxFileSize     = $row['max_file_size'];

	$course_total = dirsize($path);
	if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
		$total_after = 1;
	} else if ($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) {
		$my_MaxCourseSize = $MaxCourseSize;
		$total_after = get_human_size($my_MaxCourseSize-$course_total-$totalBytes);
	}

	// if $total_after < 0: redirect with error msg

	if (isset($_POST['submit']) && ($total_after > 0)) {

		$_POST['custom_path'] = trim($_POST['custom_path']);
		$_POST['custom_path'] = str_replace(' ', '_', $_POST['custom_path']);

		/* anything else should be okay, since we're on *nix.. hopefully */
		$_POST['custom_path'] = ereg_replace('[^a-zA-Z0-9._/]', '', $_POST['custom_path']);

		if (strpos($_POST['pathext'].$_POST['custom_path'], '..') !== false) {
			$msg->addError('UNKNOWN');
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
			exit;
		}
		else if ($zip->extract(	PCLZIP_OPT_PATH,		$path.$_POST['custom_path'],  
							PCLZIP_CB_PRE_EXTRACT,	'preExtractCallBack')			== 0) {

			echo ("Error : ".$zip->errorInfo(true));
		} else {
			$msg->addFeedback('ARCHIVE_EXTRACTED');
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'popup='.$_POST['popup'].SEP.'framed='.$_POST['framed']);
			exit;
		}

		require($_footer_file);
		exit;
	}

	require($_header_file);

	if ($framed == TRUE) {
		echo '<h3>'._AT('file_manager').'</h3>';
	}
	else {
		if ($popup == TRUE) {
			echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
		}
		
		echo '<h2>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
		}

		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			if ($popup == TRUE)
				echo ' '._AT('tools')."\n";
			else 
				echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>'."\n";
		}

		echo '</h2>'."\n";

		echo '<h3>';
		
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {	
			echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo '<a href="tools/filemanager/index.php?popup=' . $popup . SEP . 'framed=' . $framed .'">' . _AT('file_manager') . '</a>' . "\n";
		}
		echo '</h3>'."\n";
	}
?>

	<h4><?php echo _AT('zip_file_manager'); ?></h4>
<br />
	<p><?php echo _AT('zip_illegal_contents'); ?></p>
<?php
	if (($my_MaxCourseSize != AT_COURSESIZE_UNLIMITED) && ($total_after  + $MaxCourseFloat <= 0)) {
		$msg->printErrors('NO_SPACE_LEFT');
	} else {
?>
		<form method="post" action="tools/filemanager/zip.php">
		<input type="hidden" name="pathext" value="<?php echo $_GET['pathext']; ?>" />
		<input type="hidden" name="file"    value="<?php echo $_GET['file']; ?>" />
		<input type="hidden" name="popup"   value="<?php echo $_GET['popup']; ?>" />
		<input type="hidden" name="framed"   value="<?php echo $_GET['framed']; ?>" />
		<p>
			<?php echo _AT('directory_name'); ?>: <input type="text" name="custom_path" value="<?php echo $temp_name; ?>" class="formfield" />
			<input type="submit" name="submit" value="<?php echo _AT('extract'); ?>" class="button" /> -
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /><br />
			<small><?php echo _AT('extract_tip'); ?></small>
		</p>
		</form>
<?php
	} // end if

	

	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">';
	echo '<tr>
			<th>';
	echo '<small>&nbsp;</small></th><th><small>'._AT('name').'</small></th>';
	echo '<th><small>'._AT('size').'</small></th>';
	echo '<th><small>'._AT('date').'</small></th>';

	echo '</tr>';
	if (is_array($dirs)) {
		foreach($dirs as $x => $y) {
			echo $y;
		}
	}

	if (is_array($files)) {
		foreach($files as $x => $y) {
			echo $y;
		}
	}

	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';

	echo '<tr><td class="row1" colspan="2" align="right"><small><b>'._AT('archive_total').':</b><br /><br /></small></td><td align="right" class="row1"><small>&nbsp;<b>'.get_human_size($totalBytes).'</b> <br /><br /></small></td><td class="row1" colspan="2">&nbsp;</td></tr>';

	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';
	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';


	echo '<tr><td class="row1" colspan="2" align="right"><small><b>'._AT('course_total_zip').':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>'.get_human_size($course_total).'</b> </small></td><td class="row1" colspan="2"><small>&nbsp;</small></td></tr>';

	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';

	echo '<tr><td class="row1" colspan="2" align="right"><small><b>'._AT('course_available_zip1').':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>';
	if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
		echo _AT('unlimited');
	} else {
		echo get_human_size($my_MaxCourseSize-$course_total);
	}
	echo '</b> </small></td><td class="row1" colspan="2"><small>&nbsp;</small></td></tr>';


	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';

	echo '<tr><td class="row1" colspan="2" align="right"><small><b>'._AT('course_available_zip2').':</b></small></td><td align="right" class="row1"><small>&nbsp;<b>';
	if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
		echo _AT('unlimited');
	} else {
		if ($total_after <= 0) {
			echo '<span style="color: red;">';
			echo $total_after;
			echo '</span>';
		} else {
			echo $total_after;
		}
	}
	echo '</b> </small></td><td class="row1" colspan="2"><small>&nbsp;</small></td></tr>';
	echo '</table>';

	require($_footer_file);
?>