<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index.php 3850 2005-03-14 15:02:26Z shozubq $

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<?php foreach ($this->courses as $row): ?>	
	<div class="course">
		<h2><a href="bounce.php?course=<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></a></h2>

		<a href="bounce.php?course=<?php echo $row['course_id']; ?>">
			<?php if ($row['icon'] == ''): ?>
				<img src="images/clr.gif" class="icon" border="0" width="79" height="79" />
			<?php else: ?>
				<img src="images/courses/<?php echo $row['icon']; ?>" class="icon" border="0" />
			<?php endif; ?>
		</a>
		<p>
			<?php echo _AT('instructor');?>: <a href=""><?php echo get_login($row['member_id']); ?></a><br />
			<?php echo _AT('my_role');?>: <?php echo $row['role']; ?><br />
		</p>

		<div class="shortcuts">
			<a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><img src="http://marathonman.sourceforge.net/docs/images/ug/resume.gif" border="0" title="Resume Shortcut" /></a>
		</div>
	</div>
<?php endforeach; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>