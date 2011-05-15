<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE) { 
	$msg->addWarning('TRANSLATE_ON');	
}
$smtp_server = ini_get('SMTP');
if (($smtp_server == '' || $smtp_server == 'localhost') && ini_get('sendmail_path') == '') {
	$msg->addWarning('MAIL_NOT_ON');
}

// Social networking only switch
if (isset($_POST['social_submit'])) {
	$_POST['just_social']          = intval($_POST['just_social']);

	if ($_POST['just_social'] == 1) {
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('just_social', '$_POST[just_social]')";
		mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_REPLACE, 'config', mysql_affected_rows($db), $sql);
		$msg->addFeedback('ATUTOR_SOCIAL_ONLY');
		
	} else if ($_POST['just_social'] == 0) {
		$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='just_social'";
		mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_DELETE, 'config', mysql_affected_rows($db), $sql);
		$msg->addFeedback('ATUTOR_SOCIAL_LMS');
		
	}
	$_config['just_social'] = $_POST['just_social'];
}
require(AT_INCLUDE_PATH.'header.inc.php');

if ($_config['check_version']) {
	$request = @file('http://atutor.ca/check_atutor_version.php?return');
	if ($request && version_compare(VERSION, $request[0], '<')) {
		$msg->printFeedbacks('ATUTOR_UPDATE_AVAILABLE');
	}
}
if ($_config['allow_instructor_requests'] && admin_authenticate(AT_ADMIN_PRIV_USERS, AT_PRIV_RETURN)){
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."instructor_approvals";
			$result = mysql_query($sql, $db);
			$row    = mysql_fetch_assoc($result);
			$savant->assign('row', $row);
}



	$update_server = "update.atutor.ca"; 

	$file = fsockopen ($update_server, 80, $errno, $errstr, 15);
	
	if ($file) 
	{
		// get patch list
		$patch_folder = "http://" . $update_server . '/patch/' . str_replace('.', '_', VERSION) . '/';

		$patch_list_xml = @file_get_contents($patch_folder . 'patch_list.xml');
		
		if ($patch_list_xml) 
		{
			require_once('../mods/_standard/patcher/classes/PatchListParser.class.php');
			$patchListParser = new PatchListParser();
			$patchListParser->parse($patch_list_xml);
			$patch_list_array = $patchListParser->getMyParsedArrayForVersion(VERSION);
			
			if (count($patch_list_array)) {
				foreach ($patch_list_array as $row_num => $patch) {
					$patch_ids .= '\'' . $patch['atutor_patch_id'] . '\', ';
				}
					
				$sql = "select count(distinct atutor_patch_id) cnt_installed_patches from ".TABLE_PREFIX."patches " .
				       "where atutor_patch_id in (" . substr($patch_ids, 0, -2) .")".
				       " and status like '%Installed'";
			
				$result = mysql_query($sql, $db) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				
				$cnt = count($patch_list_array) - $row['cnt_installed_patches'];
				$savant->assign('cnt', $cnt);
	
				if ($cnt > 0)
				{
	

	
				}
			}
		}
	} 
if (!isset($_config['db_size']) || ($_config['db_size_ttl'] < time())) {
				$_config['db_size'] = 0;
				$sql = 'SHOW TABLE STATUS';
				$result = mysql_query($sql, $db);
				while($row = mysql_fetch_assoc($result)) {
					$_config['db_size'] += $row['Data_length']+$row['Index_length'];
				
				}

				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('db_size', '{$_config['db_size']}')";
				mysql_query($sql, $db);
			
				// get disk usage if we're on *nix
				if (DIRECTORY_SEPARATOR == '/') {
					$du = @shell_exec('du -sk '.escapeshellcmd(AT_CONTENT_DIR));
					if ($du) {
						$_config['du_size'] = (int) $du;
						$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('du_size', '{$_config['du_size']}')";
						mysql_query($sql, $db);
						
					}
				}

				$ttl = time() + 24 * 60 * 60; // every 1 day.
				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('db_size_ttl', '$ttl')";
				mysql_query($sql, $db);
				
			}

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."courses";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_courses = $row['cnt'];
			$savant->assign('num_courses', $num_courses);

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_users = $row['cnt'];
			$savant->assign('num_users', $num_users);

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."admins";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_users += $row['cnt'];
			

			$sql = "SELECT VERSION()";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_array($result);
			$mysql_version = $row[0];

$savant->assign('path_length', $path_length);
$savant->assign('pages', $_pages);
$savant->assign('db_size', $_config['db_size']);
$savant->assign('du_size', $_config['du_size']);
$savant->display('admin/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>