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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/format_content.inc.php');	/* for format_content() */
require(AT_INCLUDE_PATH.'lib/forum_codes.inc.php');		/* for format_final_output() */
require(AT_INCLUDE_PATH.'classes/zipfile.class.php');	/* for zipfile */
require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
require(AT_INCLUDE_PATH.'ims/ims_template.inc.php');		/* for ims templates + print_organizations() */

if (isset($_POST['cancel'])) {
	header('Location: ../index.php?f='.AT_FEEDBACK_EXPORT_CANCELLED);
	exit;
}

/* content id of an optional chapter */
$cid = intval($_REQUEST['cid']);

$ims_course_title = str_replace(' ', '_', $_SESSION['course_title']);
$full_course_title = $_SESSION['course_title'];

/* generate the imsmanifest.xml header attributes */
$imsmanifest_xml = str_replace('{COURSE_TITLE}', $ims_course_title, $ims_template_xml['header']);

$zipfile = new zipfile(); 

class MyHandler {
    function MyHandler(){}
    function openHandler(& $parser,$name,$attrs) {
		global $my_files;

		$elements = array(	'img'		=> 'src',
							'a'			=> 'href',				
							'object'	=> 'data',
							'applet'	=> 'classid',
							'link'		=> 'href',
							'script'	=> 'src',
							'form'		=> 'action',
							'input'		=> 'src',
							'iframe'	=> 'src',
							'embed'		=> 'src',
							'param'		=> 'value');
	
		if (isset($elements[$name]) && ($attrs[$elements[$name]] != '')) {
			$my_files[] = $attrs[$elements[$name]];
		}
			/*
			the following resources are to be identified:
			even if some of these can't be images, they can still be files in the content dir.
			theoretically the only urls we wouldn't deal with would be for a <!DOCTYPE and <form>

			img		=> src
			a		=> href				// ignore if href doesn't exist (ie. <a name>)
			object	=> data | classid	// probably only want data
			applet	=> classid			// whatever these two are should double check to see if it's a valid file (not a dir)
			link	=> href
			script	=> src
			form	=> action
			input	=> src
			iframe	=> src
		*/

    }
    function closeHandler(& $parser,$name) { }
}

/* get all the content */
$content = array();
$paths	 = array();
$top_content_parent_id = 0;

$handler=new MyHandler();
$parser =& new XML_HTMLSax();
$parser->set_object($handler);
$parser->set_element_handler('openHandler','closeHandler');

if ($_SESSION['is_admin']) {
	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id] ORDER BY content_parent_id, ordering";
} else {
	$sql = "SELECT *, UNIX_TIMESTAMP(last_modified) AS u_ts FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id] AND release_date<=NOW() ORDER BY content_parent_id, ordering";
}
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$content[$row['content_parent_id']][] = $row;
	if ($cid == $row['content_id']) {
		$top_content = $row;
		$top_content_parent_id = $row['content_parent_id'];
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
	$ims_course_title .= '-'.str_replace(' ', '_', $content[$top_content_parent_id][0]['title']);
	$full_course_title .= ': '.$content[$top_content_parent_id][0]['title'];
}

/* get the first content page to default the body frame to */
$first = $content[$top_content_parent_id][0];

/* generate the resources and save the HTML files */
$old_pref = $_SESSION['prefs'][PREF_CONTENT_ICONS];
$_SESSION['prefs'][PREF_CONTENT_ICONS] = 2;

unset($learning_concept_tags);
ob_start();
print_organizations($top_content_parent_id, $content, 0, '', array(), $toc_html);
$organizations_str = ob_get_contents();
ob_clean();

/* restore old pref */
$_SESSION['prefs'][PREF_CONTENT_ICONS] = $old_pref;

$toc_html = str_replace('{TOC}', $toc_html, $html_toc);

if ($first['content_path']) {
	$first['content_path'] .= '/';
}
$frame = str_replace(	array('{COURSE_TITLE}',		'{FIRST_ID}', '{PATH}'),
						array($ims_course_title, $first['content_id'], $first['content_path']),
						$html_frame);

$html_mainheader = str_replace('{COURSE_TITLE}', $full_course_title, $html_mainheader);


/* append the Organizations and Resources to the imsmanifest */
$imsmanifest_xml .= str_replace(	array('{ORGANIZATIONS}',	'{RESOURCES}'),
									array($organizations_str,	$resources),
									$ims_template_xml['final']);

/* save the imsmanifest.xml file */

$zipfile->add_file($frame, 'index.html');
$zipfile->add_file($toc_html, 'toc.html');
$zipfile->add_file($imsmanifest_xml, 'imsmanifest.xml');
$zipfile->add_file($html_mainheader, 'header.html');
$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/ims.css'), 'ims.css');
$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/footer.html'), 'footer.html');
$zipfile->add_file(file_get_contents('../../images/logo.gif'), 'logo.gif');


/* zip the entire ims export directory and send to the user */
/* create the archive */

header('Content-Type: application/octet-stream');
header('Content-transfer-encoding: binary'); 
header('Content-Disposition: attachment; filename="'.escapeshellcmd($ims_course_title).'_ims.zip"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

echo $zipfile->file();

exit;

?>