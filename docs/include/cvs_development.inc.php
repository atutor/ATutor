<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

/* THIS FILE DOES NOT GET INCLUDED IN THE PUBLIC DISTRIBUTION VERSION!! */

if (!defined('AT_INCLUDE_PATH')) { exit; }

	/* this block is only for developers!          */
	/* specify the language server below           */
	define('TABLE_PREFIX_LANG', '');
	define('AT_CVS_DEVELOPMENT', " AND project='atutor'");

	/* this username and password gives you read only access to the central
	 * language database.
	 */
	$lang_db = mysql_connect('atutorsvn.rcat.utoronto.ca', 'read_dev_lang', 'read_dev_lang');
	if (!$lang_db) {
		echo 'Unable to connect to db.';
		exit;
	}
	if (!mysql_select_db('dev_atutor_langs', $lang_db)) {
		echo 'DB connection established, but database "dev_atutor_langs" cannot be selected.';
		exit;
	}


?>