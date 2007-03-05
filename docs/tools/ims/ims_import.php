<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
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
$xml_base_path = '';
$element_path = array();
$imported_glossary = array();

	/* called at the start of en element */
	/* builds the $path array which is the path from the root to the current element */
	function startElement($parser, $name, $attrs) {
		global $items, $path, $package_base_path;
		global $element_path;
		global $xml_base_path;
		static $current_identifier;

		if ($name == 'manifest' && isset($attrs['xml:base']) && $attrs['xml:base']) {
			$xml_base_path = $attrs['xml:base'];
		} else if ($name == 'file') {
			// special case for webCT content packages that don't specify the `href` attribute 
			// with the `<resource>` element.
			// we take the `href` from the first `<file>` element.
			if (isset($items[$current_identifier]) && ($items[$current_identifier]['href'] == '')) {
				$items[$current_identifier]['href'] = $attrs['href'];

				$items[$current_identifier]['href'] = $attrs['href'];

				$temp_path = pathinfo($attrs['href']);
				$temp_path = explode('/', $temp_path['dirname']);

				if ($package_base_path == '') {
					$package_base_path = $temp_path;
				} else {
					$package_base_path = array_intersect($package_base_path, $temp_path);
				}

				$items[$current_identifier]['new_path'] = implode('/', $temp_path);
			}
		} else if (($name == 'item') && ($attrs['identifierref'] != '')) {
			$path[] = $attrs['identifierref'];
		} else if (($name == 'item') && ($attrs['identifier'])) {
			$path[] = $attrs['identifier'];
		} else if (($name == 'resource') && is_array($items[$attrs['identifier']]))  {
			$current_identifier = $attrs['identifier'];

			if ($attrs['href']) {
				$items[$attrs['identifier']]['href'] = $attrs['href'];

				$temp_path = pathinfo($attrs['href']);
				$temp_path = explode('/', $temp_path['dirname']);
				if (!$package_base_path) {
					$package_base_path = $temp_path;
				} else {
					$package_base_path = array_intersect($package_base_path, $temp_path);
				}

				$items[$attrs['identifier']]['new_path'] = implode('/', $temp_path);
			}
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

		if ($element_path === array('manifest', 'metadata', 'imsmd:lom', 'imsmd:general', 'imsmd:title', 'imsmd:langstring')) {
			global $package_base_name;
			$package_base_name = trim($my_data);
		}

		array_pop($element_path);
		$my_data = '';
	}

	/* called when there is character data within elements */
	/* constructs the $items array using the last entry in $path as the parent element */
	function characterData($parser, $data){
		global $path, $items, $order, $my_data, $element_path;

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

					/* horible kludge to fix the <ns2:objectiveDesc xmlns:ns2="http://www.utoronto.ca/atrc/tile/xsd/tile_objective"> */
					/* from TILE */
					if ($element_path[count($element_path)-1] != 'ns1:objectiveDesc') {
						$items[$current_item_id]['title'] .= $data;
					}
	
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

	/* glossary parser: */
	function glossaryStartElement($parser, $name, $attrs) {
		global $element_path;

		array_push($element_path, $name);
	}

	/* called when an element ends */
	/* removed the current element from the $path */
	function glossaryEndElement($parser, $name) {
		global $element_path, $my_data, $imported_glossary;
		static $current_term;

		if ($element_path === array('glossary', 'item', 'term')) {
			$current_term = $my_data;

		} else if ($element_path === array('glossary', 'item', 'definition')) {
			$imported_glossary[trim($current_term)] = trim($my_data);
		}

		array_pop($element_path);
		$my_data = '';
	}

	function glossaryCharacterData($parser, $data){
		global $my_data;

		$my_data .= $data;
	}

if (!isset($_POST['submit']) && !isset($_POST['cancel'])) {
	/* just a catch all */
	
	$errors = array('FILE_MAX_SIZE', ini_get('post_max_size'));
	$msg->addError($errors);

	header('Location: ./index.php');
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('IMPORT_CANCELLED');

	header('Location: ./index.php');
	exit;
}

$cid = intval($_POST['cid']);

if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
	if ($content = @file_get_contents($_POST['url'])) {

		// save file to /content/
		$filename = substr(time(), -6). '.zip';
		$full_filename = AT_CONTENT_DIR . $filename;

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

if ($ext != 'zip') {
	$msg->addError('IMPORTDIR_IMS_NOTVALID');
} else if ($_FILES['file']['error'] == 1) {
	$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
	$msg->addError($errors);
} else if ( !$_FILES['file']['name'] || (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url'])) {
	$msg->addError('FILE_NOT_SELECTED');
} else if ($_FILES['file']['size'] == 0) {
	$msg->addError('IMPORTFILE_EMPTY');
} 

if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else {
		header('Location: index.php');
	}
	exit;
}

/* check if ../content/import/ exists */
$import_path = AT_CONTENT_DIR . 'import/';
$content_path = AT_CONTENT_DIR;

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}
}

$import_path .= $_SESSION['course_id'].'/';
if (is_dir($import_path)) {
	clr_dir($import_path);
}

if (!@mkdir($import_path, 0700)) {
	$msg->addError('IMPORTDIR_FAILED');
}

if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else {
		header('Location: index.php');
	}
	exit;
}

