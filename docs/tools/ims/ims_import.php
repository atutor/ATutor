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
// $Id: ims_import.php,v 1.19 2004/05/06 15:10:26 joel Exp $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); /* for clr_dir() and preImportCallBack and dirsize() */
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');

/* make sure we own this course that we're exporting */
authenticate(AT_PRIV_CONTENT);


/* to avoid timing out on large files */
@set_time_limit(0);
$_SESSION['done'] = 1;

$package_base_path = '';
$element_path = array();

	/* called at the start of en element */
	/* builds the $path array which is the path from the root to the current element */
	function startElement($parser, $name, $attrs) {
		global $items, $path, $package_base_path;
		global $element_path;

		if (($name == 'item') && ($attrs['identifierref'] != '')) {
			$path[] = $attrs['identifierref'];
		} else if (($name == 'resource') && is_array($items[$attrs['identifier']]))  {
			$items[$attrs['identifier']]['href'] = $attrs['href'];

			$temp_path = pathinfo($attrs['href']);
			$temp_path = explode('/', $temp_path['dirname']);

			if ($package_base_path == '') {
				$package_base_path = $temp_path;
			} else {
				$package_base_path = array_intersect($package_base_path, $temp_path);
			}

			$items[$attrs['identifier']]['new_path'] = implode('/', $temp_path);
		}
		array_push($element_path, $name);
	}

	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		global $path, $element_path, $my_data;

		if ($name == 'item') {
			array_pop($path);
		}

		if ($element_path == array('manifest', 'metadata', 'imsmd:lom', 'imsmd:general', 'imsmd:title', 'imsmd:langstring')) {
			global $package_base_name;
			$package_base_name = trim($my_data);
		}

		array_pop($element_path);
		$my_data = '';
	}

	/* called when there is character data within elements */
	/* constructs the $items array using the last entry in $path as the parent element */
	function characterData($parser, $data){
		global $path, $items, $order, $my_data;

		$str_trimmed_data = trim($data);
				
		if (!empty($str_trimmed_data)) {
			$size = count($path);
			if ($size > 0) {
				$current_item_id = $path[$size-1];
				if ($size > 1) {
					$parent_item_id = $path[$size-2];
				} else {
					$parent_item_id = 0;
				}
				if (is_array($items[$current_item_id])) {

					/* this item already exists, append the title		*/
					/* this fixes {\n, \t, `, &} characters in elements */
					$items[$current_item_id]['title'] .= $data;

				} else {
					$order[$parent_item_id] ++;

					$items[$current_item_id] = array('title'			=> $data,
													'parent_content_id' => $parent_item_id,
													'ordering'			=> $order[$parent_item_id]-1);
				}
			}
		}


		$my_data .= $data;

	}


if (!isset($_POST['submit'])) {
	/* just a catch all */
	header('Location: ../index.php?f='.AT_FEEDBACK_IMPORT_CANCELLED);
	exit;
}


$cid = intval($_POST['cid']);

if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
	if ($content = @file_get_contents($_POST['url'])) {

		// save file to /content/
		$filename = substr(time(), -6). '.zip';
		$full_filename = '../../content/' . $filename;

		if (!$fp = fopen($full_filename, 'w+b')) {
			echo "Cannot open file ($filename)";
			exit;
		}


		if (fwrite($fp, $content, strlen($content) ) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($fp);
	}	
	$_FILES['file']['name']     = $filename;
	$_FILES['file']['tmp_name'] = $full_filename;
	$_FILES['file']['size']     = strlen($content);
	unset($content);
	$url_parts = pathinfo($_POST['url']);
	$package_base_name_url = $url_parts['basename'];
}
$ext = pathinfo($_FILES['file']['name']);
$ext = $ext['extension'];

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('content_packaging');
$_section[1][1] = 'tools/ims/';
$_section[2][0] = _AT('import_content_package');
$_section[2][1] = 'tools/ims/';

