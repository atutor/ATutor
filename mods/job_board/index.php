<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

define(AT_INCLUDE_PATH, '../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

$job = new Job();
$all_job_posts = $job->getAllJobs();

include(AT_INCLUDE_PATH.'header.inc.php');?>
<div class="jb_head">
	<div class="jb_add_posting">
		<a href="add_new_post.php"><?php echo _AT('jb_add_new_post');?></a>
	</div>
	<div class="jb_search">
		<form action="" method="get">
			<label for="jb_search"><?php echo _AT('jb_search'); ?></label>
			<input type="text" id="jb_search" name="jb_search" value="" />
			<input type="submit" name="jb_submit" value="<?php echo _AT('search'); ?>" />
		</form>
	</div>
</div>
<div style="clear:both;"></div>
<div>
<?php
$savant->assign('all_job_posts', $all_job_posts);
$savant->display('jb_posting_table.tmpl.php');
?>
</div>

<?php include(AT_INCLUDE_PATH.'footer.inc.php'); ?>