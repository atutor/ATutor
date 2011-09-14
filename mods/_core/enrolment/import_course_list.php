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

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ENROLLMENT);

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form enctype="multipart/form-data" action="mods/_core/enrolment/verify_list.php" method="post">
<input type="hidden" name="from" value="import" />
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import'); ?></legend>
	<div class="row">
		<p><?php echo _AT('list_import_howto'); ?></p>
	</div>

	<div class="row">
		<label for="sep_choice"><?php echo _AT('import_sep_txt'); ?></label><br />
		<input type="radio" name="sep_choice" id="und" value="_" checked="checked" />
		<label for="und"><?php echo _AT('underscore'); ?></label>
		<input type="radio" name="sep_choice" id="per" value="." />
		<label for="per"><?php echo _AT('period'); ?></label>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="course_list"><?php echo _AT('list_import_course_list'); ?></label><br />
		<input type="file" name="file" id="course_list" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('list_import_course_list');  ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>