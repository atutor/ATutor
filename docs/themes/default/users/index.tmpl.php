<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php foreach ($this->courses as $row):?>	
	<div class="course">
		<h5 align="right"><?php
			//if instructor
			if ($_SESSION['member_id'] == $row['member_id']) {
				echo '<img src="'.$this->img.'user-star.gif" style="vertical-align: bottom;" alt="" />'._AT('instructor');
			}
			//if alumni
			else if ($row['approved'] == 'a') {
				echo _AT('alumni');
			}
			//if notenrolled
			else if ($row['approved'] == 'n') {
				echo _AT('not_enrolled');
			}
			//if custom role
			else if ($row['role'] != '') {
				echo '<img src="'.$this->img.'user-student.gif" style="vertical-align: bottom;" alt="" />'.AT_print($row['role'], 'members.role');

			}
			//if no role and enrolled
			else {
				echo '<img src="'.$this->img.'user-student.gif" style="vertical-align: bottom;" alt="" />'._AT('student1');
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
					<small><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><?php echo _AT('resume'); ?></a></small>
				</div>
			</div>
	</div>
<?php endforeach; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>