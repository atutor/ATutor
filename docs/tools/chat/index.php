<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../../include/');

$CACHE_DEBUG=0;
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/chat.inc.php');

if ($_GET['view']) {
	header("Location:view_transcript.php?t=".$_GET['file']);
	exit;
}

if ($_GET['delete']) {
	header("Location:delete_transcript.php?m=".$_GET['file']);
	exit;
}
$admin = getAdminSettings();
require(AT_INCLUDE_PATH.'header.inc.php');


if (isset($_GET['col'])) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'date';
}

if (isset($_GET['order'])) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'desc';
}

${'highlight_'.$col} = ' u';
$tran_files = array();
if (!@opendir(AT_CONTENT_DIR . 'chat/')){
	mkdir(AT_CONTENT_DIR . 'chat/', 0777);
}

if(!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings')){
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'], 0777);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/', 0776);
	@copy('admin.settings.default', AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings');
	@chmod (AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0777);

}
	
if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/')) {
	while (($file = readdir($dir)) !== false) {
		if (substr($file, -strlen('.html')) == '.html') {
			$la	= stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$file);

			$file = str_replace('.html', '', $file);
			$tran_files[$file] = $la['ctime'];
		}
	}
}

if (count($tran_files) == 0) {
	echo '<p>'._AC('chat_none_found').'</p>';
} else {?>
	
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

	<table class="data" rules="cols" summary="">
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col" > <?php echo _AT('chat_transcript');?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=name<?php echo SEP; ?>order=asc" title="<?php echo _AT('chat_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('chat_name_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=name<?php echo SEP; ?>order=desc" title="<?php echo _AT('chat_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('chat_name_descending'); ?>" border="0" height="7" width="11" /></a>
		</th>
		<th scope="col"><?php echo _AT('status'); ?></th>
		<th scope="col"><?php echo _AC('chat_date'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=date<?php echo SEP; ?>order=asc" title="<?php echo _AT('chat_date'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('chat_date_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=date<?php echo SEP; ?>order=desc" title="<?php echo _AT('chat_date_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('chat_date_descending'); ?>" border="0" height="7" width="11" /></a>
		</th> 
	</tr>
	</thead>
	<?php

	if (($col == 'date') && ($order == 'asc')) {
		asort($tran_files);
	} else if (($col == 'date') && ($order == 'desc')) {
		arsort($tran_files);
	} else if (($col == 'name') && ($order == 'asc')) {
		ksort($tran_files);
	} else if (($col == 'name') && ($order == 'desc')) {
		krsort($tran_files);
	}
	reset ($tran_files);
	?>

	<tbody>
	<?php foreach ($tran_files as $file => $date) { ?>
		<tr>
			<td><input type="radio" name="file" value="<?php echo $file; ?>" id="<?php echo $file; ?>" /></td>

			<td><label for="<?php echo $file; ?>"><?php echo $file; ?></label></td>
			<td>
				<?php if (($file.'.html' == $admin['tranFile']) && ($admin['produceTran'])) { 
					echo _AC('chat_currently_active');
				} else {
					echo _AT('chat_inactive');
				}?>
			</td>
	
			<td><?php echo date('Y-m-d h:i:s', $date); ?></td>
		</tr>
	<?php } ?>
	</tbody>

	<tfoot>
	<tr>
		<td colspan="4"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
	</tr>
	</tfoot>

	</table>
</form>
<?php
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>