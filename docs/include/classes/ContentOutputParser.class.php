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

class ContentOutputParser {
    function ContentOutputParser(){}
    function openHandler(& $parser,$name,$attrs) {
		global $my_files;

		$name = strtolower($name);
		$attrs = array_change_key_case($attrs, CASE_LOWER);

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
			//hack, if param[name]=src or none <param> tag, extract. Skip all other <param> attributes.  
			if ($name!='param' || $attrs['name']=='src'){
				//skip glossary.html, tweak to accomodate atutor imscp; also skip repeated entries.
				//skip javascript: links, void();, #, mailto:
				if (strpos($attrs[$elements[$name]], 'glossary.html')===false 
				    && !in_array($attrs[$elements[$name]], $my_files)
				    && $attrs[$elements[$name]]!='#'
				    && strpos($attrs[$elements[$name]], 'javascript:')===false 
				    && strpos($attrs[$elements[$name]], 'mailto:')===false 
				    && strpos($attrs[$elements[$name]], 'void(')===false 
				   ){
					$my_files[] = $attrs[$elements[$name]];
				}
			}
		}
	}
	
	function closeHandler(& $parser,$name) { }
}
?>