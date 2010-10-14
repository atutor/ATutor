<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

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
	global $test_list, $test_zipped_files, $test_files, $test_xml_items, $use_a4a;
        /* added by bologna*///TODO***********BOLOGNA**************REMOVE ME*****************/
        global $db,$forum_list;//forum_list contiene tutti i forum distinti associati ai contenuti. poich� la funzione in questione � ricorsiva deve essere globale in modo che in fase di creazione dell'archivio zip i file descrittori dei forum non vengano ripetuti

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
			//XSL characters handling
			$content['title'] = str_replace('&', '&amp;', $content['title']);
				
			if ($content['content_path'] && (substr($content['content_path'],-1)!='/')) {
				$content['content_path'] .= '/';
			}

			/* 
			 * generate weblinks 
			 * Reason to put it here is cause we don't want the content to be overwrittened.
			 */
			if ($content['content_type']==CONTENT_TYPE_WEBLINK){
				$wl = new Weblinks($content['title'], $content['text']);
				$wlexport = new WeblinksExport($wl);
				$wl_xml = $wlexport->export();
				$wl_filename = 'weblinks_'.$content['content_id'].'.xml';
				$zipfile->add_file($wl_xml , 'Weblinks/'.$wl_filename, $content['u_ts']);
				$resources .= str_replace(	array('{PATH}', '{CONTENT_ID}'), 
											array($wl_filename, $content['content_id']), 
											$ims_template_xml['resource_weblink']);
				//Done.
//				continue;
			}

			if ($content['content_type']==CONTENT_TYPE_FOLDER){
				$link .= $prefix.'<item identifier="MANIFEST01_FOLDER'.$content['content_id'].'">'."\n";
				$link .= $prefix.$space.'<title>'.$content['title'].'</title>'."\n";
			} else {
				$link .= '<item identifier="MANIFEST01_ITEM'.$content['content_id'].'" identifierref="MANIFEST01_RESOURCE'.$content['content_id'].'">'."\n";
				$link .= $prefix.$space.'<title>'.$content['title'].'</title>'."\n$prefix$space";
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


                    //TODO**************BOLOGNA****************REMOVE ME************/
                    $f_count = count($forum_list); //count all distinct forum_id associated to a content page
                                                //la funzione è ricorsiva quindi lo devo ricavare attraverso la variabile globale forum_list

                    /* TODO *************BOLOGNA*************REMOVE ME*********/
                    //recupero i forum associati al contenuto corrente
                    $sql = "SELECT cf.forum_id, f.title, f.description FROM (SELECT * FROM ".TABLE_PREFIX."content_forums_assoc WHERE content_id=$content[content_id]) AS cf LEFT JOIN ".TABLE_PREFIX."forums f ON cf.forum_id=f.forum_id";
                    $result_cf = mysql_query($sql,$db);
                    $cf_count = mysql_num_rows($result_cf);
   
                    //per ogni forum ottenuto controllo se è già stato caricato nell'array
                    //necessario mantenerlo distinto poichè NON si prevedono funzioni sul
                    //controllo dei nomi nell'inserimento di file nell'archivio.
                    $find=false;
                    $forums_dependency='';  //template for associate Discussion Topic to the current content into the manifest
                    while($current_forum = mysql_fetch_assoc($result_cf)) {
                        for($j=0;$j<$f_count;$j++) {
                            if($forum_list[$j]['id'] == $current_forum['forum_id'])
                                $find= true;
                        }
                        if(!$find) {

                            $forum_list[$f_count]['id']=$current_forum['forum_id'];
                            $forum_list[$f_count]['title']=$current_forum['title'];
                            $forum_list[$f_count]['description']=$current_forum['description'];
                            $find=false;
                            $f_count++;
                        }
			$forums_dependency .= $prefix.$space.'<dependency identifierref="Forum'.$current_forum['forum_id'].'_R" />';
                    }

			 /** Test dependency **/
			 $sql = 'SELECT * FROM '.TABLE_PREFIX.'content_tests_assoc WHERE content_id='.$content['content_id'];
			 $result = mysql_query($sql, $db);
			 while ($row = mysql_fetch_assoc($result)){
				//add test dependency on top of forum dependency
				$forums_dependency .= $prefix.$space.'<dependency identifierref="MANIFEST01_RESOURCE_QTI'.$row['test_id'].'" />';
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
			//add the file iff it's a content file
			if($content['content_type']==CONTENT_TYPE_CONTENT){
				$zipfile->add_file($content['text'], 'resources/'.$content['content_path'].$content['content_id'].'.html', $content['u_ts']);
			}
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
					foreach($added_files as $xml_file=>$chunk){
						foreach ($chunk as $xml_filename){
							$added_files_xml .= str_replace('{FILE}', 'resources/'.$xml_filename, $ims_template_xml['xml']);
						}
					}
					//Save all the xml files in this array, and then print_organizations will add it to the manifest file.
					$resources .= str_replace(	array('{TEST_ID}', '{PATH}', '{FILES}'),
												array($content_test_row['test_id'], 'tests_'.$content_test_row['test_id'].'.xml', $added_files_xml),
												$ims_template_xml['resource_test']); 
/*	Taken out since we are gonna use dependency instead
					$test_xml_items .= str_replace(	array('{TEST_ID}'),
												array($content_test_row['test_id']),
												$ims_template_xml['test']); 
*/
					foreach($test_files as $filename=>$realfilepath){
						$zipfile->add_file(@file_get_contents($realfilepath), 'resources/'.$filename, filemtime($realfilepath));
					}
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
//				if (isset($url_parts['scheme'])) {
//					continue;
//				}

				/* file should be relative to content. let's double check */
				if ((substr($file, 0, 1) == '/')) {
					continue;
				}

				if (substr($file, 0, 7) != 'http://' && substr($file, 0, 8) != 'https://') {
					$file_path = realpath(AT_CONTENT_DIR . $course_id . '/' . $content['content_path'] . $file);
					/* check if the path contains AT_CONTENT_DIR in it, if not, skip it, it's trying to scan through 
					 * the file system */
					if (strpos($file_path, AT_CONTENT_DIR)!==0){
						continue; //skip
					}
                    
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
	
						//remove relative path in the content_path.	
						$filepath_array = explode('/', 'resources/' . $content['content_path'] . $file);
						$new_filepath_array = array();
						if (in_array('..', $filepath_array)){
							while (!empty($filepath_array)){
								$temp = array_shift($filepath_array);
								if ($temp == '..'){
									array_pop($new_filepath_array);
								} else {
									array_push($new_filepath_array, $temp);
								}
							}
							$zip_path = implode('/', $new_filepath_array);
						} else {
							$zip_path = 'resources/' . $content['content_path'] . $file;
						}
	
						$zipfile->add_file(@file_get_contents($file_path), $zip_path, $file_info['mtime']);
					}
				}
				//a4a secondary files have mapping, save the ones that we want in order to add the tag in
				$a4a_secondary_files = array();
				foreach ($a4a_xml_array as $a4a_filename=>$a4a_filearray){
					if (preg_match('/(.*)\sto\s(.*)/', $a4a_filename, $matches)){
						//save the actual file name
						$a4a_secondary_files[$matches[1]][] = $a4a_filename;	//values are holders
					}
				}

                /**
                 * A hack to fix youtube links.  one uses youtube.com?watch=xxx, the other uses youtube.com/v/xxx,
                 * in which both points to the same file, but needed different links to play.
                 * in A4a, these youtube links are always stored as "?watch=xxx", however, output.inc.php converted
                 * these to /v/xxx for rendering purposes.  Convert it back if youtube exists in url.
                 * http://atutor.ca/atutor/mantis/view.php?id=4548
                 * @harris 9/30/2010
                 */
                if (strpos($file, 'youtube.com')!==false){
                    //apply the conversion before linking the alternatives. Otherwise it will not be added.
                    $file = convert_youtube_playURL_to_watchURL($file);
                }
				// If this file has a4a alternatives, link it.
				if (isset($a4a_xml_array[$file]) || isset($a4a_secondary_files[$file])){
					//if this is an array, meaning that it has more than 1 alternatives, print all
					if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://') {
						$name_in_file_meta = $file;
					} else {
						$name_in_file_meta = 'resources/'.$content['content_path'] . $file;
					}
					if (is_array($a4a_secondary_files[$file])){
						$all_secondary_files_md = '';	//reinitialize string to null
						foreach ($a4a_secondary_files[$file] as $v){
							foreach($a4a_xml_array[$v] as $v2){
								$all_secondary_files_md .= $v2;	//all the meta data		
							}								
						}
						$content_files .= str_replace(	array('{FILE}', '{FILE_META_DATA}'), 
						array($name_in_file_meta, $all_secondary_files_md), 
						$ims_template_xml['file_meta']);
					} else {
						$content_files .= str_replace(	array('{FILE}', '{FILE_META_DATA}'), 
						array($name_in_file_meta, $a4a_xml_array[$file]), 
						$ims_template_xml['file_meta']);
					}
				} else {
					//if this file is in the test array, add an extra link to the direct file, 
					if (!empty($test_zipped_files) && in_array($file_path, $test_zipped_files)){
						$content_files .= str_replace('{FILE}', $file, $ims_template_xml['file']);
					} else {
						if (preg_match('/^http[s]?\:/', $file) == 1){
							$content_files .= str_replace('{FILE}', $file, $ims_template_xml['xml']);
						} elseif (file_exists($file_path) && is_file($file_path)) {
							//http://www.atutor.ca/atutor/mantis/view.php?id=4313
							//relative link that goes beyond get.php shouldn't be added
							//relative link that does not exist shouldn't be added.
							$content_files .= str_replace('{FILE}', $content['content_path'] . $file, $ims_template_xml['file']);
						}
					}
				}
				/* check if this file is one of the test xml file, if so, we need to add the dependency
				 * Note:  The file has already been added to the archieve before this is called.
				 */
				if (preg_match('/tests\_[0-9]+\.xml$/', $file) && !in_array($file, $test_zipped_files)){
					$content_files .= str_replace('{FILE}', 'QTI/'.$file, $ims_template_xml['xml']);
					$test_zipped_files[] = $file;
				}
			}
			/******************************
			 * http://www.atutor.ca/atutor/mantis/view.php?id=4383 
		     */
			$my_files = array();

			/******************************/
			//add it to the resources section if it hasn't been added.  
			//Weblinks have been added.
			//Folders aren't resourecs, they shouldn't be added
			if($content['content_type']==CONTENT_TYPE_CONTENT){
				$resources .= str_replace(	array('{CONTENT_ID}', '{PATH}', '{FILES}','{DEPENDENCY}'),
											array($content['content_id'], $content['content_path'], $content_files, $forums_dependency),
											$ims_template_xml['resource']); 
			}

			for ($i=0; $i<$depth; $i++) {
				$link .= $space;
			}

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
//			echo $title;
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
//debug($_menu, $content['content_id']);
//			if (!empty($_menu[$content['content_id']])){
			echo $prefix.'</item>';
