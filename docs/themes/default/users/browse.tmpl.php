<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<fieldset class="group_form"><legend class="group_form"><?php echo _AT('filter'); ?></legend>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<div class="input-form">
			<div class="row">
				<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
			</div>
			<div class="row">
				<?php echo _AT('access'); ?><br />
				<input type="radio" name="access" value="private" id="s1" <?php if ($_GET['access'] == 'private') { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('private'); ?></label> 

				<input type="radio" name="access" value="protected" id="s2" <?php if ($_GET['access'] == 'protected') { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('protected'); ?></label>

				<input type="radio" name="access" value="public" id="s3" <?php if ($_GET['access'] == 'public') { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('public'); ?></label>

				<input type="radio" name="access" value="" id="s" <?php if ($_GET['access'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
			</div>

		<?php if ($this->has_categories): ?>
			<div class="row">
				<label for="category"><?php echo _AT('category'); ?></label><br/>
				<select name="category" id="category">
					<option value="-1">- - - <?php echo _AT('cats_all'); ?> - - -</option>
					<option value="0" <?php if ($_GET['category'] == 0) { echo 'selected="selected"'; } ?>>- - - <?php echo _AT('cats_uncategorized'); ?> - - -</option>
					<?php echo $this->categories_select; ?>
				</select>
			</div>
		<?php endif; ?>

			<div class="row">
				<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />

				<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
				<br/>
				<?php echo _AT('search_match'); ?>:
				<input type="radio" name="include" value="all" id="match_all" <?php echo $this->checked_include_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> 
				<input type="radio" name="include" value="one" id="match_one" <?php echo $this->checked_include_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
			</div>

			<div class="row buttons">
				<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>"/>
				<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>"/>
			</div>
		</div>
	</form>
</fieldset>
	<ul style=" padding: 0px; margin: 0px">
	<?php while ($row = mysql_fetch_assoc($this->courses_result)): ?>
		<li style="list-style: none; width: 80%">
			<dl class="browse-course">
				<dt>
					<?php if ($row['icon']) { // if a course icon is available, display it here.  
						$style_for_title = 'style="height: 79px;"'; 

						//Check if this is a custom icon, if so, use get_course_icon.php to get it
						//Otherwise, simply link it from the images/
						$path = AT_CONTENT_DIR.$row['course_id']."/custom_icons/";
		                if (file_exists($path.$row['icon'])) {
							if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
								$course_icon = 'get_course_icon.php/?id='.$row['course_id'];
							} else {
								$course_icon = 'content/' . $row['course_id'] . '/';
							}
						} else {
							$course_icon = 'images/courses/'.$row['icon'];
						}
					?>
						<a href="<?php echo url_rewrite('bounce.php?course='.$row['course_id'], true); ?>"><img src="<?php echo $course_icon; ?>" class="headicon" alt="<?php echo  $row['title']; ?>" /> </a>	
					<?php } ?>
				</dt>
				<dd><h3 <?php echo $style_for_title; ?>><a href="<?php echo url_rewrite('bounce.php?course='.$row['course_id'], true); ?>"><?php echo $row['title']; ?></a></h3></dd>
				
			<?php if ($row['description']): ?>
				<dt><?php echo _AT('description'); ?></dt>
				<dd><?php echo nl2br($row['description']); ?>&nbsp;</dd>
			<?php endif; ?>

			<?php if ($has_categories): ?>
				<dt><?php echo _AT('category'); ?></dt>
				<dd><a href="<?php echo $_SERVER['PHP_SELF'].'?'.$page_string.SEP; ?>category=<?php echo $row['cat_id']; ?>"><?php echo $cats[$row['cat_id']]; ?></a>&nbsp;</dd>
			<?php endif; ?>
				
				<dt><?php echo _AT('instructor'); ?></dt>
				<dd><a href="<?php echo AT_BASE_HREF; ?>contact_instructor.php?id=<?php echo $row['course_id']; ?>"><?php echo get_display_name($row['member_id']); ?></a></dd>

				<dt><?php echo _AT('access'); ?></dt>
				<dd><?php echo _AT($row['access']); ?></dd>
			</dl>
		</li>
	<?php endwhile; ?>
	</ul>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>