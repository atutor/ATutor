<div class="input-form">
	<form action="" method="post">
		<div class="row">
			<label for="jb_title"><?php echo _AT('jb_title'); ?></label>
			<input type="text" id="jb_title" name="jb_title" value="<?php echo htmlentities_utf8($this->job_post['title']); ?>" />
		</div>		
		<div class="row">
<?php debug($this->job_post); ?>
			<label><?php echo _AT('jb_category'); ?></label><br/>
			<?php if(!empty($this->categories)): ?>
			<?php foreach($this->categories as $category): ?>
			<label for="jb_category_<?php echo $category['id'];?>"><?php echo htmlentities_utf8($category['name']); ?></label>
			<input type="checkbox" id="jb_category_<?php echo $category['id'];?>" name="jb_categories[]" value="<?php echo $category['id']; ?>" <?php echo ($this->job_post['categories'] && in_array($category['id'], $this->job_post['categories']))?'checked="checked"':''; ?> /> | 
			<?php endforeach; endif; ?>
		</div>
		<div class="row">
			<label for="jb_is_public"><?php echo _AT('jb_is_public'); ?></label>
			<input type="checkbox" id="jb_is_public" name="jb_is_public" <?php echo ($this->job_post['is_public']==1)?'checked="checked"':''; ?>/>
		</div>
		<div class="row">
			<label for="jb_closing_date"><?php echo _AT('jb_closing_date'); ?></label>
			<?php
			//load mysql timestamp template into the template.
			if (intval($this->job_post['closing_date'])) {
				$today_day   = substr($this->job_post['closing_date'], 8, 2);
				$today_mon   = substr($this->job_post['closing_date'], 5, 2);
				$today_year  = substr($this->job_post['closing_date'], 0, 4);

				$today_hour  = substr($this->job_post['closing_date'], 11, 2);
				$today_min   = substr($this->job_post['closing_date'], 14, 2);
			} else {
				$today_year  = date('Y');
			}

			//load the release_date template.
			$name = '_jb_closing_date';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
			?>
		</div>
		<div class="row">
			<label for="jb_description"><?php echo _AT('jb_description'); ?></label>
			<textarea id="jb_description" name="jb_description" ><?php echo htmlentities_utf8($this->job_post['description'], false); ?></textarea>
		</div>
		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>"/>
		</div>
	</form>
</div>