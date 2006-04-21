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

require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php foreach ($this->courses as $row):?>	
	<div class="course">
		<h6 align="right">
			<img src="<?php echo $this->img; ?>/user.gif" alt="" />
			<?php
			if ($_SESSION['member_id'] == $row['member_id']) {
				//if instructor
				echo _AT('instructor');
			} else if ($row['approved'] == 'a') {
				//if alumni
				echo _AT('alumni');
			} else if ($row['approved'] == 'n') {
				//if notenrolled
				echo _AT('pending_approval');
			} else if ($row['role'] != '') {
				//if custom role
				echo AT_print($row['role'], 'members.role');
			} else {
				//if no role and enrolled
				echo _AT('student1');
			} ?>
		</h6>
		<div>
			<a href="bounce.php?course=<?php echo $row['course_id']; ?>">
				<?php if ($row['icon'] == ''): ?>
					<img src="images/clr.gif" class="icon" border="0" width="79" height="79" alt="" />
				<?php else: ?>
						<img src="images/courses/<?php echo $row['icon']; ?>" class="icon" border="0" alt="" />
				<?php endif; ?>
			</a>

			<br /><strong><a href="bounce.php?course=<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></a></strong><br />

			<p>
				<?php echo _AT('instructor');?>: <?php echo get_login($row['member_id']); ?><br />
				<?php echo _AT('category'); ?>: <?php echo get_category_name($row['cat_id']); ?><br />
								
				<?php if ($row['tests']): ?>
					<?php echo _AT('tests'); ?>: 
					<?php foreach ($row['tests'] as $test): ?>
						<a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('tools/take_test.php?tid='.$test['test_id']); ?>"><?php echo $test['title']; ?></a> 
					<?php endforeach ;?>
				<?php endif; ?>
			</p>

			<?php if ($row['last_cid']): ?>
				<div class="shortcuts">
					<a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><?php echo _AT('resume'); ?></a> 
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>