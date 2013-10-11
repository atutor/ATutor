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
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

global $stripslashes;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	$_POST['site_name']          = trim($_POST['site_name']);
	$_POST['home_url']           = trim($_POST['home_url']);
	$_POST['default_language']   = trim($_POST['default_language']);
	$_POST['contact_email']      = trim($_POST['contact_email']);
	$_POST['session_timeout']   = intval($_POST['session_timeout']);
	$_POST['max_file_size']      = intval($_POST['max_file_size']);
	$_POST['max_file_size']      = max(0, $_POST['max_file_size']);
	$_POST['max_course_size']    = intval($_POST['max_course_size']);
	$_POST['max_course_size']    = max(0, $_POST['max_course_size']);
	$_POST['max_course_float']   = intval($_POST['max_course_float']);
	$_POST['max_course_float']   = max(0, $_POST['max_course_float']);
	$_POST['allow_registration']   = intval($_POST['allow_registration']);
	$_POST['allow_browse']   = intval($_POST['allow_browse']);
	$_POST['show_current']   = intval($_POST['show_current']);
	$_POST['allow_instructor_registration']   = intval($_POST['allow_instructor_registration']);
	$_POST['allow_unenroll']   = intval($_POST['allow_unenroll']);
	$_POST['master_list']        = intval($_POST['master_list']);
	$_POST['email_confirmation'] = intval($_POST['email_confirmation']);
	$_POST['email_notification'] = intval($_POST['email_notification']);
	$_POST['sent_msgs_ttl']      = intval($_POST['sent_msgs_ttl']);
	$_POST['allow_instructor_requests'] = intval($_POST['allow_instructor_requests']);
	$_POST['disable_create'] 			= intval($_POST['disable_create']);
	$_POST['auto_approve_instructors']  = intval($_POST['auto_approve_instructors']);
	$_POST['theme_categories']          = intval($_POST['theme_categories']);
	$_POST['user_notes']                = intval($_POST['user_notes']);
	$_POST['illegal_extentions']        = str_replace(array('  ', ' '), array(' ','|'), $_POST['illegal_extentions']);
	$_POST['cache_dir']                 = trim($_POST['cache_dir']);
	$_POST['cache_life']                = intval($_POST['cache_life']);
	$_POST['latex_server']				= (trim($_POST['latex_server'])==''?$_config['latex_server']:trim($_POST['latex_server']));
	$_POST['course_backups']            = intval($_POST['course_backups']);
	$_POST['course_backups']            = max(0, $_POST['course_backups']);
	$_POST['check_version']             = $_POST['check_version'] ? 1 : 0;
	$_POST['fs_versioning']             = $_POST['fs_versioning'] ? 1 : 0;
	$_POST['enable_mail_queue']         = $_POST['enable_mail_queue'] ? 1 : 0;
	$_POST['display_name_format']       = intval($_POST['display_name_format']);
	$_POST['pretty_url']				= intval($_POST['pretty_url']);
	$_POST['course_dir_name']			= intval($_POST['course_dir_name']);
	$_POST['max_login']					= intval($_POST['max_login']);		//max login attempt
	$_POST['use_captcha']				= $_POST['use_captcha'] ? 1 : 0;


	//apache_mod_rewrite can only be enabled if pretty_url is.
	if ($_POST['pretty_url']==1){
		$_POST['apache_mod_rewrite']		= intval($_POST['apache_mod_rewrite']);
	} else {
		$_POST['apache_mod_rewrite'] = 0;
	}

	if (!isset($display_name_formats[$_POST['display_name_format']])) {
		$_POST['display_name_format'] = $_config_defaults['display_name_format'];
	}

	//check that all values have been set	
	if (!$_POST['site_name']) {
		$missing_fields[] = _AT('site_name');
	}

	/* email check */
	if (!$_POST['contact_email']) {
		$missing_fields[] = _AT('contact_email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['contact_email'])) {
		$msg->addError('EMAIL_INVALID');	
	}

	if ($_POST['cache_dir']) {
		if (!is_dir($_POST['cache_dir'])) {
			$msg->addError('CACHE_DIR_NOT_EXIST');
		} else if (!is_writable($_POST['cache_dir'])){
			$msg->addError('CACHE_DIR_NOT_WRITEABLE');
		}
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['site_name']     = $addslashes($_POST['site_name']);
		$_POST['home_url']      = $addslashes($_POST['home_url']);
		$_POST['default_language']      = $addslashes($_POST['default_language']);
		$_POST['contact_email'] = $addslashes($_POST['contact_email']);
		$_POST['time_zone']     = $addslashes($_POST['time_zone']);

		foreach ($_config as $name => $value) {
			// the isset() is needed to avoid overridding settings that don't get set here (ie. modules)
			if (isset($_POST[$name]) && ($stripslashes($_POST[$name]) != $value) && ($stripslashes($_POST[$name]) != $_config_defaults[$name])) {
				$sql = 'REPLACE INTO %sconfig VALUES ("%s", "%s")';
				$num_rows = queryDB($sql, array(TABLE_PREFIX, $name, $_POST[$name]));
				write_to_log(AT_ADMIN_LOG_REPLACE, 'config', $num_rows, $sqlout);

			} else if (isset($_POST[$name]) && ($stripslashes($_POST[$name]) == $_config_defaults[$name])) {
				$sql = "DELETE FROM %sconfig WHERE name='%s'";
				$num_rows = queryDB($sql, array(TABLE_PREFIX, $name));
				write_to_log(AT_ADMIN_LOG_DELETE, 'config', $num_rows, $sqlout);
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		// special case: disabling the mail queue should flush all queued mail:
		if (!$_POST['enable_mail_queue'] && $_POST['old_enable_mail_queue']) {
			require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer;
			$mail->SendQueue();
		}

		header('Location: '.$_SERVER['PHP_SELF']);
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

<script type="text/javascript">
	function apache_mod_rewrite_toggler(enabled){
		var obj_y = document.getElementById('mr_y');
		var obj_n = document.getElementById('mr_n');
		if(enabled==true) {
			obj_y.disabled = "";	
		} else if (enabled==false){
			obj_y.disabled = "disabled";
			obj_n.checked = "checked";
		}
	}

	//Validate apache_mod data
	var pu_n = document.getElementById('pu_n');
	var obj_y = document.getElementById('mr_y');
	var obj_n = document.getElementById('mr_n');
	if (pu_n.checked == true){
		obj_y.disabled = "disabled";
		obj_n.checked = "checked";
	}
function toggleform(id) {
	if (document.getElementById(id).style.display == "none") {
		//show
		document.getElementById(id).style.display='';	

		if (id == "c_folder") {
			document.form0.new_folder_name.focus();
		} else if (id == "upload") {
			document.form0.file.focus();
		}

	} else {
		//hide
		document.getElementById(id).style.display='none';
	}
}

// -->
</script>



<?php 
$savant->assign('display_name_formats', $display_name_formats);
$savant->display('admin/system_preferences/config_edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>