//			}
			echo "\n";
		}  

		$string .= '</ul>';
		if ($depth > 0) {
			$string .= '</li>';
		}

	} 
}


//TODO***************BOLOGNA******************REMOVE ME***************/
/* Export Forum */
function print_resources_forum() {

    global $forum_list, $zipfile, $resources;           //$forum_list contiene tutti i forum DISTINTI associati ai contenuti. caricato in print_organizations()

    $ims_template_xml['resource_forum'] =

        '<resource identifier="Forum{FORUMID}_R" type="imsdt_xmlv1p0">
            <metadata/>
            <file href="Forum{FORUMID}/FileDescriptorForum{FORUMID}.xml"/>
        </resource>
	'."\n";

    foreach ($forum_list as $f){
    // per ogni forum associato ad uno o pi� contenuti del corso viene aggiunto un elemento resource in imsmanifest.xml
        $resources .= str_replace("{FORUMID}", $f['id'], $ims_template_xml['resource_forum']);

        //viene generato il file descrittore
        //file Descrittore con la descrzione del forum
        $fileDesDT_D = '<?xml version="1.0" encoding="UTF-8"?>

                    <topic
                        xmlns="http://www.imsglobal.org/xsd/imsdt_v1p0"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsdt_v1p0 http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_6/imsdt_v1p0_localised.xsd">

                        <title>{TitleDiscussionTopic}</title>

                        <text texttype="text/plain">{DescriptionDiscussionTopic}</text>

                    </topic>';

        //file Descrittore senza la descrizione del forum
        $fileDesDT = '<?xml version="1.0" encoding="UTF-8"?>

                    <topic
                        xmlns="http://www.imsglobal.org/xsd/imsdt_v1p0"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsdt_v1p0 http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_6/imsdt_v1p0_localised.xsd">

                        <title>{TitleDiscussionTopic}</title>

                        <text/>

                    </topic>';

		$f['title'] = htmlspecialchars($f['title']);
		$f['description'] = htmlspecialchars($f['description']);
        if (empty($f['description']))
            $text_file_des_xml = str_replace (array('{TitleDiscussionTopic}', '{DescriptionDiscussionTopic}'), array($f['title'], $f['description']), $fileDesDT);
        else
            $text_file_des_xml = str_replace (array('{TitleDiscussionTopic}', '{DescriptionDiscussionTopic}'), array($f['title'], $f['description']), $fileDesDT_D);

        $zipfile->add_file($text_file_des_xml,  'Forum'.$f['id'].'/'.'FileDescriptorForum'.$f['id'].'.xml');
    }
    //$zipfile->add_file(file_get_contents('../../images/home-forums_sm.png'),  'resources/home-forums_sm.png');
}


