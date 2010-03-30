<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: ims_export.php 8211 2008-11-11 22:55:40Z hwong $
define('AT_INCLUDE_PATH', '../../../include/');

/* content id of an optional chapter */
$cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
$c   = isset($_REQUEST['c'])   ? intval($_REQUEST['c'])   : 0;

if (isset($_REQUEST['to_tile']) && !isset($_POST['cancel'])) {
	/* for TILE */
	require_once(AT_INCLUDE_PATH.'vitals.inc.php');
	if (!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		/* user can't be authenticated */
		header('HTTP/1.1 404 Not Found');
		echo 'Document not found.';
		exit;
	}
	
	/* to avoid timing out on large files */
	@set_time_limit(0);
	
	// oauth authentication. Get oauth access token: $access_token_key
	$client_callback_url = AT_BASE_HREF.'mods/_core/imscc/ims_export.php?to_tile=1'.SEP.'cid='.$cid;
	include_once('../imscp/oauth/oauth_authenticate.php');

	$m = md5(DB_PASSWORD . 'x' . ADMIN_PASSWORD . 'x' . $_SERVER['SERVER_ADDR'] . 'x' . $cid . 'x' . $_SESSION['course_id'] . 'x' . date('Ymd'));

	$tile_import_url = AT_TILE_IMPORT_URL. '?oauth_token='.$access_token_key.'&url='.urlencode(AT_BASE_HREF. 'mods/_core/imscc/ims_export.php?cid='.$cid.'&c='.$_SESSION['course_id'].'&m='.$m);

	$oauth_server_response = @file_get_contents($tile_import_url);
	
	// handle OAUTH import response
	foreach (explode('&', $oauth_server_response) as $rtn)
	{
		$rtn_pair = explode('=', $rtn);
		
		if ($rtn_pair[0] == 'course_id') $tile_course_id = $rtn_pair[1];
		if ($rtn_pair[0] == 'error') $error = $rtn_pair[1];
	}
	
	if ($tile_course_id > 0)
		$msg->addFeedback(array('TILE_IMPORT_SUCCESS', AT_TILE_VIEW_COURSE_URL.$tile_course_id));
	else
	{
		// No response from transformable, the package file might be too big
		if (trim($error) == '') $error = _AT('tile_no_response');
		else {
			// delete this access token since it cannot import into Transformable
			$sql = "DELETE FROM ".TABLE_PREFIX."oauth_client_tokens
			         WHERE token = '".$access_token_key."'
			           AND token_type='access'";
			$result = mysql_query($sql, $db);
		}
		$msg->addError(array('TILE_IMPORT_FAIL', $error));
	}
	
	header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
	exit;
} else if (isset($_GET['m'])) {
	/* for TILE */

	/* request (hopefully) coming from a TILE server, send the content package */

	$_user_location = 'public';
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$m = md5(DB_PASSWORD . 'x' . ADMIN_PASSWORD . 'x' . $_SERVER['SERVER_ADDR'] . 'x' . $cid . 'x' . $c . 'x' . date('Ymd'));
	if (($m != $_GET['m']) || !$c) {
		header('HTTP/1.1 404 Not Found');
		echo 'Document not found.';
		exit;
	}
	
	$course_id = $c;
} else {
	$use_a4a = false;
	if (isset($_REQUEST['to_a4a'])){
		$use_a4a = true;
	} 
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$course_id = $_SESSION['course_id'];
}
//load the following after vitals is included
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4aExport.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/imscc/classes/Weblinks.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/imscc/classes/WeblinksExport.class.php');

$use_cc			 = true;
$instructor_id   = $system_courses[$course_id]['member_id'];
$course_desc     = htmlspecialchars($system_courses[$course_id]['description'], ENT_QUOTES, 'UTF-8');
$course_title    = htmlspecialchars($system_courses[$course_id]['title'], ENT_QUOTES, 'UTF-8');
$course_language = $system_courses[$course_id]['primary_language'];

$courseLanguage =& $languageManager->getLanguage($course_language);
//If course language cannot be found, use UTF-8 English
//@author harris, Oct 30,2008
if ($courseLanguage == null){
	$courseLanguage =& $languageManager->getLanguage('en');
}

