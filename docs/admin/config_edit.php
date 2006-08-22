<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['site_name']          = trim($_POST['site_name']);
	$_POST['home_url']           = trim($_POST['home_url']);
	$_POST['default_language']   = trim($_POST['default_language']);
	$_POST['contact_email']      = trim($_POST['contact_email']);
	$_POST['max_file_size']      = intval($_POST['max_file_size']);
	$_POST['max_file_size']      = max(0, $_POST['max_file_size']);
	$_POST['max_course_size']    = intval($_POST['max_course_size']);
	$_POST['max_course_size']    = max(0, $_POST['max_course_size']);
	$_POST['max_course_float']   = intval($_POST['max_course_float']);
	$_POST['max_course_float']   = max(0, $_POST['max_course_float']);
	$_POST['master_list']        = intval($_POST['master_list']);
	$_POST['email_confirmation'] = intval($_POST['email_confirmation']);
	$_POST['email_notification'] = intval($_POST['email_notification']);
	$_POST['allow_instructor_requests'] = intval($_POST['allow_instructor_requests']);
	$_POST['auto_approve_instructors']  = intval($_POST['auto_approve_instructors']);
	$_POST['theme_categories']          = intval($_POST['theme_categories']);
	$_POST['user_notes']                = intval($_POST['user_notes']);
	$_POST['illegal_extentions']        = str_replace(array('  ', ' '), array(' ','|'), $_POST['illegal_extentions']);
	$_POST['cache_dir']                 = trim($_POST['cache_dir']);
	$_POST['course_backups']            = intval($_POST['course_backups']);
	$_POST['course_backups']            = max(0, $_POST['course_backups']);
	$_POST['check_version']             = $_POST['check_version'] ? 1 : 0;
	$_POST['fs_versioning']             = $_POST['fs_versioning'] ? 1 : 0;
	$_POST['enable_mail_queue']         = $_POST['enable_mail_queue'] ? 1 : 0;
	$_POST['display_name_format']       = $_POST['display_name_format'];

	if (!isset($display_name_formats[$_POST['display_name_format']])) {
		$_POST['display_name_format'] = $_config_defaults['display_name_format'];
	}

	//check that all values have been set	
	if (!$_POST['site_name']) {
		$msg->addError('NO_SITE_NAME');
	}

	/* email check */
	if (!$_POST['contact_email']) {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['contact_email'])) {
		$msg->addError('EMAIL_INVALID');	
	}

	if ($_POST['cache_dir']) {
		if (!is_dir($_POST['cache_dir'])) {
			$msg->addError('CACHE_DIR_NOT_EXIST');
		} else if (!is_writable($_POST['cache_dir'])){
			$msg->addError('CACHE_DIR_NOT_WRITEABLE');
		}
	}

	if (!$msg->containsErrors()) {
		$_POST['site_name']     = $addslashes($_POST['site_name']);
		$_POST['home_url']      = $addslashes($_POST['home_url']);
		$_POST['default_language']      = $addslashes($_POST['default_language']);
		$_POST['contact_email'] = $addslashes($_POST['contact_email']);

		foreach ($_config as $name => $value) {
			// the isset() is needed to avoid overridding settings that don't get set here (ie. modules)
			if (isset($_POST[$name]) && (stripslashes($_POST[$name]) != $value) && (stripslashes($_POST[$name]) != $_config_defaults[$name])) {
				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('$name', '$_POST[$name]')";
				mysql_query($sql, $db);
				write_to_log(AT_ADMIN_LOG_REPLACE, 'config', mysql_affected_rows($db), $sql);
			} else if (isset($_POST[$name]) && (stripslashes($_POST[$name]) == $_config_defaults[$name])) {
				$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='$name'";
				mysql_query($sql, $db);
				write_to_log(AT_ADMIN_LOG_DELETE, 'config', mysql_affected_rows($db), $sql);
			}
		}

		$msg->addFeedback('SYSTEM_PREFS_SAVED');

		// special case: disabling the mail queue should flush all queued mail:
		if (!$_POST['enable_mail_queue'] && $_POST['old_enable_mail_queue']) {
			require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer;
			$mail->SendQueue();
		}

		header('Location: index.php');
		exit;
	}
}

