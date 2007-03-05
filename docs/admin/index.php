<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

if ($_config['check_version']) {
	$request = @file('http://atutor.ca/check_atutor_version.php?return');
	if ($request && version_compare(VERSION, $request[0], '<')) {
		$msg->printFeedbacks('ATUTOR_UPDATE_AVAILABLE');
	}
}
?>

<div style="width: 40%; float: right; padding-top: 4px; padding-left: 10px;">

	<?php if ($_config['allow_instructor_requests'] && admin_authenticate(AT_ADMIN_PRIV_USERS, AT_PRIV_RETURN)): ?> 
		<?php
			$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."instructor_approvals";
			$result = mysql_query($sql, $db);
			$row    = mysql_fetch_assoc($result);
		?>

	<div class="input-form" style="width: 98%;">
		<form method="get" action="admin/instructor_requests.php">
			<div class="row">
				<h3><?php echo _AT('instructor_requests'); ?></h3>
				<p><?php echo _AT('instructor_requests_text', $row['cnt']); ?></p>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
			</div>
		</form>
	</div>

	<?php endif; ?>

	<div class="input-form" style="width: 98%;">
		<form method="get" action="http://atutor.ca/check_atutor_version.php" target="_blank">
		<input type="hidden" name="v" value="<?php echo urlencode(VERSION); ?>" />
			<div class="row">
				<h3><?php echo _AT('atutor_version'); ?></h3>
				<p><?php echo _AT('atutor_version_text', VERSION); ?></p>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
			</div>
		</form>
	</div>

	<?php if (false && admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN)): ?>
	<div class="input-form" style="width: 98%;">
		<form method="get" action="<?php echo AT_BASE_HREF; ?>admin/fix_content.php">
			<div class="row">
				<h3><?php echo _AT('fix_content_ordering'); ?></h3>
				<p><?php echo _AT('fix_content_ordering_text'); ?></p>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
			</div>
		</form>
	</div>
	<?php endif; ?>
</div>

<div style="width: 55%;">
	<?php
	$path_length = strlen($_base_path);

	echo '<ol id="tools" style="margin-right: 0px;">';
	foreach ($_top_level_pages as $page_info) {
		echo '<li class="top-tool"><a href="' . $page_info['url'] . '">' . $page_info['title'] . '</a>  ';

		$page_info['url'] = substr($page_info['url'], $path_length);

		if ($_pages[$page_info['url']]['children']) {
			echo '<ul class="child-top-tool">';
			foreach ($_pages[$page_info['url']]['children'] as $child) {
				echo ' <li class="child-tool"><a href="'.$child.'">'._AT($_pages[$child]['title_var']).'</a></li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ol>';
?>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>