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


function print_organizations($parent_id,
							 &$_menu, 
							 $depth, 
							 $path='',
							 $children,
							 &$string) {
	
	global $html_template, $zipfile, $resources, $ims_template_xml, $parser, $my_files;
	static $paths, $zipped_files;

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
				
			if (is_array($temp_path)) {
				$this = current($temp_path);
			}
			if ($content['content_path']) {
				$content['content_path'] .= '/';
			}

			$link = $prevfix.'<item identifier="MANIFEST01_ITEM'.$content['content_id'].'" identifierref="MANIFEST01_RESOURCE'.$content['content_id'].'" parameters="">'."\n";
			$html_link = '<a href="resources/'.$content['content_path'].$content['content_id'].'.html" target="body">'.$content['title'].'</a>';
			
			/* save the content as HTML files */
			/* @See: include/lib/format_content.inc.php */
			$content['text'] = str_replace('CONTENT_DIR/', '', $content['text']);
			$content['text'] = format_content($content['text'], $content['formatting'], false);

			/* add HTML header and footers to the files */

			$content['text'] = str_replace(	array('{TITLE}',	'{CONTENT}', '{KEYWORDS}'),
									array($content['title'],	$content['text'], $content['keywords']),
									$html_template);

			/* duplicate the paths in the content_path field in the zip file */
			if ($content['content_path']) {
				if (!in_array($content['content_path'], $paths)) {
					$zipfile->priv_add_dir('resources/'.$content['content_path'], time());
					$paths[] = $content['content_path'];
				}
			}

			$zipfile->add_file($content['text'], 'resources/'.$content['content_path'].$content['content_id'].'.html', $content['u_ts']);
			$content['title'] = htmlspecialchars($content['title']);

			/* add the resource dependancies */
			$my_files = array();
			$content_files = "\n";
			$parser->parse($content['text']);

			foreach ($my_files as $file) {
				/* filter out full urls */
				$url_parts = parse_url($file);
				if (isset($url_parts['scheme'])) {
					continue;
				}

				/* file should be relative to content. let's double check */
				if ((substr($file, 0, 1) == '/') && ( strpos($file, '..') !== false) ) {
					continue;
				}

				$file_path = '../../content/' . $_SESSION['course_id'] . '/' . $content['content_path'] . $file;

				/* check if this file exists in the content dir, if not don't include it */
				if (file_exists($file_path) && 	!in_array($file_path, $zipped_files)) {
					$zipped_files[] = $file_path;

					$dir = dirname($content['content_path'] . $file).'/';

					if (!in_array($dir, $paths)) {
						$zipfile->priv_add_dir('resources/'.$dir, time());
						$paths[] = $dir;
					}

					$file_info = stat( $file_path );
					$zipfile->add_file(file_get_contents($file_path), 'resources/' . $content['content_path'] . $file, $file_info['mtime']);

					$content_files .= str_replace('{FILE}', $content['content_path'] . $file, $ims_template_xml['file']);
				}
			}

			/******************************/
			$resources .= str_replace(	array('{CONTENT_ID}', '{PATH}', '{FILES}'),
										array($content['content_id'], $content['content_path'], $content_files),
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



$ims_template_xml['header'] = '<?xml version="1.0"?>
<manifest identifier="man113" version="1.1.3" xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<metadata>
		<schema>IMS CONTENT</schema>
		<schemaversion>1.1</schemaversion>
		<lom xmlns="http://www.imsproject.org/metadata">
		  <educational>
			<learningresourcetype>
			  <source>
				<langstring xml:lang="x-none">ATutor</langstring>
			  </source>
			  <value>
				<langstring xml:lang="x-none">Content Module</langstring>
			  </value>
			</learningresourcetype>
		  </educational>
		  <lifecycle>
		  </lifecycle>
		  <general>
			<title>
			  <langstring>{COURSE_TITLE}</langstring>
			</title>
		  </general>
		  <rights>
		  </rights>
		</lom>
	</metadata>
';

$ims_template_xml['resource'] = '		<resource identifier="MANIFEST01_RESOURCE{CONTENT_ID}" type="webcontent" href="resources/{PATH}{CONTENT_ID}.html">
			<metadata/>
			<file href="resources/{PATH}{CONTENT_ID}.html"/>{FILES}
		</resource>'."\n";

$ims_template_xml['file'] = '			<file href="resources/{FILE}"/>'."\n";

$ims_template_xml['final'] = '
	<organizations default="MANIFEST01_ORG1">
		<organization identifier="MANIFEST01_ORG1" structure="hierarchical">
{ORGANIZATIONS}
		</organization>
	</organizations>
	<resources>
{RESOURCES}
	</resources>
</manifest>';

$html_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style type="text/css">
	body { font-family: Verdana, Arial, Helvetica, sans-serif;}
	</style>
	<title>{TITLE}</title>
	<meta name="Generator" content="ATutor'.VERSION.'">
	<meta name="Keywords" content="{KEYWORDS}">
</head>
<body>{CONTENT}</body>
</html>';



//output this as header.html
$html_mainheader = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" type="text/css" href="ims.css"/>
	<title></title>
</head>
<body class="headerbody"><h3>{COURSE_TITLE}</h3></body></html>';


$html_toc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" type="text/css" href="ims.css" />
	<title></title>
</head>
<body>{TOC}</body></html>';

$html_frame = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
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
      <p><br />
	  </p>
  </noframes>
</frameset>
</html>';

?>