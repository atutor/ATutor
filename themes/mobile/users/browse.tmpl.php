<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<div class="column-login">
<div class="input-form">
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

				<input type="text" name="search" id="search" size="30" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
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
</div>
<div>
<?php if (is_array($this->courses_rows)){ ?>
	<?php foreach ($this->courses_rows as $row){ ?>
	    <?php  $counter++; ?>
		<ul class="fl-list-menu fl-list-thumbnails">
		 <li>
				<h3 class="browse-courses"><a href="<?php echo url_rewrite('bounce.php?course='.$row['course_id'], true); ?>"><?php echo htmlentities($row['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>      
		     
		      <?php if ($row['description']): ?>
				<span class="fl-link-summary" title="<?php echo htmlentities($row['description']);?>"><?php echo substr(nl2br(htmlentities($row['description'], ENT_QUOTES, 'UTF-8')),0,150); 
				if(strlen($row['description']) > 150){
				echo "...";
				}
				?>&nbsp;</span>
			<?php else: ?>
				<span class="fl-link-summary" title="<?php echo htmlentities($row['description']);?>">&nbsp;</span>
			<?php endif; ?>
		
		</li>
	
	</ul>	      
	<?php } // end foreach ?>
<?php } // end if ?>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>