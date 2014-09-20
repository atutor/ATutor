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

if (!defined('AT_INCLUDE_PATH') || !defined('AT_UPGRADE_INCLUDE_PATH')) { exit; }

$_POST['db_login']    = urldecode($_POST['db_login']);
$_POST['db_password'] = urldecode($_POST['db_password']);
/* Destory session */
session_unset();
$_SESSION= array();

if(defined('MYSQLI_ENABLED')){
    $db = at_db_connect($_POST['db_host'], $_POST['db_port'], $_POST['db_login'], urldecode($_POST['db_password']), $_POST['db_name']);   
}else{
    $db = at_db_connect($_POST['db_host'], $_POST['db_port'], $_POST['db_login'], urldecode($_POST['db_password']), '');
    at_db_select($_POST['db_name'], $db);
}
if(isset($_POST['submit']) && ($_POST['action'] == 'process')) {
	unset($errors);
	if (version_compare($_POST['step1']['old_version'], '1.5', '<')) {
		$_POST['admin_username'] = trim($_POST['admin_username']);
		$_POST['admin_password'] = trim($_POST['admin_password']);
		$_POST['admin_email']    = trim($_POST['admin_email']);
		$_POST['site_name']      = trim($_POST['site_name']);
		$_POST['home_url']	     = trim($_POST['home_url']);

		/* Super Administrator Account checking: */
		if ($_POST['admin_username'] == ''){
			$errors[] = 'Administrator username cannot be empty.';
		} else {
			/* check for special characters */
			if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $_POST['admin_username']))){
				$errors[] = 'Administrator username is not valid.';
			}
		}
		if ($_POST['admin_password'] == '') {
			$errors[] = 'Administrator password cannot be empty.';
		}
		if ($_POST['admin_email'] == '') {
			$errors[] = 'Administrator email cannot be empty.';
		} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['admin_email'])) {
			$errors[] = 'Administrator email is not valid.';
		}

		/* System Preferences checking: */
		if ($_POST['email'] == '') {
			$errors[] = 'Contact email cannot be empty.';
		} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
			$errors[] = 'Contact email is not valid.';
		}

		if (!isset($errors)) {
			$sql = "INSERT INTO ".$_POST['step1']['tb_prefix']."admins VALUES ('$_POST[admin_username]', '$_POST[admin_password]', '', '$_POST[admin_email]', 'en', 1, NOW())";
			$result= queryDB($sql, array());
			unset($_POST['admin_username']);
			unset($_POST['admin_password']);
			unset($_POST['admin_email']);
		}
	}
	if (version_compare($_POST['step1']['old_version'], '1.5.2', '<')) {
		// update config table
		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('contact_email', '".urldecode($_POST['step1']['contact_email'])."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('email_notification', '".($_POST['step1']['email_notification'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('allow_instructor_requests', '".($_POST['step1']['allow_instructor_requests'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('auto_approve_instructors', '".($_POST['step1']['auto_approve'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('max_file_size', '".(int) $_POST['step1']['max_file_size']."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('max_course_size', '".(int) $_POST['step1']['max_course_size']."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('max_course_float', '".(int) $_POST['step1']['max_course_float']."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('illegal_extentions', '".str_replace(',','|',urldecode($_POST['step1']['ill_ext']))."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('site_name', '".urldecode($_POST['step1']['site_name'])."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('home_url', '".urldecode($_POST['step1']['home_url'])."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('default_language', 'en')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('cache_dir', '".urldecode($_POST['step1']['cache_dir'])."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('enable_category_themes', '".($_POST['step1']['theme_categories'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('course_backups', '". (int) $_POST['step1']['course_backups']."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('email_confirmation', '".($_POST['step1']['email_confirmation'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('master_list', '".($_POST['step1']['master_list'] ? 1 : 0)."')";
		queryDB($sql, array());

		$sql = "REPLACE INTO ".$_POST['step1']['tb_prefix']."config VALUES ('enable_handbook_notes', '".($_POST['step1']['enable_handbook_notes'] ? 1 : 0)."')";
		queryDB($sql, array());

		// check for bits 8192 and 4096 and remove them if they're set.
		$sql = "UPDATE ".$_POST['step1']['tb_prefix']."course_enrollment SET `privileges` = `privileges` - 8192 WHERE `privileges` & 8192";
		queryDB($sql, array());

		$sql = "UPDATE ".$_POST['step1']['tb_prefix']."course_enrollment SET `privileges` = `privileges` - 4096 WHERE `privileges` & 4096";
		queryDB($sql, array());
	}

	if (version_compare($_POST['step1']['old_version'], '1.5.3', '<')) {

		$sql = "DELETE FROM ".$_POST['step1']['tb_prefix']."groups";
		queryDB($sql, array());

		$sql = "DELETE FROM ".$_POST['step1']['tb_prefix']."groups_members";
		queryDB($sql, array());

		$sql = "DELETE FROM ".$_POST['step1']['tb_prefix']."tests_groups";
		queryDB($sql, array());
	}

	if (version_compare($_POST['step1']['old_version'], '1.6.4', '<')) {
		/* convert all content nodes to the IMS standard. (adds null nodes for all top pages) */
		include('ustep_content_conversion.php');

		// fix all the wrong ordering
		$sql    = "SELECT content_id, content_parent_id, ordering, course_id FROM ".$_POST['step1']['tb_prefix']."content ORDER BY course_id, content_parent_id, ordering";
		$rows_content = queryDB($sql, array());
		foreach($rows_content as $row){
			if ($current_course_id != $row['course_id']) {
				$current_course_id = $row['course_id'];
				unset($current_parent_id);
				unset($ordering);
			}
			if ($current_parent_id != $row['content_parent_id']) {
				$current_parent_id = $row['content_parent_id'];
				$ordering = 1;
			}
		
			if ($row['ordering'] != $ordering) {
				$sql = "UPDATE ".$_POST['step1']['tb_prefix']."content SET ordering=$ordering WHERE content_id=$row[content_id]";
				queryDB($sql, array());
			}
		
			 echo "\n";
		
			$ordering++;
		}
		
		/* Convert db to a tree */
		$sql = 'SELECT distinct course_id FROM '.$_POST['step1']['tb_prefix'].'content';
		$rows_course = queryDB($sql, array());
		
		foreach($rows_course as $row_course){
			$sql = 'SELECT * FROM '.$_POST['step1']['tb_prefix'].'content WHERE course_id='.$row_course['course_id'];
			$rows_content = queryDB($sql, array());
			
			$content_array = array(); 
	        foreach($rows_content as $row){
				$content_array[$row['content_parent_id']][$row['ordering']] = $row['content_id'];
			}

			$tree = buildTree($content_array[0], $content_array);

			/* Restructure the tree */
			$tree = rebuild($tree);

			
			/* Update the Db based on this new tree */
			reconstruct($tree, '', 0, $_POST['step1']['tb_prefix']);
		}
	}

	/* deal with the extra modules: */
	/* for each module in the modules table check if that module still exists in the mod directory. */
	/* if that module does not exist then check the old directory and prompt to have it copied */
	/* or delete it from the modules table. or maybe disable it instead? */
	if (version_compare($_POST['step1']['old_version'], '1.5.1', '>')) {
	//global $db;
		define('TABLE_PREFIX', $_POST['step1']['tb_prefix']);
		if(!isset($db)){
		    if(defined('MYSQLI_ENABLED')){
                $db = at_db_connect($_POST['step1']['db_host'], $_POST['step1']['db_port'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']), $_POST['step1']['db_name']);   
            }else{
                $db = at_db_connect($_POST['step1']['db_host'], $_POST['step1']['db_port'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']), '');
                at_db_select($_POST['step1']['db_name'], $db);
            }
		}
		require(AT_INCLUDE_PATH . '../mods/_core/modules/classes/Module.class.php');
		$moduleFactory = new ModuleFactory(FALSE);
		$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_DISABLED | AT_MODULE_STATUS_ENABLED);
		$keys = array_keys($module_list);
		foreach($keys as $dir_name) {
			$module =& $module_list[$dir_name];
			$module->setIsMissing($module->isExtra());
		}
	}

	/* fixed the typo of "fuild" theme that was introduced in 1.6.1 : */
	if (version_compare($_POST['step1']['new_version'], '1.6.0', '>')) {

		$sql = "UPDATE ".$_POST['step1']['tb_prefix']."themes 
		           SET title='Fluid', dir_name='fluid'
		         WHERE dir_name='fuild'";
		queryDB($sql, array());
		
		$sql = 'UPDATE '.$_POST['step1']['tb_prefix'].'config 
		           SET value=replace(value, \':"fuild";\', \':"fluid";\') 
		         WHERE name=\'pref_defaults\'';
		queryDB($sql, array());

		$sql = 'UPDATE '.$_POST['step1']['tb_prefix'].'members 
		           SET preferences=replace(preferences, \':"fuild";\', \':"fluid";\')';
		queryDB($sql, array());
	}

	/* Saved the atutor installation path into "config" table after 2.0.2 */
	if (version_compare($_POST['step1']['new_version'], '2.0.2', '>')) {
		// Calculate the ATutor installation path and save into database for the usage of
		// session associated path @ include/vitals.inc.php
		$sql = "INSERT INTO ".$_POST['step1']['tb_prefix']."config VALUES ('session_path', '".get_atutor_installation_path(AT_UPGRADE_INCLUDE_PATH)."')";
		queryDB($sql ,array());
	}

	if (!isset($errors)) {
		unset($errors);
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}


?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

<?php if (version_compare($_POST['step1']['old_version'], '1.5', '<')): ?>
	<p>Below are new configuration options that are available for this version.</p>

	<br />
		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Super Administrator</th>
		</tr>
		<tr>
			<td class="row1" colspan="2">The Super Administrator account is used for managing ATutor. Since ATutor version 1.5 the Super Administrator can also create additional Administrators each with their own privileges and roles.</td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="username">Administrator Username:</label></b><br />
			May contain only letters, numbers, or underscores.</td>
			<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo $stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo urldecode($_POST['step1']['admin_username']); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="password">Administrator Password:</label></b></td>
			<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" value="<?php if (!empty($_POST['admin_password'])) { echo $stripslashes(htmlspecialchars($_POST['admin_password'])); } else { echo urldecode($_POST['step1']['admin_password']); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="email">Administrator Email:</label></b></td>
			<td class="row1"><input type="text" name="admin_email" id="email" size="30" value="<?php if (!empty($_POST['admin_email'])) { echo $stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo urldecode($_POST['step1']['admin_email']); } ?>" class="formfield" /></td>
		</tr>
		</table>

		<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">System Preferences</th>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="cemail">Contact Email:</label></b><br />
			The email that will be used as the return email when needed and when instructor account requests are made.</td>
			<td class="row1"><input type="text" name="email" id="cemail" size="30" value="<?php if (!empty($_POST['email'])) { echo $stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo urldecode($_POST['step1']['admin_email']); } ?>" class="formfield" /></td>
		</tr>
		</table>
<?php endif; ?>
<?php if (version_compare($_POST['step1']['old_version'], '1.5.3', '<')): ?>
	<p>Groups made prior to 1.5.3 are not backwards compatible and will be removed.</p>
<?php else: ?>
	<p>There are no new configuration options for this version.</p>
<?php endif; ?>

	<br />
	<br />
	<div align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></div>
</form>