$ims_template_xml['header'] = '<?xml version="1.0" encoding="{COURSE_PRIMARY_LANGUAGE_CHARSET}"?>
<!--This is an ATutor IMS Common Cartridge 1.0 document-->
<!--Created from the ATutor Content Package Generator - http://www.atutor.ca-->
<manifest identifier="MANIFEST-'.md5($cid).'" 
xmlns="http://www.imsglobal.org/xsd/imscc/imscp_v1p1" 
xmlns:imsmd="http://ltsc.ieee.org/xsd/imscc/LOM" 
xmlns:lomimscc="http://ltsc.ieee.org/xsd/imscc/LOM" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://www.imsglobal.org/xsd/imscc/imscp_v1p1 http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/imscp_v1p2_localised.xsd http://ltsc.ieee.org/xsd/imscc/LOM http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_1/lomLoose_localised.xsd" version="IMS Common Cartridge 1.0.0">
	<metadata>
		<schema>IMS Common Cartridge</schema>
	    <schemaversion>1.0.0</schemaversion>
		<lomimscc:lom>
      <lomimscc:general>
        <lomimscc:identifier>          
			<lomimscc:catalog>ATutor</lomimscc:catalog>
			<lomimscc:entry></lomimscc:entry>
        </lomimscc:identifier>
        <lomimscc:title>
			<lomimscc:string language="{COURSE_PRIMARY_LANGUAGE_CODE}">{COURSE_TITLE}</lomimscc:string>
        </lomimscc:title>
        <lomimscc:language>en</lomimscc:language>
        <lomimscc:description> 
			<lomimscc:string language="{COURSE_PRIMARY_LANGUAGE_CODE}">{COURSE_DESCRIPTION}</lomimscc:string>
        </lomimscc:description>
        <lomimscc:keyword>
			<lomimscc:string language="{COURSE_PRIMARY_LANGUAGE_CODE}">{KEYWORDS}</lomimscc:string>
        </lomimscc:keyword>
      </lomimscc:general>
    </lomimscc:lom>
	</metadata>'
