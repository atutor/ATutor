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
if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Allows the copying of entire directories.
* @access  public
* @param   string $source		the source directory
* @param   string $dest			the destination directory
* @return  boolean				whether the copy was successful or not
* @link	   http://www.php.net/copy
* @author  www at w8c dot com
*/
function copys($source,$dest)
{
	if (!is_dir($source)) {
		return false;
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
	return true;
} 

/**
* Enables deletion of directory if not empty
* @access  public
* @param   string $dir		the directory to delete
* @return  boolean			whether the deletion was successful
* @author  Joel Kronenberg
*/
function clr_dir($dir) {
	if(!$opendir = @opendir($dir)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($dir.'/'.$readdir)) {
				if(!unlink($dir.'/'.$readdir)) {
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

	@closedir($opendir);
	
	if(!@rmdir($dir)) {
		return false;
	}
	return true;
}

/**
* Calculate the size in Bytes of a directory recursively.
* @access  public
* @param   string $dir		the directory to traverse
* @return  int				the total size in Bytes of the directory
* @author  Joel Kronenberg
*/
function dirsize($dir) {
	if (is_dir($dir)) {
		$dh = @opendir($dir);
	}
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

/**
* This function gets used by PclZip when extracting a zip archive.
* @access  private
* @return  int				whether or not to include the file
* @author  Joel Kronenberg
*/
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

/**
* This function gets used by PclZip when creating a zip archive.
* @access  private
* @return  int				whether or not to include the file
* @author  Joel Kronenberg
*/
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

// returns the most appropriate representation of Bytes in MB, KB, or B
function get_human_size($num_bytes) {
	if ($num_bytes >= AT_KBYTE_SIZE * AT_KBYTE_SIZE) {
		return round(bytes_to_megabytes($num_bytes), 2) . ' MB';
	} else if ($num_bytes >= AT_KBYTE_SIZE) {
		return round(bytes_to_kilobytes($num_bytes), 2) . ' KB';
	}
	// else:

	return $num_bytes . ' B';
}

/**
* Returns the MB representation of inputed bytes
* @access  public
* @param   int $num_bytes	the input bytes to convert
* @return  int				MB representation of $num_bytes
* @author  Heidi Hazelton
*/
function bytes_to_megabytes($num_bytes) {
	return $num_bytes/AT_KBYTE_SIZE/AT_KBYTE_SIZE;
}

/**
* Returns the Byte representation of inputed MB
* @access  public
* @param   int $num_bytes	the input MB to convert
* @return  int				the Bytes representation of $num_bytes
* @author  Heidi Hazelton
*/
function megabytes_to_bytes($num_bytes) {
	return $num_bytes*AT_KBYTE_SIZE*AT_KBYTE_SIZE;
}

/**
* Returns the KB representation of inputed Bytes
* @access  public
* @param   int $num_bytes	the input Bytes to convert
* @return  int				the KB representation of $num_bytes
* @author  Heidi Hazelton
*/
function bytes_to_kilobytes($num_bytes) {
	return $num_bytes/AT_KBYTE_SIZE;
}

/**
* Returns the Bytes representation of inputed KBytes
* @access  public
* @param   int $num_bytes	the input KBytes to convert
* @return  int				the KBytes representation of $num_bytes
* @author  Heidi Hazelton
*/
function kilobytes_to_bytes($num_bytes) {
	return $num_bytes*AT_KBYTE_SIZE;
}

/**
* Outputs all the instructors in ATutor in the form of <option> elements.
* @access  public
* @param   int $cur_instructor	the member ID of the instructor to preselect the options to.
* @see     include/html/course_properties.inc.php
* @author  Heidi Hazelton
*/
function output_instructors($cur_instructor) {
	global $db;

	$sql = "SELECT * FROM ".TABLE_PREFIX."members WHERE status=1";
	$result = mysql_query($sql, $db);
	
	while($row = mysql_fetch_assoc($result)){
		$extra = '';
		if ($row['member_id'] == $cur_instructor) {
			$extra = ' selected="selected"';
		}
		echo '<option value="'.$row['member_id'].'"'.$extra.'>'.$row['login'].'</option>';		
	}
}
/**
* Outputs the directories associated with a course in the form of <option> elements.
* @access public
* @param  string $cur_dir  the current directory to include in the options.
* @author Norma Thompson
*/
function output_dirs($current_path,$cur_dir,$indent) {
	// open the cur_dir
	if ($dir = opendir($current_path.$cur_dir)) {

		// recursively call output_dirs() for all directories in this directory
		while (false !== ($file = readdir($dir)) ) {

			//if the name is not a directory 
			if( ($file == '.') || ($file == '..') ) {
				continue;
			}

			// if it is a directory call function
			if(is_dir($current_path.$cur_dir.$file)) {
				$ldir = explode('/',$cur_dir.$file);
				$count = count($ldir);
				$label = $ldir[$count-1];
				
				$dir_option .= '<option value="'.$cur_dir.$file.'/" >'.$indent.$label.'</option>'."\n";

				$dir_option .= output_dirs($current_path,$cur_dir.$file.'/',$indent.'--');
			}
			
		} // end while	
		
		closedir($dir);	
	}
	return $dir_option;
}

function display_tree($current_path,$cur_dir) {
	// open the cur_dir
	if ($dir = opendir($current_path.$cur_dir)) {
	
		// recursively call output_dirs() for all directories in this directory
		while (false !== ($file = readdir($dir)) ) {

			//if the name is not a directory 
			if( ($file == '.') || ($file == '..') ) {
				continue;
			}
			
			// if it is a directory call function
			if(is_dir($current_path.$cur_dir.$file)) {
				$ldir = explode('/',$cur_dir.$file);
				$count = count($ldir);
				$label = $ldir[$count-1];

				$dir_option .= '<ul><li class="folders">';
				$dir_option .= '<label><input type="radio" name="dir_name" value="'.$cur_dir.$file.'" />'. $label .'</label>'."\n";
				$dir_option .= ''.display_tree($current_path,$cur_dir.$file.'/').'';
				$dir_option .= '</li></ul>';
			}

			
		} // end while	
		
		closedir($dir);	
	}
	return $dir_option;
}

?>