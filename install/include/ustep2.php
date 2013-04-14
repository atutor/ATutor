<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH') || !defined('AT_UPGRADE_INCLUDE_PATH')) { exit; }

include(AT_INCLUDE_PATH . 'install/upgrade.inc.php');

/*function update_one_ver($up_file, $tb_prefix) {
	global $progress;
	$update_file = implode('_',$up_file);
	
	$sqlUtility = new SqlUtility();
	$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/'.$update_file.'sql', $tb_prefix);

	return $up_file[4];
}
*/
$_POST['db_login'] = urldecode($_POST['db_login']);
$_POST['db_password'] = urldecode($_POST['db_password']);

	unset($errors);

	//check DB & table connection

	$db = @mysql_connect($_POST['db_host'] . ':' . $_POST['db_port'], $_POST['db_login'], urldecode($_POST['db_password']));

	if (!$db) {
		$error_no = mysql_errno();
		if ($error_no == 2005) {
			$errors[] = 'Unable to connect to database server. Database with hostname '.$_POST['db_host'].' not found.';
		} else {
			$errors[] = 'Unable to connect to database server. Wrong username/password combination.';
		}
	} else {
		if (!mysql_select_db($_POST['db_name'], $db)) {
			$errors[] = 'Unable to connect to database <b>'.$_POST['db_name'].'</b>.';
		}

		$sql = "SELECT VERSION() AS version";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		if (version_compare($row['version'], '4.0.2', '>=') === FALSE) {
			$errors[] = 'MySQL version '.$row['version'].' was detected. ATutor requires version 4.0.2 or later.';
		}

		if (!$errors) {
			$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
			unset($errors);

			//Save all the course primary language into session variables iff it has not been set. 
			if (!isset($_SESSION['course_info'])){
				$sql = "SELECT a.course_id, a.title, l.language_code, l.char_set FROM ".$_POST['tb_prefix']."courses a left join ".$_POST['tb_prefix']."languages l ON l.language_code = a.primary_language";
				$result = mysql_query($sql, $db);
				while ($row = mysql_fetch_assoc($result)){
					$_SESSION['course_info'][$row['course_id']] = array('char_set'=>$row['char_set'], 'language_code'=>$row['language_code']);
				}
			}

			$sql = "DELETE FROM ".$_POST['tb_prefix']."language_text WHERE `variable`<>'_c_template' AND `variable`<>'_c_msgs'";
			@mysql_query($sql, $db);

			$sql = "DELETE FROM ".$_POST['tb_prefix']."languages WHERE language_code<>'en'";
			@mysql_query($sql, $db);
			
			// make sure English exists in the language table when upgrading
			$sql = "INSERT INTO `".$_POST['tb_prefix']."languages` VALUES ('en', 'utf-8', 'ltr', 'en([-_][[:alpha:]]{2})?|english', 'English', 'English', 3)";
			@mysql_query($sql, $db);

			run_upgrade_sql(AT_INCLUDE_PATH . 'install/db', $_POST['old_version'], $_POST['tb_prefix']);

			//get list of all update scripts minus sql extension
			/*
			 $files = scandir(AT_INCLUDE_PATH . 'install/db'); 
			foreach ($files as $file) {
				if(count($file = explode('_',$file))==5) {
					$file[4] = substr($file[4],0,-3);
					$update_files[$file[2]] = $file;
				}
			}
			
			$curr_ver = $_POST['old_version'];
			ksort($update_files);
			foreach ($update_files as $up_file) {
				if(version_compare($curr_ver, $up_file[4], '<')) {
					update_one_ver($up_file, $_POST['tb_prefix']);
				}
			}
			*/
			/* reset all the accounts to English */
			$sql = "UPDATE ".$_POST['tb_prefix']."members SET language='en', creation_date=creation_date, last_login=last_login";
			@mysql_query($sql, $db);

			/* set all the courses to 'en' as primary language if empty. added 1.4.1 */
			$sql = "UPDATE ".$_POST['tb_prefix']."courses SET primary_language='en' WHERE primary_language=''";
			@mysql_query($sql, $db);

			$sqlUtility = new SqlUtility();
			$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_language_text.sql', $_POST['tb_prefix']);

			if (!$errors) {
				print_progress($step);

				unset($_POST['submit']);
				store_steps(1);
				print_feedback($progress);

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />
				<input type="hidden" name="upgrade_action" value="true" />';
				echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
				echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
				echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
				echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
				echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
				echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
				echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
				echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';
				print_hidden(2);
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';
				return;
			}
		}
	}

	print_progress($step);

	unset($_POST['submit']);
	if (isset($progress)) {
		print_feedback($progress);
	}

	if (isset($errors)) {
		print_errors($errors);
	}


	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
	<input type="hidden" name="step" value="2" />';
	store_steps(1);
	print_hidden(2);
	
	if ($found_lang) {
?>
<table width="60%" class="tableborder" cellspacing="0" cellpadding="1" border="0" align="center">
<tr>
	<td colspan="2" class="row1"><p><small>All installed language packs and changes made to the default English language will be deleted. You will have to re-install any language packs by downloading the latest versions from ATutor.ca. Some language packs may not currently be available.</small></p></td>
</tr>
<tr>
	<td class="row1"><small><b><label for="dir">Continue with the upgrade?</label></b></small></td>
		<td class="row1" valign="middle" nowrap="nowrap"><input type="radio" name="override" value="1" id="c2" /><label for="c2">Yes, Continue</label>, <input type="radio" name="override" value="0" id="c1" checked="checked" /><label for="c1">No, Cancel</label></td>
</tr>
</table><br />
	<?php
	}

	echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
	echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
	echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
	echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
	echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
	echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
	echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
	echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';

	echo '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p></form>';
	return;
?>