$course_language_charset = $courseLanguage->getCharacterSet();
$course_language_code = $courseLanguage->getCode();

require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
require(AT_INCLUDE_PATH.'classes/vcard.php');						/* for vcard */
require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
require(AT_INCLUDE_PATH.'../mods/_core/imscc/include/ims_template.inc.php');	/* for ims templates + print_organizations() */

if (isset($_POST['cancel'])) {
	$msg->addFeedback('EXPORT_CANCELLED');
	header('Location: ../index.php');
	exit;
}


$zipfile = new zipfile(); 
$zipfile->create_dir('resources/');

/*
	the following resources are to be identified:
	even if some of these can't be images, they can still be files in the content dir.
	theoretically the only urls we wouldn't deal with would be for a <!DOCTYPE and <form>

	img		=> src
	a		=> href				// ignore if href doesn't exist (ie. <a name>)
	object	=> data | classid	// probably only want data
	applet	=> classid | archive			// whatever these two are should double check to see if it's a valid file (not a dir)
	link	=> href
	script	=> src
	form	=> action
	input	=> src
	iframe	=> src

*/
class MyHandler {
    function MyHandler(){}
    function openHandler(& $parser,$name,$attrs) {
		global $my_files;

		$name = strtolower($name);
		$attrs = array_change_key_case($attrs, CASE_LOWER);

		$elements = array(	'img'		=> 'src',
							'a'			=> 'href',				
							'object'	=> array('data', 'classid'),
							'applet'	=> array('classid', 'archive'),
							'link'		=> 'href',
							'script'	=> 'src',
							'form'		=> 'action',
							'input'		=> 'src',
							'iframe'	=> 'src',
							'embed'		=> 'src',
							'param'		=> 'value');
	
		/* check if this attribute specifies the files in different ways: (ie. java) */
		if (is_array($elements[$name])) {
			$items = $elements[$name];

			foreach ($items as $item) {
				if ($attrs[$item] != '') {

					/* some attributes allow a listing of files to include seperated by commas (ie. applet->archive). */
					if (strpos($attrs[$item], ',') !== false) {
						$files = explode(',', $attrs[$item]);
						foreach ($files as $file) {
							$my_files[] = trim($file);
						}
					} else {
						$my_files[] = $attrs[$item];
					}
				}
			}
		} else if (isset($elements[$name]) && ($attrs[$elements[$name]] != '')) {
			/* we know exactly which attribute contains the reference to the file. */
			$my_files[] = $attrs[$elements[$name]];
		}
    }
    function closeHandler(& $parser,$name) { }
}

/* get all the content */
$content = array();
$paths	 = array();
$top_content_parent_id = 0;

$handler=new MyHandler();
$parser = new XML_HTMLSax();
$parser->set_object($handler);
$parser->set_element_handler('openHandler','closeHandler');

if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$course_id ORDER BY content_parent_id, ordering";
} else {
	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$course_id ORDER BY content_parent_id, ordering";
}
$cid = $_REQUEST['cid'];  //takes care of some system which lost the REQUEST[cid]
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) || $contentManager->isReleased($row['content_id']) === TRUE) {
		$content[$row['content_parent_id']][] = $row;
		if ($cid == $row['content_id']) {
			$top_content = $row;
			$top_content_parent_id = $row['content_parent_id'];
		}
	}
}

if ($cid) {
	/* filter out the top level sections that we don't want */
	$top_level = $content[$top_content_parent_id];
	foreach($top_level as $page) {
		if ($page['content_id'] == $cid) {
			$content[$top_content_parent_id] = array($page);
		} else {
			/* this is a page we don't want, so might as well remove it's children too */
			unset($content[$page['content_id']]);
		}
	}
	$ims_course_title = $course_title . ' - ' . $content[$top_content_parent_id][0]['title'];
} else {
	$ims_course_title = $course_title;
}

$imsmanifest_xml = str_replace(array('{COURSE_TITLE}', '{COURSE_DESCRIPTION}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}'), 
							  array($ims_course_title, $course_desc, $course_language_charset, $course_language_code),
							  $ims_template_xml['header']);
