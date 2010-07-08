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
	//	 echo 'something';
    //  exit;
	return $size;
 // exit;
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
	$abs_num_bytes = abs($num_bytes);

	if ($abs_num_bytes >= AT_KBYTE_SIZE * AT_KBYTE_SIZE) {
		return round(bytes_to_megabytes($num_bytes), 2) .' '. _AT('mb');
	} else if ($abs_num_bytes >= AT_KBYTE_SIZE) {
		return round(bytes_to_kilobytes($num_bytes), 2) .' '._AT('kb') ;
	}
	// else:

	return $num_bytes . ' '._AT('bt');
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
				
				$dir_option .= '<option value="'.$cur_dir.$file.'/" >'.$indent.$label.'</option>';

				$dir_option .= output_dirs($current_path,$cur_dir.$file.'/',$indent.'--');
			}
			
		} // end while	
		
		closedir($dir);	
	}
	return $dir_option;
}

function display_tree($current_path, $cur_dir, $pathext, $ignore_children = false) {
	// open the cur_dir
	static $list_array;
	if (!isset($list_array)) {
		$list_array = explode(',', $_GET['list']);
	}
	if ($dir = opendir($current_path . $cur_dir)) {

		// recursively call output_dirs() for all directories in this directory
		while (false !== ($file = readdir($dir)) ) {

			//if the name is not a directory 
			if( ($file == '.') || ($file == '..') ) {
				continue;
			}

			// if it is a directory call function
			if (is_dir($current_path . $cur_dir . $file)) {

				//$ldir = explode('/',$cur_dir.$file);
				//$count = count($ldir);
				//$label = $ldir[$count-1];

				$check = '';
				$here  = '';
				if ($cur_dir . $file == substr($pathext, 0, -1)) {
					$check = 'checked="checked"';
					$here = ' ' . _AT('current_location');
				} else if (($cur_dir == $pathext) && in_array($file, $list_array)) {
					$ignore_children = true;
				}

				if ($ignore_children) {
					$check = 'disabled="disabled"';
					$class = ' disabled';
				}

				$dir_option .= '<ul><li class="folders'.$class.'">';
				$dir_option .= '<label><input type="radio" name="dir_name" value="'.$cur_dir.$file.'" '.$check. '/>'. $file . $here. '</label>';
				$dir_option .= ''.display_tree($current_path,$cur_dir.$file.'/', $pathext, $ignore_children).'';
				$dir_option .= '</li></ul>';

				if (($cur_dir == $pathext) && in_array($file, $list_array)) {
					$ignore_children = false;
					$class = '';
				}
			}

			
		} // end while	
		
		closedir($dir);	
	}
	return $dir_option;
}

function course_realpath($file) {
	if (!$_SESSION['course_id']) {
		return FALSE;
	}
	
	$course_path = AT_CONTENT_DIR . $_SESSION['course_id'];
	
	$path_parts = pathinfo($file);
	
	$dir_name   = $path_parts['dirname'];
	$file_name  = $path_parts['basename'];
	$ext_name   = $path_parts['extension'];

	//1. determine the real path of the file/directory
	if (is_dir($dir_name.DIRECTORY_SEPARATOR.$file_name) && $ext_name == '') {
		//if directory ws passed through (moving file to diff directory)
		$real = realpath($dir_name . DIRECTORY_SEPARATOR . $file_name);
	} else {
		//if file was passed through or no existant direcotry was passed through (rename/creating dir)
		$real = realpath($dir_name);
	}

	//2. and whether its in the course content directory
	if (substr($real, 0, strlen($course_path)) != $course_path) {
		return FALSE;
	}

	//3. check if extensions are legal

	//4. Otherwise return the real path of the file
	return $real;
}

/**
* Returns canonicalized absolute pathname to a file/directory in the content directory
* @access public
* @param  string $file the relative path to the file or directory
* @return  string	the full path to the file or directory, FALSE if it does not exist in our content directory.
*/
function course_realpath_NEW_VERSION($file) {
	if (!$_SESSION['course_id']) {
		return FALSE;
	}
	
	$course_path = AT_CONTENT_DIR . $_SESSION['course_id'];

	// determine the real path of the file/directory
	$real = realpath($course_path . DIRECTORY_SEPARATOR . $file);
	
	if (!file_exists($real)) {
		// the file or directory does not exist
		return FALSE;

	} else if (substr($real, 0, strlen($course_path)) != $course_path) {
		// the file or directory is not in the content path
		return FALSE;

	} else {
		// Otherwise return the real path of the file
		return $real;
	}
}

/**
* Returns the name of the readme file in the given directory
* @access public
* @param  string $dir_name the name of the directory
* @return  string	the name of the readme file
*/
function get_readme($dir)
{
	if (!is_dir($dir)) return '';
	
	$dh = opendir($dir);
	
	while (($file = readdir($dh)) !== false) {
		if (stristr($file, 'readme') && substr($file, -4) <> '.php')
			return $file;
	}
	
	closedir($dh);
	return '';
}
?>