<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php foreach ($this->courses as $row):?>	
	<div class="course">
		<h5 align="right">
			<?php if ($row['role'] != '') : 
				echo $row['role']; 
			elseif ($_SESSION['member_id'] == $row['member_id']) : 
				echo _AT('instructor');
			else:
				echo _AT('student');
			endif;?>
		</h5>
			<div class="body">
				<a href="bounce.php?course=<?php echo $row['course_id']; ?>">
					<?php if ($row['icon'] == ''): ?>
						<img src="images/clr.gif" class="icon" border="0" width="79" height="79" />
					<?php else: ?>
							<img src="images/courses/<?php echo $row['icon']; ?>" class="icon" border="0" />
					<?php endif; ?>
				</a>

				<strong><a href="bounce.php?course=<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></a></strong><br />
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