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
// $Id: ims_template.inc.php 8935 2009-11-17 17:36:09Z hwong $

if (!defined('AT_INCLUDE_PATH')) { exit; }

// This function gets files from html tag @import
function get_import_files($text)
{
	$text = strtolower($text);
	$tag = '@import';
	$files = array();
	
	while (strpos(strtolower($text), strtolower($tag)) > 0)
	{
		$start_pos	= strpos($text, $tag);
		if ($start_pos !== false) 
		{
			$text = substr($text, $start_pos);
			$start_pos = strlen($tag);
			$len = strpos($text, ';') - strlen($tag);
			
			$file = substr(trim($text), $start_pos, $len);
			
			// remove these characters from file name: url, (, ), ", '
			$file = trim(preg_replace('/(\'|\"|url|\(|\))/', '', $file));
			
			// strip processed tag
			$text = substr($text, $start_pos);
			array_push($files, $file);
		}
	
	}
	
	return $files;
}
	
function print_organizations($parent_id,
							 &$_menu, 
							 $depth, 
							 $path='',
							 $children,
							 &$string) {
	
	global $html_content_template, $default_html_style, $zipfile, $resources, $ims_template_xml, $parser, $my_files;
	global $used_glossary_terms, $course_id, $course_language_charset, $course_language_code;
	static $paths, $zipped_files;
	global $glossary;
	global $test_zipped_files, $use_a4a, $db;

	$space  = '    ';
	$prefix = '                    ';

	if ($depth == 0) {
		$string .= '<ul>';
	}
	$top_level = $_menu[$parent_id];
	if (!is_array($paths)) {
		$paths = array();
	}
	if (!is_array($zipped_files)) {
		$zipped_files = array();
	}
	if ( is_array($top_level) ) {
		
		$counter = 1;
		$num_items = count($top_level);
               
		foreach ($top_level as $garbage => $content) {
			$link = '';
				
			if ($content['content_path']) {
				$content['content_path'] .= '/';
			}
			//if this is a folder, export it without identifierref
			if ($content['content_type']==CONTENT_TYPE_FOLDER){
				$link = $prevfix.'<item identifier="MANIFEST01_ITEM'.$content['content_id'].'">'."\n";
			} else {
				$link = $prevfix.'<item identifier="MANIFEST01_ITEM'.$content['content_id'].'" identifierref="MANIFEST01_RESOURCE'.$content['content_id'].'" parameters="'.htmlentities($content['test_message'], ENT_QUOTES, 'UTF-8').'">'."\n";
			}
			$html_link = '<a href="resources/'.$content['content_path'].$content['content_id'].'.html" target="body">'.$content['title'].'</a>';	
			
			/* save the content as HTML files */
			/* @See: include/lib/format_content.inc.php */
			$content['text'] = str_replace('CONTENT_DIR/', '', $content['text']);
			/* get all the glossary terms used */
			$terms = find_terms($content['text']);
			if (is_array($terms)) {
				foreach ($terms[2] as $term) {
					$used_glossary_terms[] = $term;
				}
			}

			/** Test dependency **/
			$test_dependency = '';	//Template for test
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'content_tests_assoc WHERE content_id='.$content['content_id'];
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)){
				//add test dependency ontop to forums dependency
				$test_dependency .= $prefix.$space.'<dependency identifierref="MANIFEST01_RESOURCE_QTI'.$row['test_id'].'" />';
			}

			/* calculate how deep this page is: */
			$path = '../';
			if ($content['content_path']) {
				$depth = substr_count($content['content_path'], '/');

				$path .= str_repeat('../', $depth);
			}
			
			$content['text'] = format_content($content['text'], $content['formatting'], $glossary, $path);

			/* add HTML header and footers to the files */
			
			/* use default style if <style> is not in imported html head */
			$head = '';
			if ($content['use_customized_head'])
			{
				if (strpos(strtolower($content['head']), '<style') > 0)
				{
					$head = $content['head'];
				}
				else
				{
					if (strlen($content['head']) > 0)  
						$head = $content['head'] . $default_html_style;
					else 
						$head = $default_html_style;
				}
			}

			$content['text'] = str_replace(	array('{TITLE}',	'{CONTENT}', '{KEYWORDS}', '{COURSE_PRIMARY_LANGUAGE_CHARSET}', '{COURSE_PRIMARY_LANGUAGE_CODE}', '{HEAD}'),
									array($content['title'],	$content['text'], $content['keywords'], $course_language_charset, $course_language_code, $head),
									$html_content_template);
								
			/* duplicate the paths in the content_path field in the zip file */
			if ($content['content_path']) {
				if (!in_array($content['content_path'], $paths)) {
					$zipfile->create_dir('resources/'.$content['content_path'], time());
					$paths[] = $content['content_path'];
				}
			}

			$zipfile->add_file($content['text'], 'resources/'.$content['content_path'].$content['content_id'].'.html', $content['u_ts']);
			$content['title'] = htmlspecialchars($content['title']);

			/* add the resource dependancies */
			if ($my_files == null) $my_files = array();
			$content_files = "\n";
			$parser->parse($content['text']);

			/* generate the IMS QTI resource and files */
			global $contentManager;
			//check if test export is allowed.
			if ($contentManager->allowTestExport($content['content_id'])){
				$content_test_rs = $contentManager->getContentTestsAssoc($content['content_id']);	
				$test_ids = array();		//reset test ids
				//$my_files = array();		//reset myfiles.
				while ($content_test_row = mysql_fetch_assoc($content_test_rs)){
					//export
					$test_ids[] = $content_test_row['test_id'];
					//the 'added_files' is for adding into the manifest file in this zip
					$added_files = test_qti_export($content_test_row['test_id'], '', $zipfile);

					//Save all the xml files in this array, and then print_organizations will add it to the manifest file.
					foreach($added_files as $filename=>$file_array){
						$my_files[] = $filename;
						foreach ($file_array as $garbage=>$filename2){
							if (!in_array($filename2, $my_files)){
								$my_files[] = $filename2;
							}
						}
					}

					//Save all the xml files in this array, and then print_organizations will add it to the manifest file.
					$resources .= str_replace(	array('{TEST_ID}', '{PATH}', '{FILES}'),
												array($content_test_row['test_id'], 'tests_'.$content_test_row['test_id'].'.xml', $added_files_xml),
												$ims_template_xml['resource_test']); 
				}
			}

			/* generate the a4a files */
			$a4a_xml_array = array();
			if ($use_a4a == true){
				$a4aExport = new A4aExport($content['content_id']);
//				$a4aExport->setRelativePath('resources/'.$content['content_path']);
				$secondary_files = $a4aExport->getAllSecondaryFiles();
				$a4a_xml_array = $a4aExport->exportA4a();
				$my_files = array_merge($my_files, $a4aExport->getAllSecondaryFiles());
			}

			/* handle @import */
			$import_files = get_import_files($content['text']);

			if (count($import_files) > 0) $my_files = array_merge($my_files, $import_files);

			foreach ($my_files as $file) {
				/* filter out full urls */
				$url_parts = @parse_url($file);
				if (isset($url_parts['scheme'])) {
					continue;
				}

				/* file should be relative to content. let's double check */
				if ((substr($file, 0, 1) == '/')) {
					continue;
				}

				$file_path = realpath(AT_CONTENT_DIR . $course_id . '/' . $content['content_path'] . $file);

				/* check if this file exists in the content dir, if not don't include it */
				if (file_exists($file_path) && 	is_file($file_path) && !in_array($file_path, $zipped_files)) {
					$zipped_files[] = $file_path;
					$dir = substr(dirname($file_path), strlen(AT_CONTENT_DIR . $course_id));

					if (!in_array($dir, $paths) && $dir) {
						$dir = str_replace('\\', '/', substr($dir, 1));
						$zipfile->create_dir('resources/' . $dir, time());
						
						$paths[] = $dir;
					}

					$file_info = stat( $file_path );

					//Fixes relative paths, so folder1/folder2/../file.jpg will become just folder1/file.jpg
					$file_save_path = str_replace(AT_CONTENT_DIR . $course_id . DIRECTORY_SEPARATOR, '', $file_path);
					$file_save_path = str_replace('\\', '/', $file_save_path);

					//condition checks if the file has been added, so then the test won't be added to all the subchildren
					//leads to normal images not capable to be extracted.
					if ( (empty($test_zipped_files) || (is_array($test_zipped_files) && !in_array($file_path, $test_zipped_files))) 
						 && file_exists($file_path)){
						$zipfile->add_file(@file_get_contents($file_path), 'resources/' . $file_save_path, $file_info['mtime']);
//						$test_zipped_files[] = $content['content_path'] . $file;
						$test_zipped_files[] = $file_path;
					} elseif (!is_array($test_zipped_files) && file_exists($file_path) && !in_array($file_path, $zipped_files)){
						$zipfile->add_file(@file_get_contents($file_path), 'resources/' . $file_save_path, $file_info['mtime']);
					} 

					//a4a secondary files have mapping, save the ones that we want in order to add the tag in
					$a4a_secondary_files = array();
					foreach ($a4a_xml_array as $a4a_filename=>$a4a_filearray){
						if (preg_match('/(.*)\sto\s(.*)/', $a4a_filename, $matches)){
							//save the actual file name
							$a4a_secondary_files[$matches[1]][] = $a4a_filename;	//values are holders
						}
					}

					// If this file has a4a alternatives, link it.
					if (isset($a4a_xml_array[$file]) || isset($a4a_secondary_files[$file])){
						//if this is an array, meaning that it has more than 1 alternatives, print all
						if (is_array($a4a_secondary_files[$file])){
							$all_secondary_files_md = '';	//reinitialize string to null
							foreach ($a4a_secondary_files[$file] as $v){
								foreach($a4a_xml_array[$v] as $v2){
									$all_secondary_files_md .= $v2;	//all the meta data		
								}						
							}
							$content_files .= str_replace(	array('{FILE}', '{FILE_META_DATA}'), 
							array('resources/'.$file_save_path, $all_secondary_files_md), 
							$ims_template_xml['file_meta']);
						} else {	
							$content_files .= str_replace(	array('{FILE}', '{FILE_META_DATA}'), 
							array('resources/'.$file_save_path, $a4a_xml_array[$file]), 
							$ims_template_xml['file_meta']);
						}
					} else {
						//if this file is in the test array, add an extra link to the direct file, 
						if (!empty($test_zipped_files) && in_array($file_path, $test_zipped_files)){
							$content_files .= str_replace('{FILE}', $file_save_path, $ims_template_xml['file']);
						} else {
							$content_files .= str_replace('{FILE}', $file_save_path, $ims_template_xml['file']);
						}
					}
				}

				/* check if this file is one of the test xml file, if so, we need to add the dependency
				 * Note:  The file has already been added to the archieve before this is called.
				 */
/* taken out as of nov 17th, used dependency instead
				if (preg_match('/tests\_[0-9]+\.xml$/', $file) && !in_array($file, $test_zipped_files)){
					$content_files .= str_replace('{FILE}', $file, $ims_template_xml['xml']);
					$test_zipped_files[] = $file;
				}
*/
			}

			/******************************/
			$resources .= str_replace(	array('{CONTENT_ID}', '{PATH}', '{FILES}', '{DEPENDENCY}'),
										array($content['content_id'], $content['content_path'], $content_files, $test_dependency),
										$ims_template_xml['resource']);


			for ($i=0; $i<$depth; $i++) {
				$link .= $space;
			}
			
			$title = $prefix.$space.'<title>'.$content['title'].'</title>';

			if ( is_array($_menu[$content['content_id']]) ) {
				/* has children */

				$html_link = '<li>'.$html_link.'<ul>';
				for ($i=0; $i<$depth; $i++) {
					if ($children[$i] == 1) {
						echo $space;
						//$html_link = $space.$html_link;
					} else {
						echo $space;
						//$html_link = $space.$html_link;
					}
				}

			} else {
				/* doesn't have children */

				$html_link = '<li>'.$html_link.'</li>';
				if ($counter == $num_items) {
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo $space;
							//$html_link = $space.$html_link;
						} else {
							echo $space;
							//$html_link = $space.$html_link;
						}
					}
				} else {
					for ($i=0; $i<$depth; $i++) {
						echo $space;
						//$html_link = $space.$html_link;
					}
				}
				$title = $space.$title;
			}

			echo $prefix.$link;
			echo $title;
			echo "\n";

			$string .= $html_link."\n";

			$depth ++;
			print_organizations($content['content_id'],
								$_menu, 
								$depth, 
								$path.$counter.'.', 
								$children,
								$string);
			$depth--;

			$counter++;
			for ($i=0; $i<$depth; $i++) {
				echo $space;
			}
			echo $prefix.'</item>';
			echo "\n";
		}  

		$string .= '</ul>';
		if ($depth > 0) {
			$string .= '</li>';
		}

	}
}