if (   !$_FILES['file']['name'] 
	|| (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url']) 
	|| ($ext != 'zip'))
	{
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if ($_FILES['file']['size'] == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
			
	/* check if ../content/import/ exists */
	$import_path = '../../content/import/';
	$content_path = '../../content/';

	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors[] = AT_ERROR_IMPORTDIR_FAILED;
			print_errors($errors);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}

	$import_path .= $_SESSION['course_id'].'/';

	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors[] = AT_ERROR_IMPORTDIR_FAILED;
			print_errors($errors);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}

	/* extract the entire archive into ../../content/import/$course using the call back function to filter out php files */
	error_reporting(0);
	$archive = new PclZip($_FILES['file']['tmp_name']);
	if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
							PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		echo 'Error : '.$archive->errorInfo(true);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($import_path);
		exit;
	}
	error_reporting(E_ALL ^ E_NOTICE);


	/* get the course's max_quota */
	$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$q_row	= mysql_fetch_assoc($result);

	if ($q_row['max_quota'] != AT_COURSESIZE_UNLIMITED) {

		if ($q_row['max_quota'] == AT_COURSESIZE_DEFAULT) {
			$q_row['max_quota'] = $MaxCourseSize;
		}
		$totalBytes   = dirsize($import_path);
		$course_total = dirsize('../../content/'.$_SESSION['course_id'].'/');
		$total_after  = $q_row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

		if ($total_after < 0) {
			/* remove the content dir, since there's no space for it */
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors[] = array(AT_ERROR_NO_CONTENT_SPACE, number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
			print_errors($errors);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			clr_dir($import_path);
			exit;
		}
	}


	$items = array(); /* all the content pages */
	$order = array(); /* keeps track of the ordering for each content page */
	$path  = array();  /* the hierarchy path taken in the menu to get to the current item in the manifest */

	/*
	$items[content_id/resource_id] = array(
										'title'
										'real_content_id' // calculated after being inserted
										'parent_content_id'
										'href'
										'ordering'
										);
	*/


	$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

	if ($ims_manifest_xml === false) {
		require(AT_INCLUDE_PATH.'header.inc.php');

		$errors[] = AT_ERROR_NO_IMSMANIFEST;

		if (file_exists($import_path . 'atutor_backup_version')) {
			$errors[] = AT_ERROR_NO_IMS_BACKUP;
		}

		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($import_path);
		exit;
	}


	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');

	if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
		die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
	}

	xml_parser_free($xml_parser);

	/* generate a unique new package base path based on the package file name and date as needed. */
	/* the package name will be the dir where the content for this package will be put, as a result */
	/* the 'content_path' field in the content table will be set to this path. */
	/* $package_base_name_url comes from the URL file name (NOT the file name of the actual file we open)*/
	if (!$package_base_name && $package_base_name_url) {
		$package_base_name = substr($package_base_name_url, 0, -4);
	} else if (!$package_base_name) {
		$package_base_name = substr($_FILES['file']['name'], 0, -4);
	}
	$package_base_name = strtolower($package_base_name);
	$package_base_name = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $package_base_name);

	if (is_dir('../../content/'.$_SESSION['course_id'].'/'.$package_base_name)) {
		$package_base_name .= '_'.date('ymdHi');
	}

	$package_base_path = implode('/', $package_base_path);
	reset($items);


	/* get the top level content ordering offset */
	$sql	= "SELECT MAX(ordering) AS ordering FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id] AND content_parent_id=$cid";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);
	$order_offset = intval($row['ordering']); /* it's nice to have a real number to deal with */
	
	foreach ($items as $item_id => $content_info) {
		$file_info = @stat('../../content/import/'.$_SESSION['course_id'].'/'.$content_info['href']);
		if ($file_info === false) {
			continue;
		}
		
		$path_parts = pathinfo('../../content/import/'.$_SESSION['course_id'].'/'.$content_info['href']);
		$ext = strtolower($path_parts['extension']);

		$last_modified = date('Y-m-d H:i:s', $file_info['mtime']);
		if (in_array($ext, array('gif', 'jpg', 'bmp', 'png', 'jpeg'))) {
			/* this is an image */
			$content = '<img src="'.$content_info['href'].'" alt="'.$content_info['title'].'" />';
		} else if ($ext == 'swf') {
			/* this is flash */
            /* Using default size of 550 x 400 */

			$content = '<object type="application/x-shockwave-flash" data="' . $content_info['href'] . '" width="550" height="400"><param name="movie" value="'. $content_info['href'] .'" /></object>';

		} else if ($ext == 'mov') {
			/* this is a quicktime movie  */
            /* Using default size of 550 x 400 */

			$content = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="550" height="400" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="'. $content_info['href'] . '" /><param name="autoplay" value="true" /><param name="controller" value="true" /><embed src="' . $content_info['href'] .'" width="550" height="400" controller="true" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>';
		} else if ($ext == 'mp3') {
			$content = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="200" height="15" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="'. $content_info['href'] . '" /><param name="autoplay" value="false" /><embed src="' . $content_info['href'] .'" width="200" height="15" autoplay="false" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>';
		} else if (in_array($ext, array('wav', 'au'))) {
			$content = '<embed SRC="'.$content_info['href'].'" autostart="false" width="145" height="60"><noembed><bgsound src="'.$content_info['href'].'"></noembed></embed>';

		} else if (in_array($ext, array('txt', 'css', 'html', 'htm', 'csv', 'asc', 'tsv', 'xml', 'xsl'))) {
			/* this is a plain text file */
			$content = file_get_contents('../../content/import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			if ($content === false) {
				/* if we can't stat() it then we're unlikely to be able to read it */
				/* so we'll never get here. */
				continue;
			}
			$content = get_html_body($content);

			/* potential security risk? */
			if ( strpos($content_info['href'], '..') === false) {
				@unlink('../../content/import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			}
		} else {
			$content = '<a href="'.$content_info['href'].'">'.$content_info['title'].'</a>';
		}

		$content_parent_id = $cid;
		if ($content_info['parent_content_id'] !== 0) {
			$content_parent_id = $items[$content_info['parent_content_id']]['real_content_id'];
		}

		$my_offset = 0;
		if ($content_parent_id == $cid) {
			$my_offset = $order_offset;
		}

		/* replace the old path greatest common denomiator with the new package path. */
		/* we don't use str_replace, b/c there's no knowing what the paths may be	  */
		/* we only want to replace the first part of the path.						  */
		if ($package_base_path != '') {
			$content_info['new_path']	= $package_base_name . substr($content_info['new_path'], strlen($package_base_path));
		} else {
			$content_info['new_path'] = $package_base_name;
		}
		
		$content_info['title'] = sql_quote($content_info['title']);
		$content = sql_quote($content);

		$sql= 'INSERT INTO '.TABLE_PREFIX.'content VALUES 
				(0,	'
				.$_SESSION['course_id'].','															
				.$content_parent_id.','		
				.($content_info['ordering'] + $my_offset + 1).','
				.'"'.$last_modified.'",													
				0,1,NOW(),"","'.$content_info['new_path'].'",'
				.'"'.$content_info['title'].'",'
				.'"'.$content.'", 0)';

		$result = mysql_query($sql, $db);

		/* get the content id and update $items */
		$items[$item_id]['real_content_id'] = mysql_insert_id($db);
	}

	if ($package_base_path == '.') {
		$package_base_path = '';
	}
	rename('../../content/import/'.$_SESSION['course_id'].'/'.$package_base_path, '../../content/'.$_SESSION['course_id'].'/'.$package_base_name);
	clr_dir('../../content/import/'.$_SESSION['course_id']);

	if (isset($_POST['url'])) {
		@unlink($full_filename);
	}

if ($_POST['s_cid']){
	header('Location:../../editor/edit_content.php?cid='.$_POST['cid'].SEP.'f='.AT_FEEDBACK_IMPORT_SUCCESS);
	exit;
} else {
	if ($_GET['tile']) {
		header('Location: '.$_base_href.'resources/tile/index.php?f='.AT_FEEDBACK_IMPORT_SUCCESS);
	} else {
		header('Location: ./index.php?cid='.$_POST['cid'].SEP.'f='.AT_FEEDBACK_IMPORT_SUCCESS);
	}
	exit;
}

?>