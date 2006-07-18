<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH')) { exit; }

function update_one_ver($up_file) {
	global $progress;
	$update_file = implode('_',$up_file);
	queryFromFile('db/'.$update_file.'sql');
	$progress[] = 'Successful update from version '.$up_file[2].' to '.$up_file[4];
	return $up_file[4];
}

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

		if (!$_POST['override']) {
			$sql = "SELECT COUNT(*) AS cnt FROM ".$_POST['tb_prefix']."languages";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$found_lang = false;
			if ($row['cnt'] > 1) {
				$found_lang = true;
			}
			if ($found_lang == false) {
				$_POST['override'] = true;
			}
		}

		if (!$errors && $_POST['override']) {
			$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
			unset($errors);

			$sql = "DELETE FROM ".$_POST['tb_prefix']."language_text WHERE 1";
			@mysql_query($sql, $db);

			$sql = "DELETE FROM ".$_POST['tb_prefix']."languages WHERE language_code<>'en'";
			@mysql_query($sql, $db);

			//get list of all update scripts minus sql extension
			$files = scandir('db'); 
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
					update_one_ver($up_file);
				}
			}
			
			/* reset all the accounts to English */
			$sql = "UPDATE ".$_POST['tb_prefix']."members SET language='en'";
			@mysql_query($sql, $db);

			/* set all the courses to 'en' as primary language if empty. added 1.4.1 */
			$sql = "UPDATE ".$_POST['tb_prefix']."courses SET primary_language='en' WHERE primary_language=''";
			@mysql_query($sql, $db);

			queryFromFile('db/atutor_language_text.sql');

			if (!$errors) {
				print_progress($step);

				unset($_POST['submit']);
				store_steps(1);
				print_feedback($progress);

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />
				<input type="hidden" name="upgrade_action" value="true" />';
				print_hidden(3);
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