$ims_template_xml['header'] = '<?xml version="1.0" encoding="{COURSE_PRIMARY_LANGUAGE_CHARSET}"?>
<!--This is an ATutor SCORM 1.2 Content Package document-->
<!--Created from the ATutor Content Package Generator - http://www.atutor.ca-->
<manifest xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_rootv1p2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2" identifier="MANIFEST-'.md5(time()).'" 
xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 http://www.imsglobal.org/xsd/imscp_v1p1p4.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd http://www.imsglobal.org/xsd/imsqti_item_v2p0 http://www.imsglobal.org/xsd/imsqti_item_v2p0.xsd" 
version = "CP 1.1.4">
	<metadata>
		<schema>ADL SCORM</schema> 
  	    <schemaversion>1.2</schemaversion>
		<imsmd:lom>
		  <imsmd:general>
			<imsmd:title>
			  <imsmd:langstring xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}">{COURSE_TITLE}</imsmd:langstring>
			</imsmd:title>
			<imsmd:description>
			  <imsmd:langstring xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}">{COURSE_DESCRIPTION}</imsmd:langstring>
			</imsmd:description>
		  </imsmd:general>
		  <imsmd:lifecycle>
			<imsmd:contribute>
			  <imsmd:role>
			    <imsmd:source>
			      <imsmd:langstring xml:lang="x-none">LOMv1.0</imsmd:langstring> 
			    </imsmd:source>
			    <imsmd:value>
			      <imsmd:langstring xml:lang="x-none">Author</imsmd:langstring> 
			    </imsmd:value>
			  </imsmd:role>
			  <imsmd:centity>
			    <imsmd:vcard>{VCARD}</imsmd:vcard> 
			  </imsmd:centity>
			</imsmd:contribute>
		  </imsmd:lifecycle>
		  <imsmd:educational>
			<imsmd:learningresourcetype>
			  <imsmd:source>
				<imsmd:langstring xml:lang="x-none">ATutor</imsmd:langstring>
			  </imsmd:source>
			  <imsmd:value>
				<imsmd:langstring xml:lang="x-none">Content Module</imsmd:langstring>
			  </imsmd:value>
			</imsmd:learningresourcetype>
		  </imsmd:educational>
		  <imsmd:rights>
		  </imsmd:rights>
		</imsmd:lom>
	</metadata>
