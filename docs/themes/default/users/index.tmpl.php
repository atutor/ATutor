<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php foreach ($this->courses as $row):?>	
	<div class="course">
		<h5 align="right"><?php
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
		</h5>
			<div class="body">
				<a href="bounce.php?course=<?php echo $row['course_id']; ?>">
					<?php if ($row['icon'] == ''): ?>
						<img src="images/clr.gif" class="icon" border="0" width="79" height="79" />
					<?php else: ?>
							<img src="images/courses/<?php echo $row['icon']; ?>" class="icon" border="0" />
					<?php endif; ?>
				</a>

				<strong><a href="bounce.php?course=<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></a></strong>
				<?php if ($row['member_id'] != $_SESSION['member_id']): ?>
					- <a href="users/remove_course.php?course=<?php echo $row['course_id']; ?>"><?php echo _AT('unenroll'); ?></a>
				<?php endif; ?>
				<br />
				<p><small><?php echo _AT('instructor');?>: <?php echo get_login($row['member_id']); ?><br />
				<?php echo _AT('category'); ?>: <?php echo get_category_name($row['cat_id']); ?><br />
				</small>
				</p>

				<div class="shortcuts">
					<?php if ($row['new_messages'] == 0): ?>
						<small><?php echo $row['new_messages'] . ' ' . _AT('new_messages'); ?></small>
					<?php elseif ($row['new_messages'] == 1): ?>
						<small><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('inbox/index.php?'); ?>"><?php echo $row['new_messages'] . ' ' . _AT('new_message'); ?></a></small>
					<?php else: ?>
						<small><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('inbox/index.php'); ?>"><?php echo $row['new_messages'] . ' ' . _AT('new_messages'); ?></a></small>
					<?php endif; ?>
				</div>

				<div class="shortcuts">
					<small><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><?php echo _AT('resume'); ?></a></small>
				</div>
			</div>
	</div>
<?php endforeach; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>