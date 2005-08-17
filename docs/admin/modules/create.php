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

require(AT_INCLUDE_PATH.'lib/mods.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$dirs = find_mods();

?>
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="dir"><?php echo _AT('directory'); ?></label><br />
		<select name="dir" id="dir">
			<?php foreach ($dirs as $dir): ?>
				<option value="<?php echo $dir['dir_name']; ?>"><?php echo $dir['dir_name']; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php echo _AT('module_name'); ?></label><br />
		<input type="text" name="name" id="name" value="" />
	</div>

	<div class="row">
		<label for="desc"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" id="desc" cols="10" rows="2"></textarea>
	</div>

	<div class="row">
		<label for="maintainers"><?php echo _AT('maintainers'); ?></label><br />
		<input type="">
	</div>

	<div class="row">
		<label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" id="url" value="" />
	</div>

	<div class="row">
		<label for="version"><?php echo _AT('version'); ?></label><br />
		<input type="text" name="version" id="version" value="" />
	</div>

	<div class="row">
		<label for="priv"><?php echo _AT('use_privilege'); ?></label><br />
	</div>

	<div class="row">
		<?php echo _AT('date'); ?>
	</div>

	<div class="row">
		<label for="license"><?php echo _AT('license'); ?></label><br />
		<input type="text" name="license" id="license" value="" />
	</div>

	<div class="row">
		<?php echo _AT('state'); ?>
	</div>

	<div class="row">
		<label for="notes"><?php echo _AT('notes'); ?></label><br />
		<textarea name="notes" id="notes" cols="10" rows="2"></textarea>
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>