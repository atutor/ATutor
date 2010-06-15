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

	global $db, $addslashes;
	$jid = intval($_GET['jid']);
	$member_id = $_SESSION['member_id'];
	$job_board = false;

	//if this is a public page, or guest, then disable job cart.
	if($member_id < 1){
		return;
	}

	//Check if this job has been added.
	$sql = 'SELECT job_id FROM '.TABLE_PREFIX."jb_jobcart WHERE job_id=$jid AND member_id=$member_id";
	$result = mysql_query($sql, $db);
	if ($result){
		$row = mysql_fetch_assoc($result);
		if ($row['job_id']!=''){
			$job_added = true;
		} else {
			$job_added = false;
		}
	}
?>

<?php if ($job_added): ?>
<div class="add_to_cart">
	<a href="<?php echo AT_JB_BASENAME; ?>view_post.php?action=remove_from_cart<?php echo SEP;?>jid=<?php echo $jid; ?>" title="<?php echo _AT('jb_remove_from_cart');?>"><?php echo _AT('jb_remove_from_cart');?></a>
</div>
<?php else: ?>
<div class="add_to_cart">
	<a href="<?php echo AT_JB_BASENAME; ?>view_post.php?action=add_to_cart<?php echo SEP;?>jid=<?php echo $jid; ?>" title="<?php echo _AT('jb_add_to_cart');?>"><?php echo _AT('jb_add_to_cart');?></a>
</div>
<?php endif; ?>