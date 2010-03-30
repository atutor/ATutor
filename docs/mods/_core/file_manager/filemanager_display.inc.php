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
$cp = $_GET['cp'];
$cid = intval($_GET['cid']);        // content id, used at "adapted content" page, => add/edit alternatives
$pid = intval($_GET['pid']);        // primary resource id, used at "adapted content" page, => add/edit alternatives
$a_type = intval($_GET['a_type']);  // alternative_type, used at "adapted content" page, => add/edit alternatives

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$get_file = 'get.php/';
} else {
	$get_file = 'content/' . $_SESSION['course_id'] . '/';
}

function fm_path(){
	global $pathext, $framed, $popup, $cp, $cid, $pid, $a_type;
echo '<p>'._AT('current_path').' ';

if (isset($pathext) && $pathext != '') {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?popup=' . $popup . SEP . 'framed=' . $framed.SEP . 'cp=' . $cp.SEP . 'cid=' . $cid.SEP . 'pid=' . $pid.SEP . 'a_type=' . $a_type.'">'._AT('home').'</a> ';
}
else {
	$pathext = '';
	echo _AT('home');
}


if ($pathext == '' && isset($_POST['pathext'])) {

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
				echo '<a href="'.$_SERVER['PHP_SELF'].'?pathext=' . urlencode($bit_path) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed . SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'">' . $bit . '</a>';
			}
		}
	}
	$bit_path = "";
	$bit = "";
}
echo '</p>';

}

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
	echo '<div class="input-form"><fieldset class="group_form"><legend class="group_form">'._AT('add_file_folder').'</legend>'."\n";
	echo '	<div class="row">'."\n";
	echo '		<form name="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?'.(($pathext != '') ? 'pathext='.urlencode($pathext).SEP : ''). 'popup='.$popup.SEP.'cp='.SEP.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'">'."\n";
	if( $MakeDirOn ) {
		if ($depth < $MaxDirDepth) {
			echo '		<label for="dirname">'._AT('create_folder_here').'</label><br />'."\n";
			echo '		&nbsp;<small class="spacer">'._AT('keep_it_short').'</small><br />'."\n";
			echo '		<input type="text" name="dirname" id="dirname" size="20" /> '."\n";
			echo '		<input type="hidden" name="mkdir_value" value="true" /> '."\n";
			echo '		<input type="submit" name="mkdir" value="'._AT('create_folder').'" class="button" />'."\n";
		} else {
			echo _AT('depth_reached')."\n";
		}
	}
	echo '		<input type="hidden" name="pathext" value="'.$pathext.'" />'."\n";
	echo '		</form>'."\n";
	echo '	</div>'."\n";

	echo '	<div class="row"><hr /></div>'."\n";


	// If flash is available, provide the option of using Fluid's uploader or the basic uploader
	if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") {
		echo '<div class="row">'."\n";
		if (isset($_COOKIE["fluid_on"]) && $_COOKIE["fluid_on"]=="yes")
			$fluid_on = 'checked="checked"';
		echo '(<input type="checkbox" id="fluid_on" name="fluid_on" onclick="toggleform(\'simple-container\', \'fluid-container\'); setCheckboxCookie(this, \'fluid_on=yes\', \'fluid_on=no\',\'December 31, 2099\');" value="yes" '.$fluid_on.' /> '."\n";
		echo '<label for="fluid_on" >'._AT('enable_uploader').'</label>)'."\n";
		echo '</div>'."\n";
	}


	// Create a new file
	echo '	<div class="row" style="float: left;"><input type="button" class="button" name="new_file" value="' . _AT('file_manager_new') . '" onclick="window.location.href=\''.AT_BASE_HREF.'mods/_core/file_manager/new.php?pathext=' . urlencode($pathext) . SEP . 'framed=' . $framed . SEP . 'popup=' . $popup . '\'"/></div>'."\n";

	$my_MaxCourseSize = $system_courses[$_SESSION['course_id']]['max_quota'];

	// upload file 
	if (($my_MaxCourseSize == AT_COURSESIZE_UNLIMITED) 
		|| (($my_MaxCourseSize == AT_COURSESIZE_DEFAULT) && ($course_total < $MaxCourseSize))
		|| ($my_MaxCourseSize-$course_total > 0)) {
		echo '	<div class="row" style="float: left;">'._AT('OR').'</div>'."\n".'	<div class="row" style="float: left;">'."\n";
		if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") {
		?>
			<div id="fluid-container" <?php echo (isset($_COOKIE["fluid_on"]) && $_COOKIE["fluid_on"]=="yes") ? '' : 'style="display:none;"'; ?>>
				<input type="button" id="uploader_link" class="button" name="upload_file" value="<?php echo _AT('upload_files'); ?>" onclick="toggleform('uploader', 'uploader_link');" />
				<div id="uploader" style="display: none;">
					<form id="flc-uploader" class="flc-uploader fl-uploader fl-progEnhance-enhanced" method="get" enctype="multipart/form-data" action="" style="margin: 0px; padding: 0px;">
						<div class="start">
							<div class="fl-uploader-queue-wrapper">
								<div class="fl-uploader-queue-header">
									<table cellspacing="0" cellpadding="0">
											<tr>
												<th scope="col" class="fileName"><?php echo _AT('file_name'); ?></th>
												<th scope="col" class="fileSize"><?php echo _AT('size'); ?>&nbsp;&nbsp;</th>
												<th scope="col" class="fileRemove">&nbsp;</th>
											</tr>
									</table>
								</div>

			               <div class="flc-scroller fl-scroller">
			                        <div class="fl-scroller-inner">
			                            <table cellspacing="0" class="flc-uploader-queue fl-uploader-queue" summary="Queue of files to upload.">
			                                <tbody>
			                                    <!-- Rows will be rendered in here. -->
			                                    
			                                    <!-- Template markup for the file queue rows -->
			                                    <tr class="flc-uploader-file-tmplt flc-uploader-file fl-uploader-hidden-templates">
			                                        <th class="flc-uploader-file-name fl-uploader-file-name" scope="row"><?php echo _AT('file_placeholder'); ?></th>
			                                        <td class="flc-uploader-file-size fl-uploader-file-size">0 <?php echo _AT('kb'); ?></td>
			                                        <td class="fl-uploader-file-actions">
			                                            <button type="button" class="flc-uploader-file-action fl-uploader-file-action" title="<?php echo _AT('remove_queued_file'); ?>" tabindex="-1">
			                                                <span class="fl-uploader-button-text fl-uploader-hidden"><?php echo _AT('remove_queued_file'); ?></span>
			                                            </button>
			                                        </td>
			                                    </tr>
			                                    
			                                    <!-- Template for the file error info rows -->
			                                    <tr class="flc-uploader-file-error-tmplt fl-uploader-file-error fl-uploader-hidden-templates">
			                                        <td colspan="3" class="flc-uploader-file-error"></td>
			                                    </tr>
			                                </tbody>
			                            </table>
			                            <div class="flc-uploader-file-progressor-tmplt fl-uploader-file-progress"><span class="fl-uploader-file-progress-text fl-uploader-hidden">76%</span></div>
			                        </div>
			                    </div>

								<div class="flc-uploader-browse-instructions fl-uploader-browse-instructions"> <?php echo _AT('click_browse_files'); ?> </div>

								<div class="flc-uploader-queue-footer fl-uploader-queue-footer">
									<table cellspacing="0" cellpadding="0">
											<tr>
												<td class="flc-uploader-total-progress-text"><?php echo _AT('total'); ?>: <span class="fluid-uploader-totalFiles">0</span> <?php echo _AT('files'); ?> (<span class="fluid-uploader-totalBytes">0 <?php echo _AT('kb'); ?></span>)</td>
												<td class="fl-uploader-footer-buttons" align="right" ><a class="flc-uploader-button-browse fl-uploader-browse" tabindex="0" ><?php echo _AT('browse_files'); ?></a></td>
											</tr>
									</table>
									<div class="flc-uploader-total-progress fl-uploader-total-progress-okay">&nbsp;</div>
								</div>
							</div>
							
							<div class="fl-uploader-btns">
								<button type="button" class="flc-uploader-button-pause fl-uploader-pause fl-uploader-hidden" onclick="toggleform('uploader', 'uploader_link');"><?php echo _AT('cancel'); ?></button>
								<button type="button" class="flc-uploader-button-upload fl-uploader-upload fl-uploader-button-default fl-uploader-dim" ><?php echo _AT('upload'); ?></button>
							</div>
							
						</div>
					</form>

				</div>
			</div>
		<?php
			if (isset($_COOKIE["fluid_on"]) && $_COOKIE["fluid_on"]=="yes")
				echo '<div id="simple-container" style="display: none;">';
			else
				echo '<div id="simple-container">';
		} else {
			// Display as regular if there's no Flash detected
			echo '<div id="simple-container">'."\n";
		}

		// Simple single file uploader
		echo '<form onsubmit="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');" class="fl-ProgEnhance-basic" name="form1" method="post" action="mods/_core/file_manager/upload.php?popup='.$popup.SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'" enctype="multipart/form-data">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$my_MaxFileSize.'" />';
		echo '<label for="uploadedfile">'._AT('upload_files').'</label><br />'."\n";
		echo '<input type="file" name="uploadedfile" id="uploadedfile" class="formfield" size="20" /> ';
		echo '<input type="submit" name="submit" value="'._AT('upload').'" class="button" />';
		echo '<input type="hidden" name="pathext" value="'.$pathext.'" />  ';

		if ($popup == TRUE) {
			echo '<input type="hidden" name="popup" value="1" />';
		}
		echo '</form>';
		echo '</div>';

		echo '		</div>'."\n".'	</fieldset></div>';

	} else {
		echo '	</fieldset></div>'."\n";
		$msg->printInfos('OVER_QUOTA');
	}
	echo '<br />';
}



