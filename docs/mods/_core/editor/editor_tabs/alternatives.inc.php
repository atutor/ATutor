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

/**
 * This script creates the interface of "edit content" => "adapted content"
 */

if (!defined('AT_INCLUDE_PATH')) { exit; }

if ($cid == 0) {
	$msg->printErrors('SAVE_BEFORE_PROCEED');
	require_once(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

/**
 * This function returns the preview link of the given file
 * @param  $file     the file location in "file manager"
 * @return the relative URL to preview the file
 */
function get_preview_link($file)
{
	global $content_row;
	
	if (substr($file, 0 , 7) == 'http://' || substr($file, 0 , 8) == 'https://') {
		return $file;
	} else {
		if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
			$get_file = 'get.php/';
		} else {
			$get_file = 'content/' . $_SESSION['course_id'] . '/';
		}
		
		return $get_file.$content_row['content_path'].'/'.$file;
	}
}

/**
 * When the file name is a remote URL, this function reduces the full URL
 * @param  $filename
 * @return the reduced name
 */
function get_display_filename($filename)
{
	if (substr($filename, 0 , 7) == 'http://' || substr($filename, 0 , 8) == 'https://') {
		if (substr($filename, 0 , 7) == 'http://') $prefix = 'http://';
		if (substr($filename, 0 , 8) == 'https://') $prefix = 'https://';
		$name = substr($filename, strrpos($filename, '/'));
		$filename = $prefix.'...'.$name;
	}
	return $filename;
}

/**
 * Display alternative table cell
 * @param $secondary_result   mysql result of all secondary alternatives
 *        $alternative type   the resource type of the alternative to display. Must be one of the values in resource_types.type_id
 *        $content_id         used to pass into file_manager/index.php
 *        $ps                 used to pass into file_manager/index.php
 * @return html of the table cell "<td>...</td>"
 */ 
function display_alternative_cell($secondary_result, $alternative_type, $content_id, $pid, $td_header_id)
{
	global $content_row;
	
	$found_alternative = false;
	
	echo '    <td headers="'.$td_header_id.'">'."\n";
	
	if (mysql_num_rows($secondary_result) > 0)
	{
		mysql_data_seek($secondary_result, 0);  // move the mysql result cursor back to the first row
		while ($secondary_resource = mysql_fetch_assoc($secondary_result))
		{
			if ($secondary_resource['type_id'] == $alternative_type)
			{
				echo '    <div id="'.$pid.'_'.$alternative_type.'">'."\n";
				echo '      <a href="'.get_preview_link($secondary_resource['secondary_resource']).'" title="'._AT('new_window').'" target="_new">'.get_display_filename($secondary_resource['secondary_resource']).'</a><br />'."\n";
				echo '      <a href="#" onclick="ATutor.poptastic(\''.AT_BASE_HREF.'mods/_core/file_manager/index.php?framed=1'. SEP.'popup=1'. SEP.'cp='. $content_row['content_path'].SEP.'cid='.$content_id.SEP.'pid='.$pid.SEP.'a_type='.$alternative_type.'\');return false;" title="'._AT('new_window').'">'."\n";
				echo '        <img src="'.AT_BASE_HREF.'images/home-tests_sm.png" border="0" title="'._AT('alter').'" alt="'._AT('alter').'" />'."\n";
				echo '      </a>'."\n";
				echo '      <a href="#" onclick="removeAlternative(\''.$content_row['content_path'].'\', '.$content_id.','.$pid.','.$alternative_type.');return false;">'."\n";
				echo '        <img src="'.AT_BASE_HREF.'images/icon_delete.gif" border="0" title="'._AT('remove').'" alt="'._AT('remove').'" />'."\n";
				echo '      </a>'."\n";
				echo '    </div>'."\n";
				$found_alternative = true;
				break;
			}
		}
	}
	if (!$found_alternative)
	{
		echo '    <div id="'.$pid.'_'.$alternative_type.'">'."\n";
		echo '      <input type="button" value="'._AT('add').'" title="'._AT('new_window').'" onclick="ATutor.poptastic(\''.AT_BASE_HREF.'mods/_core/file_manager/index.php?framed=1'. SEP.'popup=1'. SEP.'cp='. $content_row['content_path'].SEP.'cid='.$content_id.SEP.'pid='.$pid.SEP.'a_type='.$alternative_type.'\');return false;" />'."\n";
		echo '    </div>'."\n";
	}
	echo '    </td>'."\n";
}

// Main program
global $db, $content_row;
populate_a4a($cid, $_POST['body_text'], $_POST['formatting']);

include_once(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.class.php');

$a4a = new A4a($cid);
$primary_resources = $a4a->getPrimaryResources();

if (count($primary_resources)==0)
{
	echo '<p>'. _AT('No_resources'). '</p>';
}
else
{
	$is_post_indicator_set = false;
	// get all resource types
	$sql = "SELECT * FROM ".TABLE_PREFIX."resource_types";
	$resource_types_result = mysql_query($sql, $db);
	
	echo '<table class="data" rules="all" style="width:100%">'."\n";
	echo '  <thead>'."\n";
	echo '  <tr>'."\n";
	echo '    <th rowspan="2" id="header1">'._AT('original_resource').'</th>'."\n";
	echo '    <th rowspan="2" id="header2">'._AT('resource_type').'</th>'."\n";
	echo '    <th colspan="4">'._AT('alternatives').'</th>'."\n";
	echo '  </tr>'."\n";
	echo '  <tr>'."\n";
	echo '    <th id="header3">'._AT('text').'</th>'."\n";
	echo '    <th id="header4">'._AT('audio').'</th>'."\n";
	echo '    <th id="header5">'._AT('visual').'</th>'."\n";
	echo '    <th id="header6">'._AT('sign_lang').'</th>'."\n";
	echo '  </tr>'."\n";
	echo '  </thead>'."\n";
	
	echo '  <tbody>';

	foreach($primary_resources as $primary_resource_id => $primary_resource_row)
	{
		$primary_resource = $primary_resource_row['resource'];
		
		$sql = "SELECT prt.type_id, rt.type
		          FROM ".TABLE_PREFIX."primary_resources pr, ".
		                 TABLE_PREFIX."primary_resources_types prt, ".
		                 TABLE_PREFIX."resource_types rt
		         WHERE pr.content_id = ".$cid."
		           AND pr.language_code = '".$_SESSION['lang']."'
		           AND pr.primary_resource_id='".$primary_resource_id."'
		           AND pr.primary_resource_id = prt.primary_resource_id
		           AND prt.type_id = rt.type_id";
		$primary_type_result = mysql_query($sql, $db);
		
		if (!$is_post_indicator_set)
		{
			echo '  <input type="hidden" name="use_post_for_alt" value="1" />'."\n";
			$is_post_indicator_set = true;
		}
		
		// get secondary resources for the current primary resource
		$sql = "SELECT pr.primary_resource_id, sr.secondary_resource, srt.type_id
		          FROM ".TABLE_PREFIX."primary_resources pr, ".
		                 TABLE_PREFIX."secondary_resources sr, ".
		                 TABLE_PREFIX."secondary_resources_types srt
		         WHERE pr.content_id = ".$cid."
		           AND pr.language_code = '".$_SESSION['lang']."'
		           AND pr.primary_resource_id='".$primary_resource_id."'
		           AND pr.primary_resource_id = sr.primary_resource_id
		           AND sr.secondary_resource_id = srt.secondary_resource_id";
		$secondary_result = mysql_query($sql, $db);
		
		echo '  <tr>'."\n";

		// table cell "original resource"
		echo '    <td headers="header1">'."\n";
		echo '    <a href="'.get_preview_link($primary_resource).'" title="'._AT('new_window').'" target="_new">'.get_display_filename($primary_resource).'</a>'."\n";
		echo '    </td>'."\n";

		// table cell "original resource type"
		echo '    <td headers="header2">'."\n";
		
		mysql_data_seek($resource_types_result, 0);  // move the mysql result cursor back to the first row
		while ($resource_type = mysql_fetch_assoc($resource_types_result))
		{
			if ($resource_type['type'] == 'sign_language')
				continue;
			else 
			{
				echo '<input type="checkbox" name="alt_'.$primary_resource_id.'_'.$resource_type['type_id'].'" value="1" id="alt_'.$primary_resource_id.'_'.$resource_type['type_id'].'"';
				if ($_POST['use_post_for_alt'])
				{
					if (isset($_POST['alt_'.$primary_resource_id.'_'.$resource_type['type_id']])) {
						echo 'checked="checked"';
					}
				}
				else {
					if (mysql_num_rows($primary_type_result)> 0) mysql_data_seek($primary_type_result, 0);
					while ($primary_resource_type = mysql_fetch_assoc($primary_type_result)) {
						if ($primary_resource_type['type_id'] == $resource_type['type_id']){
							echo 'checked="checked"';
							break;
						}
					}
				}
				echo '/>'."\n";
				echo '<label for="alt_'.$primary_resource_id.'_'.$resource_type['type_id'].'">'. _AT($resource_type['type']).'</label><br/>'."\n";	
			}	
		}
		echo '    </td>'."\n";
		
		// table cell "text alternative"
		display_alternative_cell($secondary_result, 3, $cid, $primary_resource_id, "header3");
		
		// table cell "audio"
		display_alternative_cell($secondary_result, 1, $cid, $primary_resource_id, "header4");
		
		// table cell "visual"
		display_alternative_cell($secondary_result, 4, $cid, $primary_resource_id, "header5");
		
		// table cell "sign language"
		display_alternative_cell($secondary_result, 2, $cid, $primary_resource_id, "header6");
		
		echo '  </tr>'."\n";
	}
	echo '  </tbody>'."\n";
	echo '</table>'."\n";
}
?>

<script type="text/javascript">
//<!--
// This function does:
// 1. save the removal into db via ajax
// 2. set the according field to "add" button
function removeAlternative(contentPath, cid, pid, a_type) 
{
	jQuery.post("<?php echo AT_BASE_HREF; ?>mods/_core/editor/remove_alternative.php", 
			{"pid":pid, "a_type":a_type}, 
			function(data) {});

	var button_html = '      <input type="button" value="<?php echo _AT('add'); ?>" title="<?php echo _AT('new_window'); ?>" onclick="ATutor.poptastic(\\\'<?php echo AT_BASE_HREF; ?>mods/_core/file_manager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>cp='+contentPath+'<?php echo SEP; ?>cid='+cid+'<?php echo SEP; ?>pid='+pid+'<?php echo SEP; ?>a_type='+a_type+'\\\');return false;" />';
	eval("document.getElementById(\""+pid+"_"+a_type+"\").innerHTML = '"+button_html+"'");
}
//-->
</script>