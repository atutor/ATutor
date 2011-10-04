<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

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
		$depth = substr_count($src, '/');
		for ($i = 0; $i < $depth + 1; $i++)  // $depth+1 because the last '/' is not recorded in content.content_path
			$path .= '../';
		$path .= $dest;
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
		echo '(<input type="checkbox" id="fluid_on" name="fluid_on" value="yes" '.$fluid_on.' /> '."\n";
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
		<div id="uploader-error-container"></div>
			<div id="fluid-container">
				<div id="uploader">
				<!-- Basic upload controls, used when JavaScript is unavailable -->
        <form method="post" enctype="multipart/form-data" class="fl-progEnhance-basic">
            <p>Use the Browse button to add a file, and the Save button to upload it.</p>
            <input name="fileData" type="file" />
            <input class="fl-uploader-basic-save" type="submit" value="Save"/>
        </form>
        
        <!-- Uploader container -->
        <form class="flc-uploader fl-uploader fl-progEnhance-enhanced" method="get" enctype="multipart/form-data">
            
            <!-- File Queue, which is split up into two separate tables: one for the header and body -->
            <table class="fl-uploader-header">
           		<tr>
					<th class="fl-uploader-file-name">File Name</th>
					<th class="fl-uploader-file-size">Size</th>
					<th class="fl-uploader-file-actions"></th>
				</tr>u
            </table>
            
            <!-- File Queue body, which is the default container for the FileQueueView component -->
            <table summary="The list of files" class="flc-uploader-queue fl-uploader-queue">
				<caption>File Upload Queue:</caption>
				<tbody>
					<!-- Template for file row -->
					<tr class="flc-uploader-file-tmplt flc-uploader-file">
						<td class="flc-uploader-file-name fl-uploader-file-name">File Name Placeholder</td>
						<td class="flc-uploader-file-size fl-uploader-file-size">0 KB</td>
						<td class="fl-uploader-file-actions">
							<button type="button" class="flc-uploader-file-action" tabindex="-1"></button>
						</td>
					</tr>
					
					<!-- Template for error info row -->
					<tr class="flc-uploader-file-error-tmplt fl-uploader-file-error">
						<td colspan="3" class="flc-uploader-file-error"></td>
					</tr>
				</tbody>
			</table>
            
            <!-- File progress bar template, used to generate progress bars for each file in the queue -->
            <div class="flc-uploader-file-progressor-tmplt fl-uploader-file-progress"></div>            

            <!-- Initial instructions -->
            <div class="flc-uploader-browse-instructions fl-uploader-browse-instructions">
                Choose <em>Browse files</em> to add files to the queue. 
            </div>            

            <!-- Status footer -->
            <div class="flc-uploader-queue-footer fl-uploader-queue-footer fl-fix">
                <div class="flc-uploader-total-progress-text fl-uploader-total-progress-text fl-force-left">
                    Total: 0 files (0 KB)
                </div>
                <div class="fl-text-align-right fl-force-right">
                    <span class="flc-uploader-button-browse fl-uploader-browse">
                        <span class="flc-uploader-button-browse-text">Browse files</span>
                    </span>
                </div>
                <!-- Total progress bar -->
                <div class="flc-uploader-total-progress fl-uploader-total-progress-okay"></div>
                <div class="flc-uploader-errorsPanel fl-uploader-errorsPanel">
                     <div class="fl-uploader-errorsPanel-header"><span class="flc-uploader-errorPanel-header">Warnings:</span></div>
    
                     <!-- The markup for each error section will be rendered into these containers. -->
                     <div class="flc-uploader-errorPanel-section-fileSize"></div>
                     <div class="flc-uploader-errorPanel-section-numFiles"></div>
                     
                     <!-- Error section template.-->
                     <div class="flc-uploader-errorPanel-section-tmplt fl-uploader-hidden-templates">
                         <div class="flc-uploader-errorPanel-section-title fl-uploader-errorPanel-section-title">
                             x files were too y and were not added to the queue.
                         </div>
                         
                         <div class="flc-uploader-errorPanel-section-details fl-uploader-errorPanel-section-details">
                             <p>The following files were not added:</p>
                             <p class="flc-uploader-errorPanel-section-files">file_1, file_2, file_3, file_4, file_5 </p>
                         </div>
                         
                         <button type="button" class="flc-uploader-errorPanel-section-toggleDetails fl-uploader-errorPanel-section-toggleDetails">Hide this list</button>
                         <button type="button" class="flc-uploader-errorPanel-section-removeButton fl-uploader-errorPanel-section-removeButton">
                             <span class="flc-uploader-erroredButton-text fl-uploader-hidden">Remove error</span>
                         </button>
                     </div>
                 </div>                
            </div>
            
            <!-- Upload buttons -->
            <div class="fl-uploader-buttons">
                <button type="button" class="flc-uploader-button-pause fl-uploader-button-stop fl-uploader-hidden">Stop Upload</button>
                <button type="button" class="flc-uploader-button-upload fl-uploader-button-upload fl-uploader-dim">Upload</button>
            </div>
            
            <div class="flc-uploader-status-region fl-offScreen-hidden"></div>
        </form>        
            
        <script type="text/javascript">
            var myUploader = fluid.uploader(".flc-uploader", {
                queueSettings: {
                    uploadURL: '<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/upload.php',
                    fileUploadLimit: 5,
                    fileQueueLimit: 2,
                    postParams: {pathext: '<?php echo $pathext; ?>', type: 'ajax', submit: 'submit'},
                    fileSizeLimit: <?php echo $my_MaxFileSize/1024; ?>
                },
                events: {
                    onSuccess: {
                        event: "onFileSuccess",
                        args: [
                            {
                                fileName: "{arguments}.0.name",
                                responseText: "{arguments}.1"
                            }
                        ]
                    },
                    onError: {
                        event: "onFileError",
                        args: [
                            {
                                fileName: "{arguments}.0.name",
                                statusCode: "{arguments}.2",
                                responseText: "{arguments}.3.responseText"
                            }
                        ]
                    }
                },
                listeners: {
            		onSuccess: function (response){
		                // example assumes that the server code passes the new image URL in the serverData
        		        console.log("Success triggered", response);
        		        jQuery('#uploader-error-container').html(response.responseText);
                	}, 
                	onError: function(response) {
                        console.log("Error triggered", response);
                        jQuery('#uploader-error-container').html(response.responseText);
                    },
                    onUploadStart: function() {
                        jQuery('#uploader-error-container').html("");
                    },
                    afterUploadComplete: function () {
                        window.location = "<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/index.php?pathext=<?php echo $pathext; ?>";
                    }
    		    },
    		    components: {
                    strategy: {
                        options: {
                            flashMovieSettings: {
                                flashURL: "<?php echo AT_BASE_HREF; ?>jscripts/infusion/lib/swfupload/flash/swfupload.swf",
                                flashButtonImageURL: "<?php echo AT_BASE_HREF; ?>jscripts/infusion/components/uploader/images/browse.png"
                            }
                        }
                    }
                }
            });
            
            //bind fluid checkbox
            jQuery('#fluid_on').bind("click", function() {
                toggleform('simple-container', 'fluid-container'); 
                setCheckboxCookie(this, 'fluid_on=yes', 'fluid_on=no','December 31, 2099');
                console.log('hey');
            });
            
            //hide multifile uploader if it's not checked 
            if (!jQuery('#fluid_on').attr('checked')) {
                jQuery('#fluid-container').hide();
            }
        </script>
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
		echo '<form onsubmit="openWindow(\''.AT_BASE_HREF.'tools/prog.php\');" name="form1" method="post" action="mods/_core/file_manager/upload.php?popup='.$popup.SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'" enctype="multipart/form-data">';
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
}

