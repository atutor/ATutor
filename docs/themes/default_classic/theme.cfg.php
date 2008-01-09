<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

/* This is the default configuration file for the default theme. */

/* The theme's name. */
	$_theme['name'] = 'ATutor Classic';

/* The theme's version number. */
	$_theme['version'] = '0.2';

/* Which version of ATutor is this theme intended for. */
	$_theme['atutor-version'] = '1.5.3';

/* author information */
	$_theme['author_name']  = 'ATutor';
	$_theme['author_url']   = 'http://atutor.ca';
	$_theme['author_email'] = '';


/* Top left header image  - approximately w:230 x h:90					*/
/* Default: images/pub_default.jpg										*/	
/* The path to the image must be an absolute path, or use $_base_href   */
/* if it's in the ATutor installation directory.                        */
define('AT_HEADER_IMAGE',					$_base_path. 'images/pub_default.jpg');

/* Top right logo default: images/at-logo.gif */
/* The path to the image must be an absolute path, or use $_base_href   */
/* if it's in the ATutor installation directory.                        */
define('AT_HEADER_LOGO',					$_base_path . 'images/at-logo.gif');

?>
