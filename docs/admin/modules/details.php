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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'classes/Module/ModuleParser.class.php');
require(AT_INCLUDE_PATH.'lib/mods.inc.php');

if (isset($_GET['mod_dir'], $_GET['enable'])) {
	enable($dir_name);
	$msg->addFeedback('MOD_ENABLED');
} else if (isset($_GET['mod_dir'], $_GET['disable'])) {
	disable($dir_name);
	$msg->addFeedback('MOD_DISABLED');

} else if (isset($_GET['mod_dir'], $_GET['details'])) {
	header('Location: details.php?mod='.$_GET['mod_dir']);
	exit;

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['detail'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$moduleParser   =& new ModuleParser();

$_GET['mod'] = str_replace(array('.','..','/'), '', $_GET['mod']);

if (!file_exists('../../mods/'.$_GET['mod'].'/module.xml')) {
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$moduleParser->parse(file_get_contents('../../mods/'.$_GET['mod'].'/module.xml'));

?>
<form method="get" action="admin/modules/index.php">
<div class="input-form">
	<div class="row">
		<h3><?php echo $moduleParser->rows[0]['name']; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('description'); ?><br />
		<?php echo nl2br($moduleParser->rows[0]['description']); ?>
	</div>

	<div class="row">
		<?php echo _AT('maintainers'); ?><br />
			<ul>
				<?php foreach ($moduleParser->rows[0]['maintainers'] as $maintainer): ?>
					<li><?php echo $maintainer['name'] .'&lt; '.$maintainer['email'].'&gt;'; ?></li>
				<?php endforeach; ?>
			</ul>
	</div>

	<div class="row">
		<?php echo _AT('url'); ?><br />
		<?php echo $moduleParser->rows[0]['url']; ?>
	</div>

	<div class="row">
		<?php echo _AT('version'); ?><br />
		<?php echo $moduleParser->rows[0]['version']; ?>
	</div>

	<div class="row">
		<?php echo _AT('date'); ?><br />
		<?php echo $moduleParser->rows[0]['date']; ?>
	</div>

	<div class="row">
		<?php echo _AT('license'); ?><br />
		<?php echo $moduleParser->rows[0]['license']; ?>
	</div>

	<div class="row">
		<?php echo _AT('state'); ?><br />
		<?php echo $moduleParser->rows[0]['state']; ?>
	</div>

	<div class="row">
		<?php echo _AT('notes'); ?><br />
		<?php echo $moduleParser->rows[0]['notes']; ?>
	</div>

	<div class="row">
		<?php echo _AT('files'); ?><br />
			<ul>
				<?php foreach ($moduleParser->rows[0]['files'] as $file): ?>
					<li><?php echo $file['baseinstalldir'] .'/'.$file['name']; ?></li>
				<?php endforeach; ?>
			</ul>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="Back" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>