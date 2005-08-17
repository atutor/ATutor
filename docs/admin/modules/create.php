<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: add_new.php 5300 2005-08-17 15:22:09Z heidi $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

unset($module_xml);

if (isset($_POST['submit'])) {
	require('./module.template.php');
	
	$maintainers_xml = '';
	if (isset($_POST['name_1'])) {
		$maintainers_xml .= str_replace(array('{NAME}', '{EMAIL}'), array(stripslashes($addslashes($_POST['name_1'])), stripslashes($addslashes($_POST['email_1']))), $maintainer_xml);
	}

	if (isset($_POST['name_2'])) {
		$maintainers_xml .= str_replace(array('{NAME}', '{EMAIL}'), array(stripslashes($addslashes($_POST['name_2'])), stripslashes($addslashes($_POST['email_2']))), $maintainer_xml);
	}

	$tokens = array('{NAME}', '{DESCRIPTION}', '{MAINTAINERS}', '{URL}', '{VERSION}', '{USER_PRIVILEGE}', '{DATE}', '{LICENSE}', '{STATE}', '{NOTES}', '{FILELIST}');
	$replace = array(stripslashes($addslashes($_POST['name'])),
					stripslashes($addslashes($_POST['description'])), 
					$maintainers_xml, 
					stripslashes($addslashes($_POST['url'])),
					stripslashes($addslashes($_POST['version'])),
					'priv',
					time(),
					stripslashes($addslashes($_POST['license'])),
					'moo',
					stripslashes($addslashes($_POST['notes'])),
					'list');

	$module_xml = str_replace($tokens, $replace, $module_xml);
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($module_xml)) :  ?>
	<div class="input-form">
		<div class="row"><pre><?php highlight_string($module_xml); ?></pre></div>
	</div>

<?php else: ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php echo _AT('module_name'); ?></label><br />
		<input type="text" name="name" id="name" size="40" value="" />
	</div>

	<div class="row">
		<label for="desc"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" id="desc" cols="10" rows="2"></textarea>
	</div>

	<div class="row">
		<?php echo _AT('maintainers'); ?>
		<ol style="margin-top: 0px; margin-bottom: 0px;">
			<li style="margin-bottom: 5px;"><label for="name_1"><?php echo _AT('name'); ?></label> <input type="text" name="name_1" id="name_1" value="" />
				<label for="email_1"><?php echo _AT('email'); ?></label> <input type="text" name="email_1" id="email_1" value="" /></li>

			<li><label for="name_2"><?php echo _AT('name'); ?></label> <input type="text" name="name_2" id="name_2" value="" />
		<label for="email_2"><?php echo _AT('email'); ?></label> <input type="text" name="email_2" id="email_2" value="" /></li>
		</ol>
	</div>

	<div class="row">
		<label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" id="url" size="50" value="" />
	</div>

	<div class="row">
		<label for="version"><?php echo _AT('version'); ?></label><br />
		<input type="text" name="version" id="version" size="5" value="" />
	</div>

	<div class="row">
		<label for="priv"><?php echo _AT('use_privilege'); ?></label><br />
	</div>

	<div class="row">
		<?php echo _AT('date'); ?>
	</div>

	<div class="row">
		<label for="license"><?php echo _AT('license'); ?></label><br />
		<input type="text" name="license" id="license" size="60" value="GPL" />
	</div>

	<div class="row">
		<?php echo _AT('state'); ?>
	</div>

	<div class="row">
		<label for="notes"><?php echo _AT('notes'); ?></label><br />
		<textarea name="notes" id="notes" cols="10" rows="2"></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
	</div>
</div>
</form>
<?php endif; ?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>