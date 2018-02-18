<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
 * Create the include/config.inc.php based on the config template
 * @param self-explanatory
 * @param true or false. If the file is written successfully, return true, otherwise, return false.
 */
function write_config_file($filename, $db_login, $db_pwd, $db_host, $db_port, $db_name, $tb_prefix,
         $comments, $content_dir, $smtp, $get_file) {
	global $config_template;

	$tokens = array('{USER}',
					'{PASSWORD}',
					'{HOST}',
					'{PORT}',
					'{DBNAME}',
					'{TABLE_PREFIX}',
					'{GENERATED_COMMENTS}',
					'{CONTENT_DIR}',
					'{MAIL_USE_SMTP}',
					'{GET_FILE}'
				);

	$values = array(urldecode($db_login),
				urldecode($db_pwd),
				$db_host,
				$db_port,
				$db_name,
				$tb_prefix,
				$comments,
				urldecode($content_dir),
				$smtp,
				$get_file
			);

	$config_template = str_replace($tokens, $values, $config_template);

	if (!$handle = @fopen($filename, 'wb')) {
		return false;
	}
	@ftruncate($handle,0);
	if (!@fwrite($handle, $config_template, strlen($config_template))) {
		return false;
	}

	@fclose($handle);
	return true;
}

/**
 * Parse config.inc.php that is created by write_config_file()
 * @param the path to the config file
 * @return an array that contains config info
 */
function parse_config_file($config_file) {
	if (!file_exists($config_file)) {
		return false;
	}
	
	$file_content = file_get_contents($config_file);
	$rows = explode("\n", $file_content);
	
	$configs = array();
	
	$term_define = 'define(';
	
	foreach($rows as $row => $data) {
		$data = str_replace(' ', '', $data);
		if (substr($data, 0, 7) == $term_define) {
			$data = str_replace("'", "", substr(substr($data, 7, strlen($data)), 0, -2));
			$one_config = explode(',', $data);
			$configs[$one_config[0]] = $one_config[1];
		}
	}
	return $configs;
}

$config_template = "<"."?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2018                                              */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
{GENERATED_COMMENTS}
/************************************************************************/
/************************************************************************/
/* the database user name                                               */
define('DB_USER',                      '{USER}');

/* the database password                                                */
define('DB_PASSWORD',                  '{PASSWORD}');

/* the database host                                                    */
define('DB_HOST',                      '{HOST}');

/* the database tcp/ip port                                             */
define('DB_PORT',                      '{PORT}');

/* the database name                                                    */
define('DB_NAME',                      '{DBNAME}');

/* The prefix to add to table names to avoid conflicts with existing    */
/* tables. Default: AT_                                                 */
define('TABLE_PREFIX',                 '{TABLE_PREFIX}');

/* Where the course content files are located.  This includes all file  */
/* manager and imported files.  If security is a concern, it is         */
/* recommended that the content directory be moved outside of the web	*/
/* accessible area.														*/
define('AT_CONTENT_DIR', '{CONTENT_DIR}');

/* Whether or not to use the default php.ini SMTP settings.             */
/* If false, then mail will try to be sent using sendmail.              */
/* If true, set the username and password for the SMTP user     */
define('MAIL_USE_SMTP', {MAIL_USE_SMTP});
define('MAIL_SMTP_USER', '');
define('MAIL_SMTP_PASSWORD', '');

/* Whether or not to use the AT_CONTENT_DIR as a protected directory.   */
/* If set to FALSE then the content directory will be hard coded        */
/* to ATutor_install_dir/content/ and AT_CONTENT_DIR will be ignored.   */
/* This option is used for compatability with IIS and Apache 2.         */
define('AT_FORCE_GET_FILE', {GET_FILE});

/* DO NOT ALTER THIS LAST LINE                                          */
define('AT_INSTALL', TRUE);

?".">";

?>