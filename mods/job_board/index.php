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
<form action="" method="get">
	<div class="jb_head">
		<div class="jb_search">					
				<label for="jb_search"><?php echo _AT('jb_search'); ?></label>
				<input type="text" id="jb_search" name="jb_search" value="" />
				<input type="submit" name="jb_submit" value="<?php echo _AT('search'); ?>" />
			<a onclick="toggleAdvanceSearch()"><?php echo _AT('jb_advanced_search'); ?></a>
			<div class="jb_advance_search" style="display: none;">
			advance search table goes here.
			<?php $savant->display('jb_advance_search_table.tmpl.php');?>
			</div>
		</div>
		<div class="jb_add_posting">
			<a href="<?php echo AT_JB_BASENAME;?>employer/login.php"><?php echo _AT('jb_login');?></a>
			<a href="<?php echo AT_JB_BASENAME;?>employer/registration.php"><?php echo _AT('jb_not_a_member');?></a>
		</div>		
	</div>
</form>
<div style="clear:both;"></div>
<div>
<?php
$savant->assign('all_job_posts', $all_job_posts);
$savant->assign('job_obj', $job);
$savant->display('jb_posting_table.tmpl.php');
?>
</div>

<script type="text/javascript" >
	function toggleAdvanceSearch(){
		var box_state = jQuery('.jb_advance_search').css('display');
		if (box_state == 'none'){
			jQuery('.jb_advance_search').css('display', 'block');
		} else {
			jQuery('.jb_advance_search').css('display', 'none');
		}
	}
</script>

<?php include(AT_INCLUDE_PATH.'footer.inc.php'); ?>