';

$ims_template_xml['resource'] = '		<resource identifier="MANIFEST01_RESOURCE{CONTENT_ID}" type="webcontent" href="resources/{PATH}{CONTENT_ID}.html"  adlcp:scormtype="asset">
			<metadata/>
			<file href="resources/{PATH}{CONTENT_ID}.html"/>{FILES}
			{DEPENDENCY}
		</resource>
'."\n";
$ims_template_xml['resource_test'] = '		<resource identifier="MANIFEST01_RESOURCE_QTI{TEST_ID}" type="imsqti_xmlv1p2/imscc_xmlv1p0/assessment">
			<metadata/>
			<file href="{PATH}"/>{FILES}
		</resource>
'."\n";

$ims_template_xml['file'] = '			<file href="resources/{FILE}"/>'."\n";
$ims_template_xml['xml'] = '			<file href="{FILE}"/>'."\n";
$ims_template_xml['file_meta'] = '			<file href="{FILE}">
				<metadata>
				{FILE_META_DATA}
				</metadata>
			</file>'."\n";
$ims_template_xml['final'] = '
	<organizations default="MANIFEST01_ORG1">
		<organization identifier="MANIFEST01_ORG1" structure="hierarchical">
			<title>{COURSE_TITLE}</title>
{ORGANIZATIONS}
		</organization>
	</organizations>
	<resources>
{RESOURCES}
	</resources>