// Directory and File listing 


echo '<form name="checkform" action="'.$_SERVER['PHP_SELF'].'?'.(($pathext!='') ? 'pathext='.urlencode($pathext).SEP : '').'popup='.$popup .SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'" method="post">';
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />';
?>
<table class="data static" summary="" border="0" rules="groups" style="width: 90%">
<thead>
<tr>
<td colspan="5">
<?php fm_path(); ?>
</td>
</tr>
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
<?php


if($pathext) : ?>
	<tr>
		<td colspan="5"><a href="<?php echo $_SERVER['PHP_SELF'].'?back=1'.SEP.'pathext='.$pathext.SEP. 'popup=' . $popup .SEP. 'framed=' . $framed .SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type; ?>"><img src="images/arrowicon.gif" border="0" height="11" width="10" alt="" /> <?php echo _AT('back'); ?></a></td>
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
		$filename = '<a href="'.$_SERVER['PHP_SELF'].'?pathext='.urlencode($pathext.$file.'/'). SEP . 'popup=' . $popup . SEP . 'framed='. $framed . SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'">'.$file.'</a>';
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
		$dirs[$file1] .= get_human_size($size).'</td></tr>';

		
	} else {
		$files[$file1] .= '<tr> <td  align="center">';
		$files[$file1] .= '<input type="checkbox" id="'.$file.'" value="'.$file.'" name="check[]"/> </td>';
		$files[$file1] .= '<td  align="center"><label for="'.$file.'">'.$fileicon.'</label></td>';
		$files[$file1] .= '<td >&nbsp;';

		if ($framed) {
			$files[$file1] .= '<a href="'.$get_file.$pathext.urlencode($filename).'">'.$filename.'</a>';
		} else {
			$files[$file1] .= '<a href="mods/_core/file_manager/preview.php?file='.$pathext.$filename.SEP.'pathext='.urlencode($pathext).SEP.'popup='.$popup.'">'.$filename.'</a>';
		}

		if ($ext == 'zip') {
			$files[$file1] .= ' <a href="mods/_core/file_manager/zip.php?'.(($pathext!='') ? 'pathext='.urlencode($pathext).SEP : ''). 'file=' . urlencode($file) . SEP . 'popup=' . $popup . SEP . 'framed=' . $framed .'">';
			$files[$file1] .= '<img src="images/archive.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('extract_archive').'"height="16" width="11" class="img-size-fm3" />';
			$files[$file1] .= '</a>';
		}

		if (in_array($ext, $editable_file_types)) {
			$files[$file1] .= ' <a href="mods/_core/file_manager/edit.php?'.(($pathext!='') ? 'pathext='.urlencode($pathext).SEP : ''). 'popup=' . $popup . SEP . 'framed=' . $framed . SEP . 'file=' . $file . '">';
			$files[$file1] .= '<img src="images/edit.gif" border="0" alt="'._AT('extract_archive').'" title="'._AT('edit').'" height="15" width="18" class="img-size-fm4" />';
			$files[$file1] .= '</a>';
		}

		$files[$file1] .= '&nbsp;</td>';

		$files[$file1] .= '<td  align="right" style="white-space:nowrap">';

		if ($popup == TRUE) {
			if ($a_type > 0)  // define content alternative
			{
				$files[$file1] .= '<input class="button" type="button" name="alternative" value="' ._AT('use_as_alternative') . '" onclick="javascript: setAlternative(\''.get_relative_path($_GET['cp'], $pathext).$file.'\', \''.AT_BASE_HREF.$get_file.$pathext.urlencode($file).'\', \''.$cid.'\', \''.$pid.'\', \''.$a_type.'\');" />&nbsp;';
			}
			else
				$files[$file1] .= '<input class="button" type="button" name="insert" value="' ._AT('insert') . '" onclick="javascript:insertFile(\'' . $file . '\', \'' . get_relative_path($_GET['cp'], $pathext) . '\', \'' . $ext . '\', \'' .$_SESSION['prefs']['PREF_CONTENT_EDITOR']. '\');" />&nbsp;';
		}

		$files[$file1] .= AT_date(_AT('filemanager_date_format'), $filedata[10], AT_DATE_UNIX_TIMESTAMP);
		$files[$file1] .= '&nbsp;</td>';
		
		$files[$file1] .= '<td  align="right" style="white-space:nowrap">';
		$files[$file1] .= get_human_size($filedata[7]).'</td></tr>';
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
function insertFile(fileName, pathTo, ext, ed_pref) { 

	// pathTo + fileName should be relative to current path (specified by the Content Package Path)

	if (ext == "gif" || ext == "jpg" || ext == "jpeg" || ext == "png") {
		var info = "<?php echo _AT('alternate_text'); ?>";
		var html = '<img src="' + pathTo+fileName + '" border="0" alt="' + info + '" />';

		insertLink(html, ed_pref);
	} else if (ext == "mpg" || ext == "avi" || ext == "wmv" || ext == "mov" || ext == "swf" || ext == "mp3" || ext == "wav" || ext == "ogg" || ext == "mid" ||ext == "flv"|| ext == "mp4") {
		var html = '[media]'+ pathTo + fileName + '[/media]';

		insertLink(html, ed_pref);
	} else {
		var info = "<?php echo _AT('put_link'); ?>";
		var html = '<a href="' + pathTo+fileName + '">' + info + '</a>';
		
		insertLink(html, ed_pref);
	}
}

function insertLink(html, ed_pref)
{
    if (window.opener) {
        var isNotVisual = window.opener.document.form.html.checked && (ed_pref === '1');
    }

	if (!window.opener || !isNotVisual) {
		if (!window.opener && window.parent.tinyMCE)
			window.parent.tinyMCE.execCommand('mceInsertContent', false, html);
		else
			if (window.opener && window.opener.tinyMCE)
				window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
	} else {
			insertAtCursor(window.opener.document.form.body_text, html);
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

// This function does:
// 1. save into db via ajax
// 2. set the according field in opener window to the selected file
// 3. close file manager itself
function setAlternative(file, file_preview_link, cid, pid, a_type) {
	jQuery.post("<?php echo AT_BASE_HREF; ?>mods/_core/editor/save_alternative.php", 
			{"pid":pid, "a_type":a_type, "alternative":file}, 
			function(data) {});

	link_html = '\
      <a href="'+file_preview_link+'" title="<?php echo _AT('new_window'); ?>" target="_new">'+file+'</a><br /> \
      <a href="#" onclick="poptastic(\\\'<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>cp=<?php echo $cp.SEP; ?>cid='+cid+'<?php echo SEP; ?>pid='+pid+'<?php echo SEP; ?>a_type='+a_type+'\\\');return false;" title="<?php echo _AT('new_window'); ?>"> \
        <img src="<?php echo AT_BASE_HREF; ?>images/home-tests_sm.png" border="0" title="<?php echo _AT('alter'); ?>" alt="<?php echo _AT('alter'); ?>" /> \
      </a> \
      <a href="#" onclick="removeAlternative(\\\'<?php echo $cp; ?>\\\', '+cid+','+pid+','+a_type+');return false;"> \
        <img src="<?php echo AT_BASE_HREF; ?>images/icon_delete.gif" border="0" title="<?php echo _AT('remove'); ?>" alt="<?php echo _AT('remove'); ?>" /> \
      </a> \
    </div> \
';
	eval("window.opener.document.getElementById(\""+pid+"_"+a_type+"\").innerHTML = '"+link_html+"'");
	
	window.close();
}

<?php  if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") { ?>
// toggle the view between div object and button
function toggleform(id, link) {
	var obj = document.getElementById(id);
	var btn = document.getElementById(link);

	if (obj.style.display == "none") {
		//show
		obj.style.display='';
		obj.focus();

		btn.style.display = 'none';


	} else {
		//hide
		obj.style.display='none';
		btn.style.display = '';
	}
}

// set a cookie
function setCheckboxCookie(obj, value1, value2, date)
{
	var today = new Date();
	var the_date = new Date(date);
	var the_cookie_date = the_date.toGMTString();
	if (obj.checked==true)
		var the_cookie = value1 + ";expires=" + the_cookie_date;
	else
		var the_cookie = value2 + ";expires=" + the_cookie_date;
	document.cookie = the_cookie;
}
<?php } ?>

<?php 
// When uploading a file as an alternative content, set the alternative field in the opener window 
// and close "file manager" once the upload is successful
if ($a_type > 0 && isset($_GET['uploadfile']) && $_GET['uploadfile'] <> '') { ?>
function setAlternativeAndClose() {
	setAlternative('<?php echo get_relative_path($_GET['cp'], $pathext).$_GET['uploadfile']; ?>', '<?php echo AT_BASE_HREF.$get_file.$pathext.urlencode($_GET['uploadfile']); ?>', '<?php echo $cid; ?>', '<?php echo $pid; ?>', '<?php echo $a_type; ?>');
	window.close();
}

window.onload=setAlternativeAndClose;
<?php } ?>

//-->
</script>