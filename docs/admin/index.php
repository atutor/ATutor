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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE) { 
	$msg->addWarning('TRANSLATE_ON');	
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."instructor_approvals";
$result = mysql_query($sql, $db);
$row    = mysql_fetch_assoc($result);
?>

<div id="guide">
	<a href="<?php echo AT_GUIDES_PATH; ?>admin/2.0.configuration.html" target="_new" title="Read the Administrator Handbook's Configuration Guide"><em>Configuration</em> Guide</a>
</div>


<form method="get" action="admin/instructor_requests.php">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('instructor_requests'); ?></h3>
			<p><?php echo _AT('instructor_requests_text', $row['cnt']); ?></strong></p>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
		</div>
	</div>
</form>


<form method="get" action="http://atutor.ca/check_atutor_version.php">
	<input type="hidden" name="v" value="<?php echo urlencode(VERSION); ?>" />
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('atutor_version'); ?></h3>
			<p><?php echo _AT('atutor_version_text', VERSION); ?></strong></p>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		</div>
	</div>
</form>

<form method="get" action="<?php echo $_base_href; ?>admin/fix_content.php">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('fix_content_ordering'); ?></h3>
			<p><?php echo _AT('fix_content_ordering_text'); ?></p>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>