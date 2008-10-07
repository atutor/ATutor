<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php if (!$this->courses && get_instructor_status()): ?>
	<?php global $msg; $msg->printInfos('NO_COURSES_INST'); ?>
<?php elseif (!$this->courses): ?>
	<?php global $msg; $msg->printInfos('NO_COURSES'); ?>
<?php endif; ?>

<?php foreach ($this->courses as $row):?>	
	<div class="course">
		<div style="font-size:smaller;" align="right"><?php
			$link  = '<a href="'.url_rewrite('bounce.php?course=' . $row['course_id']) . '">';
			$link2 = '</a>';

			if ($_SESSION['member_id'] == $row['member_id']) {
				//if instructor
				echo _AT('instructor');
			} else if ($row['approved'] == 'a') {
				//if alumni
				echo _AT('alumni');
			} else if ($row['approved'] == 'n') {
				//if notenrolled
				echo _AT('pending_approval');
				$link  = $link2 = "";
			} else {
				//if no role and enrolled
				echo _AT('student1');
			} ?>
		</div>
			<div class="body">
				<?php if ($row['icon'] == ''): ?>
						<img src="images/clr.gif" class="icon" border="0" width="79" height="79" alt="" />
				<?php else: 
						echo $link;  

              	$sql2="SELECT icon from ".TABLE_PREFIX."courses WHERE course_id='$row[course_id]'";
				$result2 = mysql_query($sql2, $db);
				
				while($row2=mysql_fetch_assoc($result2)){
					$filename = $row2['icon'];
				}
		
                $path = AT_CONTENT_DIR .$row['course_id'].'/custom_icons/'.$filename;
                
                if (file_exists($path)) {
                    if (defined('AT_FORCE_GET_FILE')) {
                        $dir = 'get_course_icon.php?id='.$row['course_id'];
                    } else {
                        $dir = 'content/' . $_SESSION['course_id'] . '/'.$row['icon'];
                    }
                } else {
                    	$dir = "images/courses/".$row['icon'];
                }
                ?>
				<img src="<?php echo $dir; ?>" class="icon" border="0" alt="<?php echo $row['title']; ?>" />
					<?php echo $link2; ?>
				<?php endif; ?>

				<strong><?php echo $link.$row['title'].$link2; ?></strong>

				<?php if ($row['member_id'] != $_SESSION['member_id']  && $_config['allow_unenroll'] == 1): ?>
					- <a href="users/remove_course.php?course=<?php echo $row['course_id']; ?>"><?php echo _AT('unenroll_me'); ?></a>
				<?php endif; ?>

				<br />
 
				<p>
					<small><?php echo _AT('instructor');?>: <?php echo get_display_name($row['member_id']); ?>
					<?php echo ' - <a href="'. AT_BASE_HREF.'inbox/send_message.php?id='.$row['member_id'].'">'._AT('send_message').'</a>'; ?>
					<br />
					<?php echo _AT('category'); ?>: <?php echo get_category_name($row['cat_id']); ?><br />
					
					
					<?php if ($row['tests']): ?>
						<?php echo _AT('tests'); ?>: 
						<?php foreach ($row['tests'] as $test): ?>
							<a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('tools/test_intro.php?tid='.$test['test_id']); ?>"><?php echo $test['title']; ?></a> 
						<?php endforeach ;?>
					<?php endif; ?>
				</small>
				</p>

				<?php if ($row['last_cid']): ?>
					<div class="shortcuts">
						<small><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><?php echo _AT('resume'); ?></a></small>
					</div>
				<?php endif; ?>
			</div>
	</div>
<?php endforeach; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>