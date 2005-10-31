<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<div id="browse">
	<div style="float: left; white-space:nowrap; padding-right:30px;">
			<h3><?php echo _AT('cats_categories'); ?></h3>

			<ul class="browse-list">
				<?php 
				foreach ($this->cats as $cat_id => $cat_name): 
					echo '<li>';
					if ($cat_id == $this->cat): ?>
						<div class="browse-selected">
					<?php else: ?>
						<div class="browse-unselected">
					<?php endif; ?>
							<a href="<?php echo $_SERVER['PHP_SELF'].'?cat='.$cat_id; ?>#courses"><?php echo $cat_name ?></a>    
						</div>
					</li>
				<?php endforeach; ?>		
			</ul>			<br />

	</div>
	<a name="courses"></a>
	<div style="float: left; white-space:nowrap; padding-right:30px;">
			<h3><?php echo _AT('courses').': '.$this->cats[$this->cat]; ?></h3>

			<?php if (isset($this->courses)):
				$cur_sub_cat = ''; ?>

				<ul class="browse-list">
					<li>
					<?php if ($this->show_course == 0): ?>
						<div class="browse-selected">
					<?php else: ?>
						<div class="browse-unselected">
					<?php endif; ?>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?cat=0<?php echo SEP;?>show_course=0#info"><?php echo _AT('all_courses'); ?></a>
					</div>			
					</li>
					
					<?php foreach ($this->courses as $course_id=>$info):
						if (isset($this->sub_cats) && array_key_exists($info['cat_id'], $this->sub_cats) && ($cur_sub_cat != $this->sub_cats[$info['cat_id']])):
							$cur_sub_cat = $this->sub_cats[$info['cat_id']];?>
							</ul><br /><h4><?php echo $cur_sub_cat; ?></h4><ul class="browse-list">
						<?php endif; ?>
						<li>
						<?php if ($info['selected']): ?>
							<div class="browse-selected">
						<?php else: ?>
							<div class="browse-unselected">
						<?php endif; ?>
							<a href="<?php echo $info['url']; ?>"><?php echo $info['title']; ?></a>
						</div>
						</li>

					<?php endforeach; ?>
				</ul>
			<?php else:
				echo _AT('no_courses');
			endif;?>
			<br />
	</div>

	<div style="float: left; width: 40%;">
		<h3><?php echo _AT('info');?>: 
		<?php
			if ($this->show_course == 0) {
				echo _AT('all_courses'); 
			} else {
				echo $this->course_row[0]['title']; 
			}
		?> </h3>
			

	<?php foreach ($this->course_row as $this->course_row): ?>
		<a name="info"></a>
		<div style="border:solid thin #999;">
				<h4 style="clear: none;	display:inline;"><?php echo $this->course_row['title']; ?></h4>&nbsp;- <a href="bounce.php?course=<?php echo $this->course_row['course_id']; ?>"><?php echo _AT('enter_course'); ?></a>
				<p><?php echo $this->course_row['description']; ?></p>
				<p>
				<?php 
					echo _AT('instructor').': '. $this->course_row['login'];
					echo ' - <a href="'. $_base_href.'contact_instructor.php?id='.$this->course_row['course_id'].'">'._AT('contact_instructor').'</a><br />'; 
				?>
				<?php echo _AT('access').': '._AT($this->course_row['access']); ?></p>

				<br />
		</div><br />
	<?php endforeach; ?>
	</div>
</div>
<br />

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>