;

$ims_template_xml['resource'] = '		<resource identifier="MANIFEST01_RESOURCE{CONTENT_ID}" type="webcontent" href="resources/{PATH}{CONTENT_ID}.html">
			<metadata/>
			<file href="resources/{PATH}{CONTENT_ID}.html"/>{FILES}
                        {DEPENDENCY}
		</resource>
'."\n";
$ims_template_xml['resource_glossary'] = '		<resource identifier="MANIFEST01_RESOURCE_GLOSSARY" type="associatedcontent/imscc_xmlv1p0/learning-application-resource" href="resources/GlossaryItem/glossary.xml">
			<metadata/>
			<file href="resources/GlossaryItem/glossary.xml"/>
		</resource>
'."\n";
$ims_template_xml['resource_test'] = '		<resource identifier="MANIFEST01_RESOURCE_QTI{TEST_ID}" type="imsqti_xmlv1p2/imscc_xmlv1p0/assessment">
			<metadata/>
			<file href="QTI/{PATH}"/>{FILES}
		</resource>
'."\n";
$ims_template_xml['resource_weblink'] = '		<resource identifier="MANIFEST01_RESOURCE{CONTENT_ID}" type="imswl_xmlv1p0">
			<metadata/>
			<file href="Weblinks/{PATH}"/>
		</resource>
'."\n";
$ims_template_xml['file'] = '			<file href="resources/{FILE}"/>'."\n";
$ims_template_xml['xml'] = '			<file href="{FILE}"/>'."\n";
$ims_template_xml['glossary'] = '			<item identifier="GlossaryItem" identifierref="MANIFEST01_RESOURCE_GLOSSARY">
				<title>Glossary</title>
			</item>';
$ims_template_xml['test'] = '			<item identifier="QTI{TEST_ID}" identifierref="MANIFEST01_RESOURCE_QTI{TEST_ID}">
				<title>Test {TEST_ID}</title>
			</item>';
$ims_template_xml['file_meta'] = '			<file href="{FILE}">
				<metadata>
				{FILE_META_DATA}
				</metadata>
			</file>'."\n";

$ims_template_xml['final'] = '
	<organizations>
		<organization identifier="MANIFEST01_ORG1" structure="rooted-hierarchy">
			<item identifier="resources">		
		{ORGANIZATIONS}
		    </item>
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
<glossary:glossary xmlns:glossary="http://www.atutor.ca/xsd/glossary" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      {GLOSSARY_TERMS}
</glossary:glossary>
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