// Directory and File listing 
echo '<form name="checkform" action="'.$_SERVER['PHP_SELF'].'?'.(($pathext!='') ? 'pathext='.urlencode($pathext).SEP : '').'popup='.$popup .SEP. 'framed='.$framed.SEP.'cp='.$_GET['cp'].SEP.'pid='.$_GET['pid'].SEP.'cid='.$cid.SEP.'a_type='.$a_type.'" method="post">';
echo '<input type="hidden" name="pathext" value ="'.$pathext.'" />';

// display the section to use a remote URL as an alternative
if ($a_type > 0) {
?>
<div class="input-form" style="min-height:10px">
<fieldset class="group_form" style="min-height: 0px;"><legend class="group_form"><?php echo _AT('use_url_as_alternative'); ?></legend>
	<div class="row">
	  <input name="remote_alternative" id="remote_alternative" value="http://" size="60" />
	  <input class="button" type="button" name="alternative" value="<?php echo _AT('use_as_alternative'); ?>" onclick="javascript: setURLAlternative();" />
	</div>
</fieldset>
</div>
<?php }?>

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
	var isVisual = false;
	
	if (window.opener) {
		if (typeof window.opener.jQuery("#html_visual_editor:checked").val() !== "undefined") {
			isVisual = true;
		}
	}

	if (!window.opener || isVisual) {
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
	// HTML encode the name of the secondary resource
	file = jQuery('<div/>').text(file).html();

	// save the selected secondary resource into db
	jQuery.post("<?php echo AT_BASE_HREF; ?>mods/_core/editor/save_alternative.php", 
			{"pid":pid, "a_type":a_type, "alternative":file}, 
			function(data) {});

	link_html = '\
      <a href="'+file_preview_link+'" title="<?php echo _AT('new_window'); ?>" target="_new">'+file+'</a><br /> \
      <a href="#" onclick="ATutor.poptastic(\\\'<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>cp=<?php echo $cp.SEP; ?>cid='+cid+'<?php echo SEP; ?>pid='+pid+'<?php echo SEP; ?>a_type='+a_type+'\\\');return false;" title="<?php echo _AT('new_window'); ?>"> \
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

// This function validates the url then call setAlternative()
function setURLAlternative() {
	remote_url = jQuery('#remote_alternative').val();
	if (remote_url == '' || remote_url == 'http://') {
		alert("<?php echo _AT('empty_url'); ?>");
		return false;
	}
	setAlternative(remote_url, remote_url, '<?php echo $cid; ?>.', '<?php echo $pid; ?>', '<?php echo $a_type; ?>');
}

<?php  if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes"): ?>
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
<?php endif; ?>

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
