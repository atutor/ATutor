<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: create.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

unset($module_xml);

if (isset($_POST['submit'])) {
	require('./module.template.php');
	
	$maintainers_xml = '';
	if (isset($_POST['name_1'], $_POST['email_1']) && $_POST['name_1'] && $_POST['email_1']) {
		$maintainers_xml .= str_replace(array('{NAME}', '{EMAIL}'), array($stripslashes($_POST['name_1']), $stripslashes($_POST['email_1'])), $maintainer_xml);
	}

	if (isset($_POST['name_2'], $_POST['email_2']) && $_POST['name_2'] && $_POST['email_2']) {
		$maintainers_xml .= str_replace(array('{NAME}', '{EMAIL}'), array($stripslashes($_POST['name_2']), $stripslashes($_POST['email_2'])), $maintainer_xml);
	}
	$maintainers_xml .= "\n";
	$tokens = array('{NAME}', '{DESCRIPTION}', '{MAINTAINERS}', '{URL}', '{VERSION}', '{USER_PRIVILEGE}', '{DATE}', '{LICENSE}', '{STATE}', '{NOTES}');
	$replace = array($stripslashes($_POST['name']),
					$stripslashes($_POST['description']),
					$maintainers_xml, 
					$stripslashes($_POST['url']),
					$stripslashes($_POST['version']),
					$stripslashes($_POST['priv']),
					$stripslashes($_POST['date']),
					$stripslashes($_POST['license']),
					$stripslashes($_POST['state']),
					$stripslashes($_POST['notes']));

	$module_xml = str_replace($tokens, $replace, $module_xml);
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($module_xml)) :  ?>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="input-form">
			<div class="row"><pre><?php highlight_string($module_xml); ?></pre></div>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
		</div>
	</div>
	</form>
<?php else: ?>

[[ this form is used to generate the module.xml file which must be packaged with each module ]]

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<h3><?php echo _AT('module_details'); ?></h3>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="name"><?php echo _AT('module_name'); ?></label><br />
		<input type="text" name="name" id="name" size="40" value="" />
	</div>

	<div class="row">
		<label for="desc"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" id="desc" cols="10" rows="2"></textarea>
	</div>

	<div class="row">
		<?php echo _AT('maintainers'); ?>
		<ol style="margin-top: 0px; margin-bottom: 0px;">
			<li style="margin-bottom: 5px;"><label for="name_1"><?php echo _AT('name'); ?></label> <input type="text" name="name_1" id="name_1" size="25" value="" />
				<label for="email_1"><?php echo _AT('email'); ?></label> <input type="text" name="email_1" id="email_1" size="35" value="" /></li>

			<li><label for="name_2"><?php echo _AT('name'); ?></label> <input type="text" name="name_2" id="name_2" size="25" value="" />
		<label for="email_2"><?php echo _AT('email'); ?></label> <input type="text" name="email_2" id="email_2" size="35" value="" /></li>
		</ol>
	</div>

	<div class="row">
		<label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" id="url" size="50" value="http://" />
	</div>

	<div class="row">
		<label for="license"><?php echo _AT('license'); ?></label><br />
		<input type="text" name="license" id="license" size="65" value="GPL" />
	</div>


	<h3><?php echo _AT('release_details'); ?></h3>

	<div class="row">
		<label for="version"><?php echo _AT('version'); ?></label><br />
		<input type="text" name="version" id="version" size="5" value="" />
	</div>

	<div class="row">
		<?php echo _AT('use_custom_privilege'); ?><br />
		<input type="radio" name="priv" value="false" id="priv_1" checked="checked" /><label for="priv_1"><?php echo _AT('no'); ?></label>, 
		<input type="radio" name="priv" value="true"  id="priv_2" /><label for="priv_2"><?php echo _AT('yes'); ?></label>
	</div>

	<div class="row">
		<label for="date"><?php echo _AT('date'); ?></label><br />
		<input type="text" name="date" id="date" value="" />
	</div>

	<div class="row">
		<?php echo _AT('state'); ?><br />
			<input type="radio" name="state" value="stable"       id="state_1" checked="checked" /><label for="state_1"><?php echo _AT('stable'); ?></label>, 
			<input type="radio" name="state" value="beta"         id="state_2" /><label for="state_2"><?php echo _AT('beta'); ?></label>,
			<input type="radio" name="state" value="experimental" id="state_3" /><label for="state_3"><?php echo _AT('experimental'); ?></label>
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