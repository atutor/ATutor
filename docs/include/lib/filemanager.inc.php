<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* allows the copying of entire directories */
/* found on http://www.php.net/copy */
/* written by: www at w8c dot com */
function copys($source,$dest)
{
	if (!is_dir($source)) {
		return 0;
	}
	if (!is_dir($dest))	{
		mkdir($dest);
	}
	
	$h=@dir($source);
	while (@($entry=$h->read()) !== false) {
		if (($entry == '.') || ($entry == '..')) {
			continue;
		}

		if (is_dir("$source/$entry") && $dest!=="$source/$entry") {
			copys("$source/$entry", "$dest/$entry");
		} else {
			@copy("$source/$entry", "$dest/$entry");
		}
	}
	$h->close();
	return 1;
} 

// Enables deletion of directory if not empty
function clr_dir($dir) {
	if(!$opendir = @opendir($dir)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($dir.'/'.$readdir)) {
				if(!@unlink($dir.'/'.$readdir)) {
					return false;
				}
			} else if (is_dir($dir.'/'.$readdir)) {
				/* calls itself to clear subdirectories */
				if(!clr_dir($dir.'/'.$readdir)) {
					return false;
				}
			}
		}
	} /* end while */

	closedir($opendir);
	
	if(!@rmdir($dir)) {
		return false;
	}
	return true;
}

function dirsize($dir) {
	$dh = @opendir($dir);
	if (!$dh) {
		return -1;
	}
	$size = 0;
	while (($file = readdir($dh)) !== false) {

		if ($file != '.' && $file != '..') {
			$path = $dir.$file;
			if (is_dir($path)) {
				$size += dirsize($path.'/');
			} elseif (is_file($path)) {
				$size += filesize($path);
			}
		}
		
	}
	closedir($dh);
	return $size;
}


	function preExtractCallBack($p_event, &$p_header) {
		global $translated_file_names;

		if ($p_header['folder'] == 1) {
			return 1;
		}

		if ($translated_file_names[$p_header['index']] == '') {
			return 0;
		}

		if ($translated_file_names[$p_header['index']]) {
			$p_header['filename'] = substr($p_header['filename'], 0, -strlen($p_header['stored_filename']));
			$p_header['filename'] .= $translated_file_names[$p_header['index']];
		}
		return 1;
	}

	function preImportCallBack($p_event, &$p_header) {
		global $IllegalExtentions;

		if ($p_header['folder'] == 1) {
			return 1;
		}

		$path_parts = pathinfo($p_header['filename']);
		$ext = $path_parts['extension'];

		if (in_array($ext, $IllegalExtentions)) {
			return 0;
		}

		return 1;
	}

	/* only extract the language.csv file */
	function preImportLangCallBack($p_event, &$p_header) {

		if ($p_header['filename'] == '../../content/import/lang/language.csv') {
			return 1;
		}

		return 0;
	}

	/* prints the <options> out of $cats which is an array of course categories where */
	/* $cats[parent_cat_id][] = $row */
function print_course_cats($parent_cat_id, &$cats, $cat_row, $depth=0) {
	$my_cats = $cats[$parent_cat_id];
	if (!is_array($my_cats)) {
		return;
	}
	foreach ($my_cats as $cat) {

		echo '<option value="'.$cat['cat_id'].'"';
		if($cat['cat_id'] == $cat_row){
			echo  ' selected="selected"';
		}
		echo '>';
		echo str_pad('', $depth, '-');
		echo $cat['cat_name'].'</option>'."\n";

		print_course_cats($cat['cat_id'], $cats,  $cat_row, $depth+1);
	}
}

function bytes_to_megabytes($num_bytes) {
	return $num_bytes/AT_KBYTE_SIZE/AT_KBYTE_SIZE;
}

function megabytes_to_bytes($num_bytes) {
	return $num_bytes*AT_KBYTE_SIZE*AT_KBYTE_SIZE;
}

function bytes_to_kilobytes($num_bytes) {
	return $num_bytes/AT_KBYTE_SIZE;
}

function kilobytes_to_bytes($num_bytes) {
	return $num_bytes*AT_KBYTE_SIZE;
}

function output_instructors($cur_instructor) {
	global $db;

	$sql = "SELECT * FROM ".TABLE_PREFIX."members WHERE status='1'";
	$result = mysql_query($sql, $db);
	
	while($row = mysql_fetch_array($result)){
		$extra = "";
		if ($row['member_id'] == $cur_instructor) {
			$extra = ' selected="selected"';
		} 			
		echo '<option value="'.$row['member_id'].'"'.$extra.'>'.$row['login'].'</option>';		
	}
	return 1;
}

?>