$onload = 'document.form.sitename.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['submit'])) {

} else {
	$defaults = $_POST;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="sitename"><?php echo _AT('site_name'); ?></label><br />
		<input type="text" name="site_name" size="40" maxlength="60" id="sitename" value="<?php if (!empty($_POST['site_name'])) { echo $stripslashes(htmlspecialchars($_POST['site_name'])); } else { echo $_config['site_name']; } ?>" />
	</div>

	<div class="row">
		<label for="home_url"><?php echo _AT('home_url'); ?></label><br />

		<input type="text" name="home_url" size="50" maxlength="60" id="home_url" value="<?php if (!empty($_POST['home_url'])) { echo $stripslashes(htmlspecialchars($_POST['home_url'])); } else { echo $_config['home_url']; } ?>"  />
	</div>

	<div class="row">
		<label for="default_lang"><?php echo _AT('default_language'); ?></label><br />

		<?php if (!empty($_POST['default_language'])) { 
				$select_lang = $_POST['default_language']; 
			} else { 
				$select_lang = $_config['default_language'];
			} ?>
		<?php if ($disabled): ?>
			<select name="default_language" id="default_lang" disabled="disabled"><option><?php echo $select_lang; ?></option></select>
		<?php else: ?>
			<?php $languageManager->printDropdown($select_lang, 'default_language', 'default_lang'); ?>
		<?php endif; ?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="cemail"><?php echo _AT('contact_email'); ?></label><br />
		<input type="text" name="contact_email" id="cemail" size="40" value="<?php if (!empty($_POST['email'])) { echo $stripslashes(htmlspecialchars($_POST['email'])); } else { echo $_config['contact_email']; } ?>"  />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="maxfile"><?php echo _AT('maximum_file_size'); ?></label> (<?php echo _AT('default'); ?>: <?php echo $_config_defaults['max_file_size']; ?>)<br />
		<input type="text" size="10" name="max_file_size" id="maxfile" value="<?php if (!empty($_POST['max_file_size'])) { echo $stripslashes(htmlspecialchars($_POST['max_file_size'])); } else { echo $_config['max_file_size']; } ?>"  /> <?php echo _AT('bytes'); ?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="maxcourse"><?php echo _AT('maximum_course_size'); ?></label> (<?php echo _AT('default'); ?>: <?php echo $_config_defaults['max_course_size']; ?>)<br />
		<input type="text" size="10" name="max_course_size" id="maxcourse" value="<?php if (!empty($_POST['max_course_size'])) { echo $stripslashes(htmlspecialchars($_POST['max_course_size'])); } else { echo $_config['max_course_size']; } ?>"  /> <?php echo _AT('bytes'); ?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="float"><?php echo _AT('maximum_course_float'); ?></label> (<?php echo _AT('default'); ?>: <?php echo $_config_defaults['max_course_float']; ?>)<br />
		<input type="text" size="10" name="max_course_float" id="float" value="<?php if (!empty($_POST['max_course_float'])) { echo $stripslashes(htmlspecialchars($_POST['max_course_float'])); } else { echo $_config['max_course_float']; } ?>"  /> <?php echo _AT('bytes'); ?>
	</div>

	<div class="row">
		<?php echo _AT('display_name_format'); ?> (<?php echo _AT('default'); ?>: <em><?php echo _AT($display_name_formats[$_config_defaults['display_name_format']], _AT('login_name'), _AT('first_name'), _AT('second_name'), _AT('last_name')); ?></em>)<br />
		<?php foreach ($display_name_formats as $key => $value): ?>
			<input type="radio" name="display_name_format" value="<?php echo $key; ?>" id="dnf<?php echo $key; ?>" <?php if ($_config['display_name_format'] == $key) { echo 'checked="checked"'; }?> /><label for="dnf<?php echo $key; ?>"><em><?php echo _AT($value, _AT('login_name'), _AT('first_name'), _AT('second_name'), _AT('last_name')); ?></em></label><br />
		<?php endforeach; ?>
	</div>

	<div class="row">
		<?php echo _AT('master_list_authentication'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['master_list'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="master_list" value="1" id="ml_y" <?php if ($_config['master_list']) { echo 'checked="checked"'; }?>  /><label for="ml_y"><?php echo _AT('enable'); ?></label> 

		<input type="radio" name="master_list" value="0" id="ml_n" <?php if(!$_config['master_list']) { echo 'checked="checked"'; }?>  /><label for="ml_n"><?php echo $disable_on . _AT('disable') . $disable_off; ?></label>
	</div>

	<div class="row">
		<?php echo _AT('require_email_confirmation'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['require_email_confirmation'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="email_confirmation" value="1" id="ec_y" <?php if ($_config['email_confirmation']) { echo 'checked="checked"'; }?>  /><label for="ec_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="email_confirmation" value="0" id="ec_n" <?php if(!$_config['email_confirmation']) { echo 'checked="checked"'; }?>  /><label for="ec_n"><?php echo _AT('disable'); ?></label>
	</div>
		
	<div class="row">
		<?php echo _AT('allow_instructor_requests'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['allow_instructor_requests'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="allow_instructor_requests" value="1" id="air_y" <?php if($_config['allow_instructor_requests']) { echo 'checked="checked"'; }?>  /><label for="air_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="allow_instructor_requests" value="0" id="air_n" <?php if(!$_config['allow_instructor_requests']) { echo 'checked="checked"'; }?>  /><label for="air_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('instructor_request_email_notification'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['email_notification'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="email_notification" value="1" id="en_y" <?php if ($_config['email_notification']) { echo 'checked="checked"'; }?>  /><label for="en_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="email_notification" value="0" id="en_n" <?php if(!$_config['email_notification']) { echo 'checked="checked"'; }?>  /><label for="en_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('auto_approve_instructors'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['auto_approve_instructors'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="auto_approve_instructors" value="1" id="aai_y" <?php if($_config['auto_approve_instructors']) { echo 'checked="checked"'; }?>  /><label for="aai_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="auto_approve_instructors" value="0" id="aai_n" <?php if(!$_config['auto_approve_instructors']) { echo 'checked="checked"'; }?>  /><label for="aai_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('theme_specific_categories'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['theme_categories'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="theme_categories" value="1" id="tc_y" <?php if($_config['theme_categories']) { echo 'checked="checked"'; }?>  /><label for="tc_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="theme_categories" value="0" id="tc_n" <?php if(!$_config['theme_categories']) { echo 'checked="checked"'; }?>  /><label for="tc_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('user_contributed_notes'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['user_notes'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="user_notes" value="1" id="un_y" <?php if($_config['user_notes']) { echo 'checked="checked"'; }?>  /><label for="un_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="user_notes" value="0" id="un_n" <?php if(!$_config['user_notes']) { echo 'checked="checked"'; }?>  /><label for="un_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<label for="ext"><?php echo _AT('illegal_file_extensions'); ?></label><br />
		<textarea name="illegal_extentions" cols="24" id="ext" rows="2" class="formfield" ><?php if ($_config['illegal_extentions']) { echo str_replace('|',' ',$_config['illegal_extentions']); }?></textarea>
	</div>

	<div class="row">
		<label for="cache"><?php echo _AT('cache_directory'); ?></label><br />
		<input type="text" name="cache_dir" id="cache" size="40" value="<?php if (!empty($_POST['cache_dir'])) { echo $stripslashes(htmlspecialchars($_POST['cache_dir'])); } else { echo $_config['cache_dir']; } ?>"  />
	</div>

	<div class="row">
		<label for="course_backups"><?php echo _AT('course_backups'); ?></label> (<?php echo _AT('default'); ?>: <?php echo $_config_defaults['course_backups']; ?>)<br />
		<input type="text" size="2" name="course_backups" id="course_backups" value="<?php if (!empty($_POST['course_backups'])) { echo $stripslashes(htmlspecialchars($_POST['course_backups'])); } else { echo $_config['course_backups']; } ?>"  />
	</div>

	<div class="row">
		<?php echo _AT('auto_check_new_version'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['check_version'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="check_version" value="1" id="cv_y" <?php if($_config['check_version']) { echo 'checked="checked"'; }?>  /><label for="cv_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="check_version" value="0" id="cv_n" <?php if(!$_config['check_version']) { echo 'checked="checked"'; }?>  /><label for="cv_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('file_storage_version_control'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['fs_versioning'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<input type="radio" name="fs_versioning" value="1" id="cf_y" <?php if($_config['fs_versioning']) { echo 'checked="checked"'; }?>  /><label for="cf_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="fs_versioning" value="0" id="cf_n" <?php if(!$_config['fs_versioning']) { echo 'checked="checked"'; }?>  /><label for="cf_n"><?php echo _AT('disable'); ?></label>
	</div>

	<div class="row">
		<input type="hidden" name="old_enable_mail_queue" value="<?php echo $_config['enable_mail_queue']; ?>" />
		<?php echo _AT('enable_mail_queue'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['enable_mail_queue'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<?php echo _AT('mail_queue_cron'); ?><br />
		<?php if (!$_config['last_cron'] || (time() - (int) $_config['last_cron'] > 2 * 60 * 60)): ?>
			<input type="radio" name="enable_mail_queue" value="1" disabled="disabled" /><?php echo _AT('enable'); ?> <input type="radio" name="enable_mail_queue" value="0" id="mq_n" checked="checked" /><label for="mq_n"><?php echo _AT('disable'); ?></label>
		<?php else: ?>
			<input type="radio" name="enable_mail_queue" value="1" id="mq_y" <?php if($_config['enable_mail_queue']) { echo 'checked="checked"'; }?>  /><label for="mq_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="enable_mail_queue" value="0" id="mq_n" <?php if(!$_config['enable_mail_queue']) { echo 'checked="checked"'; }?>  /><label for="mq_n"><?php echo _AT('disable'); ?></label>
		<?php endif; ?>
	</div>

	<div class="row">
		<?php echo _AT('auto_install_languages'); ?> (<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['auto_install_languages'] ? _AT('enable') : _AT('disable')); ?>)<br />
		<?php echo _AT('auto_install_languages_cron'); ?><br />
		<?php if (!$_config['last_cron'] || (time() - (int) $_config['last_cron'] > 2 * 60 * 60)): ?>
			<input type="radio" name="auto_install_languages" value="1" disabled="disabled" /><?php echo _AT('enable'); ?> <input type="radio" name="auto_install_languages" value="0" id="ai_n" checked="checked" /><label for="ai_n"><?php echo _AT('disable'); ?></label>
		<?php else: ?>
			<input type="radio" name="auto_install_languages" value="1" id="ai_y" <?php if($_config['auto_install_languages']) { echo 'checked="checked"'; }?>  /><label for="ai_y"><?php echo _AT('enable'); ?></label> <input type="radio" name="auto_install_languages" value="0" id="ai_n" <?php if(!$_config['auto_install_languages']) { echo 'checked="checked"'; }?>  /><label for="ai_n"><?php echo _AT('disable'); ?></label>
		<?php endif; ?>
	</div>

	<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>