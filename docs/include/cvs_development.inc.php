<?php
/* THIS FILE DOES NOT GET INCLUDED IN THE PUBLIC DISTRIBUTION VERSION!! */

if (!defined('AT_INCLUDE_PATH')) { exit; }

	/* this block is only for developers!          */
	/* specify the language server below           */
	define('TABLE_PREFIX_LANG', '');
	define('AT_CVS_DEVELOPMENT', " AND project='atutor'");
	define('TABLE_SUFFIX_LANG', '_SVN');

	$lang_db = mysql_connect('atutorsvn.rcat.utoronto.ca', 'read_dev_lang', 'read_dev_lang');
	if (!$lang_db) {
		/* AT_ERROR_NO_DB_CONNECT */
		echo 'Unable to connect to db.';
		exit;
	}
	if (!mysql_select_db('dev_atutor_langs', $lang_db)) {
		echo 'DB connection established, but database "dev_atutor_langs" cannot be selected.';
		exit;
	}
?>