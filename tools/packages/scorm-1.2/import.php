<?php
/*
 * tools/packages/scorm-1.2/import.php
 *
 * This file is part of ATutor, see http://www.atutor.ca
 * 
 * Copyright (C) 2005  Matthai Kurian 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

define('AT_INCLUDE_PATH', '../../../include/');
if (!isset ($_POST['type'])) {
	require(AT_INCLUDE_PATH.'vitals.inc.php');
} 

@set_time_limit(0);
$_SESSION['done'] = 1;

require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); 
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');

authenticate(AT_PRIV_CONTENT);


function chmodPackageDir ($path) {

	if (!is_dir($path)) return;
	else chmod ($path, 0755);

	$h = opendir($path);
	while ($f = readdir($h)) {
		if ($f == '.' || $f == '..') continue;
		$fpath = $path.'/'.$f;
		if (!is_dir($fpath)) {
   			chmod ($fpath, 0644);
		} else {
			chmodPackageDir ($fpath);
		}
	}
	closedir ($h);
}

$package_base_path = '';

$idx      = '';		// the current item's index, 1, 1.1, 1.2, 2, 2.1 ...
$idxs     = array();	// array containing the idx for all items
$orgid    = 0;		// index of current organization 1...
$depth    = 0;		// depth in organization tree
$itemid   = array();	
$files    = array();
$orgitems = array();
$idxs     = array();
$text;
$res;
$ress     = array();
$files    = array();
$finfo;
$totalsize = 0;

if (!isset($_POST['submit'])) {
	$msg->addFeedback('IMPORT_CANCELLED');
	header('Location: ../index.php');
	exit;
}

$cid = intval($_POST['cid']);


if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
	if ($content = @file_get_contents($_POST['url'])) {
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

if ($_FILES['file']['error'] == 1) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
	$msg->printErrors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (!$_FILES['file']['name'] 
	|| (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url']) 
	|| ($ext != 'zip')) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('FILE_NOT_SELECTED');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if ($_FILES['file']['size'] == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('IMPORTFILE_EMPTY');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
			
	$package_path = AT_INCLUDE_PATH . '../sco/';

	if (!is_dir($package_path)) {
		if (!@mkdir($package_path, 0755)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('PACKAGE_DIR_FAILED');
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
		chmod ($package_path, 0755);
	}

	$package_path .= $_SESSION['course_id'].'/';
	if (!is_dir($package_path)) {
		if (!@mkdir($package_path, 0755)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('PACKAGE_DIR_FAILED');
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
		chmod ($package_path, 0755);
	}

	$package_path .= 'tmp/';
	clr_dir($package_path);
	if (!is_dir($package_path)) {
		if (!@mkdir($package_path, 0755)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('PACKAGE_DIR_FAILED');
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
		chmod ($package_path, 0755);
	}

	$archive = new PclZip($_FILES['file']['tmp_name']);
	if ($archive->extract (PCLZIP_OPT_PATH, $package_path) == 0) {

		require(AT_INCLUDE_PATH.'header.inc.php');
		echo 'Error : '.$archive->errorInfo(true);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($package_path);
		exit;
	}

	chmodPackageDir ($package_path);

	$sql	= "SELECT max_quota
		   FROM ".TABLE_PREFIX."courses
		   WHERE  course_id=$_SESSION[course_id]";

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
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors = array('NO_CONTENT_SPACE', number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
			$msg->printErrors($errors);
			
			require(AT_INCLUDE_PATH.'footer.inc.php');
			clr_dir($import_path);
			exit;
		}
	}


parseManifest ($package_path);
doValidation();
doImport();


if (isset($_POST['url'])) {
      @unlink($full_filename);
}
$orgs = array();
for ($i=1; $orgitems[$i]; $i++) {
	array_push ($orgs, $orgitems[$i]['title']);
}
$oc = sizeOf($orgs);
if ($oc == 1)  {
	$msg->addFeedback(array('PACKAGE_IMPORT_SUCCESS', $orgs[0]));
} else {
	$l = '';
	for ($i=0; $i<$oc; $i++) {
		$l .= '<li>' . $orgs[$i] . '</li>';
	}
	$msg->addFeedback(array('PACKAGES_IMPORT_SUCCESS', $l));
}

header('Location: ./index.php');
exit;


function parseManifest ($import_path) {
	global $msg;

	$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

	if ($ims_manifest_xml === false) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->addError('NO_IMSMANIFEST');
		$msg->printErrors();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($import_path);
		exit;
	}

	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');
	
	if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
		die(sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
	}
	
	xml_parser_free($xml_parser);
}

function scormType ($i) {
	global $idxs, $orgitems, $res;
	$r = $res[$orgitems[$idxs[$i]]['identifierref']]['adlcp:scormtype'];
	if ($r) return $r;
	$o = explode ('.', $idxs[$i]);
	if (sizeOf($o) > 1) return 'cluster';
	return 'organization';  
}  

function doValidation () {
	global $msg;
	global $orgitems;
	global $idxs;
	global $res;
	global $package_path;

	$ic = sizeOf ($idxs);

	$err  = 0;
	$warn = 0;

	for ($i=0; $i<$ic; $i++) {
		$title = addslashes($orgitems[$idxs[$i]]['title']);

		$href = $res[$orgitems[$idxs[$i]]['identifierref']]['href'];
		$styp = $res[$orgitems[$idxs[$i]]['identifierref']]['adlcp:scormtype'];
		$pre  = $orgitems[$idxs[$i]]['adlcp:prerequisites'];
		$max  = $orgitems[$idxs[$i]]['adlcp:maxtimeallowed'];
		$act  = $orgitems[$idxs[$i]]['adlcp:timelimitaction'];
		$lms  = $orgitems[$idxs[$i]]['adlcp:datafromlms'];
		$mas  = $orgitems[$idxs[$i]]['adlcp:masteryscore'];

		if ($idxs[$i].'.1' == $idxs[$i+1]) { // cluster
			if ($href != '' && ++$warn)
			    $msg->addWarning ('SCORM_ITEM_CLUSTER_HAS_OBJECT');
		} else { 
			if ($styp == '' && ++$err)
			    $msg->addError ('SCORM_ITEM_SCORMTYPE_MISSING');
			if ($href == '' && ++$err)
			    $msg->addError ('SCORM_ITEM_HREF_MISSING');
		}

	}
	if ($err) {
		header('Location: ./index.php');
		exit;
	}

}


function doImport () {
	global $db;
	global $msg;
	global $orgitems;
	global $idxs;
	global $res;
	global $package_path;

	$now = date('Y-m-d H:i:s');
	$file = $_FILES['file']['name'];
	$sql = "INSERT INTO ".TABLE_PREFIX."packages
	        VALUES (
			NULL,
			'$file',
			'$now',
			$_SESSION[course_id],
			'scorm-1.2'
		)";

	$result = mysql_query($sql, $db);
	if (!$result) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->addError('DB_NOT_UPDATED');
		$msg->printAll();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} 

	$pkg = mysql_insert_id($db);
	rename ($package_path, dirname($package_path) . '/' . $pkg);

	$ic = sizeOf ($idxs);

	for ($i=0; $i<$ic; $i++) {
		$title = addslashes($orgitems[$idxs[$i]]['title']);
		$scormtype = scormType($i);

		switch ($scormtype) {
		case 'organization':
			$sql = "INSERT INTO ".TABLE_PREFIX."scorm_1_2_org (
					package_id, title
				) VALUES ( $pkg, '$title')";

			$result = mysql_query($sql, $db);
			if (!$result) {
				require(AT_INCLUDE_PATH.'header.inc.php');
				$msg->addError('DB_NOT_UPDATED');
				$msg->printAll();
				require(AT_INCLUDE_PATH.'footer.inc.php');
				exit;
			}
			$orgid = mysql_insert_id($db);
			$sql = "INSERT INTO ".TABLE_PREFIX."scorm_1_2_item
				VALUES (
					0,
					$orgid,
					'$idxs[$i]',
					'$title',
					'',
					'$scormtype',
					'', '', '', '', ''
				)";
			$result = mysql_query($sql, $db);
			break;

		case 'sco':
			if (!$orgitems[$idxs[$i]]['adlcp:timelimitaction'])
				$orgitems[$idxs[$i]]['adlcp:timelimitaction'] =
			          	'continue, no message';
		case 'asset':
		case 'cluster':
			$href = $res[$orgitems[$idxs[$i]]['identifierref']]['href'];
			$pre  = $orgitems[$idxs[$i]]['adlcp:prerequisites'];
			$max  = $orgitems[$idxs[$i]]['adlcp:maxtimeallowed'];
			$act  = $orgitems[$idxs[$i]]['adlcp:timelimitaction'];
			$lms  = $orgitems[$idxs[$i]]['adlcp:datafromlms'];
			$mas  = $orgitems[$idxs[$i]]['adlcp:masteryscore'];
			$sql = "INSERT INTO ".TABLE_PREFIX."scorm_1_2_item
				VALUES (
					0,
					$orgid,
					'$idxs[$i]',
					'$title',
					'$href',
					'$scormtype',
					'$pre',
					'$max', '$act', '$lms', '$mas'
				)";
			$result = mysql_query($sql, $db);
			if (!$result) {
				require(AT_INCLUDE_PATH.'header.inc.php');
				$msg->addError('DB_NOT_UPDATED');
				$msg->printAll();
				require(AT_INCLUDE_PATH.'footer.inc.php');
				exit;
			}
		}
	}
}


function startElement($parser, $name, $h) {

	global $orgid, $itemid,  $depth;
	global $orgitems, $idx, $idxs;
	global $res, $ress;
	global $files, $finfo, $totalsize;

	switch ($name) {
		case 'organization':
				$orgid++;
		case 'item':
				$itemid[$depth++]++;
				$idx = implode ('.', $itemid);
				array_push ($idxs, $idx);
				while (list($l, $r) = each($h)) {
					$orgitems[$idx][$l]=$r;
				}
				break;
		case 'title':
				break;

		case 'resource':
				array_push ($ress, $h['identifier']);
				while (list($l, $r) = each($h)) {
					$res[$h['identifier']][$l]=$r;
				}
				break;
		case 'dependency':
				break;
		case 'file':
				array_push ($files, $h['href']);
				$f=AT_CONTENT_DIR
					.'import/'.$_SESSION['course_id']
					.'/'.$h['href'];
				$finfo[$h['href']] = @stat($f);
				$totalsize +=  $finfo[$h['href']]['size'];
				break;
	}
}

function endElement($parser, $name) {
	global $orgid, $idx, $itemid, $depth, $text, $orgitems;

	switch ($name) {
		case 'organization':
				$depth=0;
				$itemid = array ($orgid);
				break;
		case 'item':	
				while ($itemid[$depth]) {
					array_pop($itemid);
				}
				$depth--;
				break;
		case 'title':
		case 'adlcp:datafromlms':
		case 'adlcp:maxtimeallowed':
		case 'adlcp:timelimitaction':
		case 'adlcp:prerequisites':
		case 'adlcp:masteryscore':
				$orgitems[$idx][$name] = trim($text);
				break;
		case 'resource':

	}
	$text = '';
}

function characterData($parser, $data){
	global $text;

	$text .= $data;
}

?>
