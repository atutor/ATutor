<div class="input-form">
	<form action="" method="post">
		<div class="row">
			<label for="jb_title"><?php echo _AT('jb_title'); ?></label>
			<input type="text" id="jb_title" name="jb_title" />
		</div>		
		<div class="row">
			<label><?php echo _AT('jb_category'); ?></label><br/>
			<?php if(!empty($this->categories)): ?>
			<?php foreach($this->categories as $category): ?>
			<label for="jb_category_<?php echo $category['id'];?>"><?php echo htmlentities_utf8($category['name']); ?></label>
			<input type="checkbox" id="jb_category_<?php echo $category['id'];?>" name="jb_categories[]" value="<?php echo $category['id']; ?>" /> | 
			<?php endforeach; endif; ?>
		</div>
		<div class="row">
			<label for="jb_is_public"><?php echo _AT('jb_is_public'); ?></label>
			<input type="checkbox" id="jb_is_public" name="jb_is_public" ></textarea>
		</div>
		<div class="row">
			<!-- todo: use the date picker -->
			<label for="jb_closing_date"><?php echo _AT('jb_closing_date'); ?></label>
			<input type="text" id="jb_closing_date" name="jb_closing_date" ></textarea>
		</div>
		<div class="row">
			<label for="jb_description"><?php echo _AT('jb_description'); ?></label>
			<textarea id="jb_description" name="jb_description" ></textarea>
		</div>
		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>"/>
		</div>
	</form>
</div>
