<?php	
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: preview.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);

if (isset($_POST['back'])) {
	header('Location: index.php');
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$feed_id    = intval($_GET['fid']);
$cache_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss.cache';
$title_file = AT_CONTENT_DIR.'feeds/'.$feed_id.'_rss_title.cache';

if (!file_exists($cache_file) || ((time() - filemtime($cache_file)) > 21600) ) {
	make_cache_file($feed_id);
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
	<div class="row">
		<h3><?php if (file_exists($title_file)) { readfile($title_file); } ?></h3>
	</div>

	<div class="row">
		<?php if (file_exists($cache_file) && filesize($cache_file) > 0) { 
			readfile($cache_file); 
			echo '<p><br /><small>'._AT('new_window').'</small></p>';
		} else {
			echo _AT('no_content_avail');
		}?>
	</div>

	<div class="row buttons">
		<input type="submit" name="back" value="<?php echo _AT('back'); ?>" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>