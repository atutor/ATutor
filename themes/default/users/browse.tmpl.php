<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<div class="input-form" style="width:90%;">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('filter'); ?></legend>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		
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
		
	</form>
</fieldset>
</div>
<div class="container" style="width:95%; margin:auto;">
<table class="data">
<tr>
<th>&nbsp;</th>
<th><?php echo _AT('title'); ?></th>
<th><?php echo _AT('description'); ?></th>
<th><?php echo _AT('category'); ?></th>
<th><?php echo _AT('instructor'); ?></th>
<th><?php echo _AT('access'); ?></th>
<th><?php echo _AT('shortcuts'); ?></th>
</tr>
<?php if (is_array($this->courses_rows)){ ?>
	<?php foreach ($this->courses_rows as $row){ ?>
	    <?php  $counter++; ?>
		 <tr class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
		 <td>
		
		      <?php if ($row['icon']) { // if a course icon is available, display it here.  
			      $style_for_title = 'style="height: 1.5em;"'; 

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
			      <a href="<?php echo url_rewrite('bounce.php?course='.$row['course_id'], true); ?>"><img src="<?php echo $course_icon; ?>" class="headicon" alt="<?php echo  htmlentities($row['title'], ENT_QUOTES, 'UTF-8'); ?>" style="float:left;margin-right:.5em;"/></a>
		      <?php } ?>
		</td>
		<td>
				<h3><a href="<?php echo url_rewrite('bounce.php?course='.$row['course_id'], true); ?>"><?php echo htmlentities($row['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
		</td>
		<td>
			<?php if ($row['description']): ?>
				<div style="height:6.4em;" title="<?php echo htmlentities($row['description']);?>"><?php echo substr(nl2br(htmlentities($row['description'], ENT_QUOTES, 'UTF-8')),0,150); 
				if(strlen($row['description']) > 150){
				echo "...";
				}
				?>&nbsp;</div>
			<?php else: ?>
				<div style="height:6.4em;clear:right;" title="<?php echo htmlentities($row['description']);?>">&nbsp;</div>
			<?php endif; ?>
		</td>
		<td>
			<?php if (is_array($this->cats) && $row['cat_id'] != 0): ?>
				<a href="<?php echo $_SERVER['PHP_SELF'].'?'.$page_string.SEP; ?>category=<?php echo $row['cat_id']; ?>"><?php echo $this->cats[$row['cat_id']]; ?></a>
			<?php endif; ?>
		</td>
		<td>
				<a href="<?php echo AT_BASE_HREF; ?>contact_instructor.php?id=<?php echo $row['course_id']; ?>"><?php echo get_display_name($row['member_id']); ?></a>
		</td>
		<td>
			<?php echo _AT($row['access']); ?>
		</td>
		<td>
		 <?php
		    // insert enrolment link if allowed
		    if (isset($row['enroll_link'])) : ?> 
			- <small><?php echo $row['enroll_link']; ?></small>
		<?php endif; ?>
		</td>
		</tr>
	      
	<?php } // end foreach ?>
<?php } // end if ?>
</table>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>