/* extract the entire archive into AT_COURSE_CONTENT . import/$course using the call back function to filter out php files */
error_reporting(0);
$archive = new PclZip($_FILES['file']['tmp_name']);
if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
						PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
	$msg->addError('IMPORT_FAILED');
//	echo 'Error : '.$archive->errorInfo(true);
	clr_dir($import_path);
	header('Location: index.php');
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
	$course_total = dirsize(AT_CONTENT_DIR . $_SESSION['course_id'].'/');
	$total_after  = $q_row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

	if ($total_after < 0) {
		/* remove the content dir, since there's no space for it */
		$errors = array('NO_CONTENT_SPACE', number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
		$msg->addError($errors);
		
		clr_dir($import_path);

		if (isset($_GET['tile'])) {
			header('Location: '.$_base_path.'tools/tile/index.php');
		} else {
			header('Location: index.php');
		}
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
	$msg->addError('NO_IMSMANIFEST');

	if (file_exists($import_path . 'atutor_backup_version')) {
		$msg->addError('NO_IMS_BACKUP');
	}

	clr_dir($import_path);

	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else {
		header('Location: index.php');
	}
	exit;
}



// check if this is an eXe package
// NOTE: THIS NEEDS WORK! WHAT IS THE BEST WAY TO DETERMINE IF PACKAGE IS eXe?
// PERHAPS USE PARSER BELOW TO CHECK FOR ORGANIZATION?
$isExeContent = false;
$ims_organization_pos = strpos ($ims_manifest_xml, '<organization');
if ($ims_organization_pos !== false){
	$ims_organization_pos = strpos ($ims_manifest_xml, 'identifier', $ims_organization_pos);
	if ($ims_organization_pos !== false){
		$exe_pos = strpos ($ims_manifest_xml, 'eXenew', $ims_organization_pos);
		//if ($exe_pos < ($ims_organization_pos + '50')){
		if ($exe_pos !== false){
			$isExeContent = true;
		}
	}
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
//debug($items);

/* check if the glossary terms exist */
if (file_exists($import_path . 'glossary.xml')){
	$glossary_xml = @file_get_contents($import_path.'glossary.xml');
	$element_path = array();

	$xml_parser = xml_parser_create();

	/* insert the glossary terms into the database (if they're not in there already) */
	/* parse the glossary.xml file and insert the terms */
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
	xml_set_element_handler($xml_parser, 'glossaryStartElement', 'glossaryEndElement');
	xml_set_character_data_handler($xml_parser, 'glossaryCharacterData');

	if (!xml_parse($xml_parser, $glossary_xml, true)) {
		debug($glossary_xml);
		die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
	}
	xml_parser_free($xml_parser);
	$contains_glossary_terms = true;
	foreach ($imported_glossary as $term => $defn) {
		if (!$glossary[urlencode($term)]) {
			$sql = "INSERT INTO ".TABLE_PREFIX."glossary VALUES (NULL, $_SESSION[course_id], '$term', '$defn', 0)";
			mysql_query($sql, $db);	
		}
	}
}


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
$package_base_name = preg_replace("/[^A-Za-z0-9._\-]/", '', $package_base_name);

if (is_dir(AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$package_base_name)) {
	$package_base_name .= '_'.date('ymdHis');
}


if ($package_base_path) {
	$package_base_path = implode('/', $package_base_path);
}
if ($xml_base_path) {
	$package_base_path = $xml_base_path . $package_base_path;

	mkdir(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$xml_base_path);
	$package_base_name = $xml_base_path . $package_base_name;
}
reset($items);

/* get the top level content ordering offset */
$sql	= "SELECT MAX(ordering) AS ordering FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id] AND content_parent_id=$cid";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_assoc($result);
$order_offset = intval($row['ordering']); /* it's nice to have a real number to deal with */
	
	foreach ($items as $item_id => $content_info) {
		if (isset($content_info['href'], $xml_base_path)) {
			$content_info['href'] = $xml_base_path . $content_info['href'];
		}
		if (!isset($content_info['href'])) {
			// this item doesn't have an identifierref. so create an empty page.
			$content = '';
			$ext = '';
			$last_modified = date('Y-m-d H:i:s');
		} else {
			$file_info = @stat(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			if ($file_info === false) {
				continue;
			}
		
			$path_parts = pathinfo(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			$ext = strtolower($path_parts['extension']);

			$last_modified = date('Y-m-d H:i:s', $file_info['mtime']);
		}
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
			$content = file_get_contents(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			if ($content === false) {
				/* if we can't stat() it then we're unlikely to be able to read it */
				/* so we'll never get here. */
				continue;
			}

			// get the contents of the 'head' element
			$head = get_html_head($content);
			$content = get_html_body($content);
			if ($contains_glossary_terms) {
				// replace glossary content package links to real glossary mark-up using [?] [/?]
				$content = preg_replace('/<a href="([.\w\d\s]+[^"]+)" target="body" class="at-term">([.\w\d\s&;"]+)<\/a>/i', '[?]\\2[/?]', $content);
			}

			/* potential security risk? */
			if ( strpos($content_info['href'], '..') === false) {
				@unlink(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$content_info['href']);
			}
		} else if ($ext) {
			/* non text file, and can't embed (example: PDF files) */
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
		
		$content_info['title'] = addslashes($content_info['title']);
		$content = addslashes($content);

		// add a 'div' around eXe content
		if ($isExeContent == true){
			$content = '<div class=\"execontent\">\n'.$content.'\n</div>';
		}

		$sql= 'INSERT INTO '.TABLE_PREFIX.'content VALUES 
				(NULL,	'
				.$_SESSION['course_id'].','															
				.$content_parent_id.','		
				.($content_info['ordering'] + $my_offset + 1).','
				.'"'.$last_modified.'",													
				0,1,NOW(),"","'.$content_info['new_path'].'",'
				.'"'.$content_info['title'].'",'
				.'"'.$content.'")';

		$result = mysql_query($sql, $db);

		// store the contents of the 'head' section
		/**
		if ($result){
			// get content ID
			$id = mysql_insert_id($db);
			$head = addslashes($head);

			$sql = 'INSERT INTO '.TABLE_PREFIX.'head VALUES (NULL, '.$_SESSION['course_id'].','
				.$id.','
				.'"'.$head.'")';
			$resultHead = mysql_query($sql, $db);
		}
		**/

		/* get the content id and update $items */
		$items[$item_id]['real_content_id'] = mysql_insert_id($db);
	}

	if ($package_base_path == '.') {
		$package_base_path = '';
	}

	if (@rename(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$package_base_path, AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$package_base_name) === false) {
		if (!$msg->containsErrors()) {
			$msg->addError('IMPORT_FAILED');
		}
	}
	clr_dir(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id']);

	if (isset($_POST['url'])) {
		@unlink($full_filename);
	}


if ($_POST['s_cid']){
	if (!$msg->containsErrors()) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: ../../editor/edit_content.php?cid='.intval($_POST['cid']));
	exit;
} else {
	if (!$msg->containsErrors()) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	if ($_GET['tile']) {
		header('Location: '.AT_BASE_HREF.'tools/tile/index.php');
	} else {
		header('Location: ./index.php?cid='.intval($_POST['cid']));
	}
	exit;
}

?>