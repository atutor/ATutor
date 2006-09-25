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
// $Id$

define('AT_INCLUDE_PATH', '../include/');

$CACHE_DEBUG=0;
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'lib/chat.inc.php');
$admin = getAdminSettings();

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<p align="center"><a href="chat/chat.php?firstLoginFlag=1"><strong> <?php echo _AT('enter_chat');  ?></strong></a></p><br />
<?php

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('name' => 1, 'date' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'date';
}

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
	echo '<p>'._AT('chat_none_found').'</p>';
} else {?>
	
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

	<table class="data" rules="cols" summary="">
	<colgroup>
		<?php if ($col == 'name'): ?>
			<col class="sort" />
			<col span="2" />
		<?php elseif($col == 'date'): ?>
			<col span="2" />
			<col class="sort" />
		<?php endif; ?>
	</colgroup>
	<thead>
	<tr>
		<th scope="col"><a href="chat/index.php?<?php echo $orders[$order]; ?>=name"><?php echo _AT('chat_transcript');?></a></th>
		<th scope="col"><?php echo _AT('status'); ?></th>
		<th scope="col"><a href="chat/index.php?<?php echo $orders[$order]; ?>=date"><?php echo _AT('chat_date'); ?></a></th> 
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
		<tr onmousedown="document.location='chat/view_transcript.php?t=<?php echo $file; ?>'">
			<td><a href="chat/view_transcript.php?t=<?php echo $file; ?>"><?php echo $file; ?></a></td>
			<td>
				<?php if (($file.'.html' == $admin['tranFile']) && ($admin['produceTran'])) { 
					echo _AT('chat_currently_active');
				} else {
					echo _AT('chat_inactive');
				}?>
			</td>
	
			<td><?php echo date('Y-m-d h:i:s', $date); ?></td>
		</tr>
	<?php } ?>
	</tbody>

	</table>
</form>
<?php
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