</manifest>';

$html_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}" lang="{COURSE_PRIMARY_LANGUAGE_CODE}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={COURSE_PRIMARY_LANGUAGE_CHARSET}" />
	<style type="text/css">
	body { font-family: Verdana, Arial, Helvetica, sans-serif;}
	a.at-term {	font-style: italic; }
	</style>
	<title>{TITLE}</title>
	<meta name="Generator" content="ATutor">
	<meta name="Keywords" content="{KEYWORDS}">
</head>
<body>{CONTENT}</body>
</html>';

$html_content_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}" lang="{COURSE_PRIMARY_LANGUAGE_CODE}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={COURSE_PRIMARY_LANGUAGE_CHARSET}" />
	{HEAD}
	<title>{TITLE}</title>
	<meta name="Generator" content="ATutor">
	<meta name="Keywords" content="{KEYWORDS}">
</head>
<body>{CONTENT}</body>
</html>';

$default_html_style = '	<style type="text/css">
	body { font-family: Verdana, Arial, Helvetica, sans-serif;}
	a.at-term {	font-style: italic; }
	</style>';
	
//output this as header.html
$html_mainheader = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}" lang="{COURSE_PRIMARY_LANGUAGE_CODE}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={COURSE_PRIMARY_LANGUAGE_CHARSET}" />
	<link rel="stylesheet" type="text/css" href="ims.css"/>
	<title>{COURSE_TITLE}</title>
