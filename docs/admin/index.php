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

	<div class="input-form" style="width: 98%">
		<?php
			if (!isset($_config['db_size']) || ($_config['db_size_ttl'] < time())) {
				$_config['db_size'] = 0;
				$sql = 'SHOW TABLE STATUS';
				$result = mysql_query($sql, $db);
				while($row = mysql_fetch_assoc($result)) {
					$_config['db_size'] += $row['Data_length']+$row['Index_length'];
				}

				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('db_size', '{$_config['db_size']}')";
				mysql_query($sql, $db);

				// get disk usage if we're on *nix
				if (DIRECTORY_SEPARATOR == '/') {
					$du = shell_exec('du -sk '.escapeshellcmd(AT_CONTENT_DIR));
					if ($du) {
						$_config['du_size'] = (int) $du;
						$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('du_size', '{$_config['du_size']}')";
						mysql_query($sql, $db);
					}
				}

				$ttl = time() + 24 * 60 * 60; // every 1 day.
				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('db_size_ttl', '$ttl')";
				mysql_query($sql, $db);
			}

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."courses";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_courses = $row['cnt'];

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_users = $row['cnt'];

			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."admins";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$num_users += $row['cnt'];

			$sql = "SELECT VERSION()";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_array($result);
			$mysql_version = $row[0];
		?>

		<div class="row">
			<h3><?php echo _AT('statistics_information'); ?></h3>

			<dl class="col-list">
				<?php if ($_config['db_size']): ?>
					<dt><?php echo _AT('database'); ?>:</dt>
					<dd><?php echo number_format($_config['db_size']/AT_KBYTE_SIZE/AT_KBYTE_SIZE,2); ?> <acronym title="<?php echo _AT('megabytes'); ?>"><?php echo _AT('mb'); ?></acronym></dd>
				<?php endif; ?>

				<?php if ($_config['du_size']): ?>
					<dt><?php echo _AT('disk_usage'); ?>:</dt>
					<dd><?php echo number_format($_config['du_size']/AT_KBYTE_SIZE,2); ?> <acronym title="<?php echo _AT('megabytes'); ?>"><?php echo _AT('mb'); ?></acronym></dd>
				<?php endif; ?>

				<dt><?php echo _AT('courses'); ?>:</dt>
				<dd><?php echo $num_courses; ?></dd>

				<dt><?php echo _AT('users'); ?>:</dt>
				<dd><?php echo $num_users; ?></dd>

				<dt><?php echo _AT('atutor_version'); ?>:</dt>
				<dd><?php echo _AT('atutor_version_text', VERSION, urlencode(VERSION)); ?></dd>

				<dt><?php echo _AT('php_version'); ?>:</dt>
				<dd><?php echo PHP_VERSION; ?></dd>

				<dt><?php echo _AT('mysql_version'); ?>:</dt>
				<dd><?php echo $mysql_version; ?></dd>

				<dt><?php echo _AT('os'); ?>:</dt>
				<dd><?php echo php_uname('s') . ' ' . php_uname('r'); ?></dd>
			</dl>
		</div>
	</div>

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