//debug($imsmanifest_xml);
//exit;

/* get the first content page to default the body frame to */
$first = $content[$top_content_parent_id][0];

$test_ids = array();	//global array to store all the test ids

//TODO**************BOLOGNA***************REMOVE ME***************************/
//Exoprt Forum:
global $forum_list;
$forum_list = array();

/* generate the resources and save the HTML files */
$used_glossary_terms = array();
ob_start();
print_organizations($top_content_parent_id, $content, 0, '', array(), $toc_html);

//Exoprt Forum:
print_resources_forum();

$organizations_str = ob_get_contents();
ob_end_clean();
if (count($used_glossary_terms)) {
	$used_glossary_terms = array_unique($used_glossary_terms);
	sort($used_glossary_terms);
	reset($used_glossary_terms);
	$terms_xml = '';
	foreach ($used_glossary_terms as $term) {
		$term_key = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');
		$glossary[$term_key] = htmlentities($glossary[$term_key], ENT_QUOTES, 'UTF-8');
		$glossary[$term_key] = str_replace('&', '&amp;', $glossary[$term_key]);
		$terms_xml .= str_replace(	array('{TERM}', '{DEFINITION}'),
									array($term_key, $glossary[$term_key]),
									$glossary_term_xml);
	}
	$glossary_xml = str_replace(array('{GLOSSARY_TERMS}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}'),
							    array($terms_xml, $course_language_charset),
								$glossary_xml);
	//add to resource
	$resources .= $ims_template_xml['resource_glossary'];
} else {
	unset($glossary_xml);
}

if ($glossary_xml){
	$glossary_manifest_xml = $ims_template_xml['glossary'];
} else {
	$glossary_manifest_xml = '';
}
/* append the Organizations and Resources to the imsmanifest */
$imsmanifest_xml .= str_replace(	array('{ORGANIZATIONS}', '{GLOSSARY}',	'{RESOURCES}', '{TEST_ITEMS}', '{COURSE_TITLE}'),
									array($organizations_str, $glossary_manifest_xml,	$resources, $test_xml_items, $ims_course_title),
									$ims_template_xml['final']);

/* generate the vcard for the instructor/author */
$sql = "SELECT first_name, last_name, email, website, login, phone FROM ".TABLE_PREFIX."members WHERE member_id=$instructor_id";
$result = mysql_query($sql, $db);
$vcard = new vCard();
if ($row = mysql_fetch_assoc($result)) {
	$vcard->setName($row['last_name'], $row['first_name'], $row['login']);
	$vcard->setEmail($row['email']);
	$vcard->setNote('Originated from an ATutor at '.AT_BASE_HREF.'. See ATutor.ca for additional information.');
	$vcard->setURL($row['website']);

	$imsmanifest_xml = str_replace('{VCARD}', $vcard->getVCard(), $imsmanifest_xml);
} else {
	$imsmanifest_xml = str_replace('{VCARD}', '', $imsmanifest_xml);
}

/* save the imsmanifest.xml file */
$zipfile->add_file($imsmanifest_xml, 'imsmanifest.xml');

if ($glossary_xml) {
	$zipfile->create_dir('resources/GlossaryItem/');
	$zipfile->add_file($glossary_xml,  'resources/GlossaryItem/glossary.xml');
}
$zipfile->close(); // this is optional, since send_file() closes it anyway

$ims_course_title = str_replace(array(' ', ':'), '_', $ims_course_title);
/**
 * A problem here with the preg_replace below.
 * Originally was designed to remove all werid symbols to avoid file corruptions.
 * In UTF-8, all non-english chars are considered to be 'werid symbols'
 * We can still replace it as is, or add fileid to the filename to avoid these problems
 * Well then again people won't be able to tell what this file is about
 * If we are going to take out the preg_replace, some OS might not be able to understand
 * these characters and will have problems importing.
 */
$ims_course_title = preg_replace("{[^a-zA-Z0-9._-]}","", trim($ims_course_title));
$zipfile->send_file($ims_course_title . '_imscc');

exit;
?>