</head>
<body class="headerbody"><h3>{COURSE_TITLE}</h3></body></html>';


$html_toc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}" lang="{COURSE_PRIMARY_LANGUAGE_CODE}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={COURSE_PRIMARY_LANGUAGE_CHARSET}" />
	<link rel="stylesheet" type="text/css" href="ims.css" />
	<title></title>
</head>
<body>{TOC}</body></html>';

// index.html
$html_frame = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{COURSE_PRIMARY_LANGUAGE_CODE}" lang="{COURSE_PRIMARY_LANGUAGE_CODE}">
	<meta http-equiv="Content-Type" content="text/html; charset={COURSE_PRIMARY_LANGUAGE_CHARSET}" />
	<title>{COURSE_TITLE}</title>
</head>
<frameset rows="50,*,50">
<frame src="header.html" name="header" title="header" scrolling="no">
	<frameset cols="25%, *" frameborder="1" framespacing="3">
		<frame frameborder="2" marginwidth="0" marginheight="0" src="toc.html" name="frame" title="TOC">
		<frame frameborder="2" src="resources/{PATH}{FIRST_ID}.html" name="body" title="{COURSE_TITLE}">
	</frameset>
<frame src="footer.html" name="footer" title="footer" scrolling="no">
	<noframes>
		<h1>{COURSE_TITLE}</h1>
      <p><a href="toc.html">Table of Contents</a> | <a href="footer.html">About</a><br />
	  </p>
  </noframes>
</frameset>
</html>';



$glossary_xml = '<?xml version="1.0" encoding="{COURSE_PRIMARY_LANGUAGE_CHARSET}"?>
<!--This is an ATutor Glossary terms document-->
<!--Created from the ATutor Content Package Generator - http://www.atutor.ca-->

<!DOCTYPE glossary [
   <!ELEMENT item (term, definition)>
   <!ELEMENT term (#PCDATA)>
   <!ELEMENT definition (#PCDATA)>
]>

<glossary>
      {GLOSSARY_TERMS}
</glossary>
';

$glossary_term_xml = '	<item>
		<term>{TERM}</term>
		<definition>{DEFINITION}</definition>
	</item>';

$glossary_body_html = '<h2>Glossary</h2>
	<ul>
{BODY}
</ul>';

$glossary_term_html = '	<li><a name="{ENCODED_TERM}"></a><strong>{TERM}</strong><br />
		{DEFINITION}<br /><br /></li>';

?>