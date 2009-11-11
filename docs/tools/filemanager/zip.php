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
// $Id$

$page = 'file_manager';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}


$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

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
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('UNKNOWN');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$path_parts = pathinfo($pathext.$file);

	$temp_name = substr($file, 0, -strlen('.'.$path_parts['extension']));

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
			$fileicon = '<img src="images/folder.gif" alt="'._AT('folder').'" />';

			$is_dir = true;

		} else if ($ext == 'zip') {

			$totalBytes += $list[$i]['size'];
			$filename = $list[$i]['stored_filename'];
			$fileicon = '<img src="images/icon-zip.gif" alt="'._AT('zip_archive').'" height="16" width="16" border="0" />';

		} else {
			$totalBytes += $list[$i]['size'];
			$filename = $list[$i]['stored_filename'];
			$fileicon = '<img src="images/icon_minipost.gif" alt="'._AT('file').'" height="11" width="16" />';
		}
		
		if ($is_dir) {
			$dirs[strtolower($filename)] .= '<tr>
				<td>'.$filename.'</td>';

				$dirs[strtolower($filename)] .= '<td class="row1" align="right">'.get_human_size($list[$i]['size']).' </td>';
				$dirs[strtolower($filename)] .= '<td class="row1">&nbsp;';
				
				$dirs[strtolower($filename)] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
					
				$dirs[strtolower($filename)] .= '&nbsp;</td>';

				$dirs[strtolower($filename)] .= '</tr>';
		} else {

			$files[strtolower($filename)] .= '<tr>
				<td>';

				if (in_array($ext, $IllegalExtentions)) {
					$files[strtolower($filename)] .=  '<span style="text-decoration: line-through;" title="'._AT('illegal_file').'">'.$filename.'</span>';
				} else {
					$files[strtolower($filename)] .= $filename;
					
					$trans_name = str_replace(' ', '_', $path_parts['basename']);
					$trans_name = preg_replace("/[^A-Za-z0-9._\-]/", '', $trans_name);

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
					
				$files[strtolower($filename)] .= '</td>';

				$files[strtolower($filename)] .= '<td align="right">'.get_human_size($list[$i]['size']).' </td>';
				$files[strtolower($filename)] .= '<td>&nbsp;';
				
				$files[strtolower($filename)] .= AT_date(_AT('filemanager_date_format'), $list[$i]['mtime'], AT_DATE_UNIX_TIMESTAMP);
					
				$files[strtolower($filename)] .= '</td>';
		
				$files[strtolower($filename)] .= '</tr>';
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
	}else{
		$total_after = get_human_size($my_MaxCourseSize - $course_total - $totalBytes);
	}

	// if $total_after < 0: redirect with error msg

	if (isset($_POST['submit']) && ($total_after > 0)) {
		$_POST['custom_path'] = trim($_POST['custom_path']);
		$_POST['custom_path'] = str_replace(' ', '_', $_POST['custom_path']);

		/* anything else should be okay, since we're on *nix.. hopefully */
		$_POST['custom_path'] = preg_replace('/[^a-zA-Z0-9._\/]/', '', $_POST['custom_path']);

		if (strpos($_POST['pathext'].$_POST['custom_path'], '..') !== false) {
			$msg->addError('UNKNOWN');
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
			exit;
		} else if ($zip->extract(	PCLZIP_OPT_PATH,		$path. $_POST['pathext'] . $_POST['custom_path'],  
							PCLZIP_CB_PRE_EXTRACT,	'preExtractCallBack')			== 0) {

			echo ("Error : ".$zip->errorInfo(true));
		} else {
			$msg->addFeedback('ARCHIVE_EXTRACTED');
			header('Location: index.php?pathext='.$_POST['pathext'].SEP.'popup='.$_POST['popup'].SEP.'framed='.$_POST['framed']);
			exit;
		}

		header('Location: index.php');
		exit;
	}

	require(AT_INCLUDE_PATH.'header.inc.php');

	if (($my_MaxCourseSize != AT_COURSESIZE_UNLIMITED) && ($total_after  + $MaxCourseFloat <= 0)) {
		$msg->printErrors('NO_SPACE_LEFT');
	} else {
?>
		<form method="post" action="tools/filemanager/zip.php">
		<input type="hidden" name="pathext" value="<?php echo $_GET['pathext']; ?>" />
		<input type="hidden" name="file"    value="<?php echo $_GET['file']; ?>" />
		<input type="hidden" name="popup"   value="<?php echo $_GET['popup']; ?>" />
		<input type="hidden" name="framed"   value="<?php echo $_GET['framed']; ?>" />
		<div class="input-form">
			<div class="row">
				<p><?php echo _AT('zip_illegal_contents'); ?></p>
				<p><?php echo _AT('extract_tip'); ?></p>
			</div>

			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('directory_name'); ?><br />
				<input type="text" name="custom_path" value="<?php echo $temp_name; ?>" />
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('extract'); ?>" /> 
				<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
			</div>
		</div>
		</form>
<?php
	} // end if
?>

<table class="data static" summary="" rules="groups">
<thead>
<tr>
	<th><?php echo _AT('name'); ?></th>
	<th><?php echo _AT('size'); ?></th>
	<th><?php echo _AT('date'); ?></th>
</tr>
</thead>
<tbody>
	<?php
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
?>
</tbody>
<tfoot>
<tr>
	<td align="right"><?php echo _AT('archive_total'); ?>:</td>
	<td align="right"><?php echo get_human_size($totalBytes); ?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="right"><?php echo _AT('course_total_zip'); ?>:</td>
	<td align="right"><?php echo get_human_size($course_total); ?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="right"><?php echo _AT('course_available_zip1'); ?>:</td>
	<td align="right"><?php
			if ($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) {
				echo _AT('unlimited');
			} else {
				echo get_human_size($my_MaxCourseSize-$course_total);
			} ?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="right"><?php echo _AT('course_available_zip2'); ?>:</td>
	<td align="right"><?php
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
			} ?></td>
	<td>&nbsp;</td>
</tr>
</tfoot>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>