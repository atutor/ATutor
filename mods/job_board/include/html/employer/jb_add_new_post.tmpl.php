<?php
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	load_editor();
}
?>
<div class="input-form">
	<form name="form" action="" method="post">
		<div class="row">
			<label for="jb_title"><?php echo _AT('jb_title'); ?></label>
			<input type="text" id="jb_title" name="title" />
		</div>		
		<div class="row">
			<label><?php echo _AT('categories'); ?></label><br/>
			<?php if(!empty($this->categories)): ?>
			<?php foreach($this->categories as $category): ?>
			<div class="category_box">
				<input type="checkbox" id="jb_category_<?php echo $category['id'];?>" name="jb_categories[]" value="<?php echo $category['id']; ?>" />
				<label for="jb_category_<?php echo $category['id'];?>"><?php echo htmlentities_utf8($category['name']); ?></label>				
			</div>
			<?php endforeach; endif; ?>
			<div style="clear:both;"></div>
		</div>
		<div class="row">
			<label for="jb_is_public"><?php echo _AT('jb_is_public'); ?></label>
			<input type="checkbox" id="jb_is_public" name="jb_is_public" ></textarea>
		</div>
		<div class="row">
			<label for="jb_closing_date"><?php echo _AT('jb_closing_date'); ?></label>
			<?php
			//load mysql timestamp template into the template.
			$today_day   = date('d');
			$today_mon   = date('m');
			$today_year  = date('Y');

			$today_hour  = date('H');
			$today_min   = date('i');

			//load the release_date template.
			$name = '_jb_closing_date';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
			?>
		</div>
		<div class="row">
			<?php echo _AT('formatting'); ?><br />
			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />

			<label for="text"><?php echo _AT('plain_text'); ?></label>
			<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>

			<label for="html"><?php echo _AT('html'); ?></label>
			<?php   //Button for enabling/disabling visual editor
				if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
					echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
					echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button"/>';
				} else {
					echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'"  ';
					if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
					echo ' class="button" />';
				}
			?>
		</div>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="jb_description"><?php echo _AT('jb_post_description'); ?></label><br />
			<small>&middot; <?php echo _AT('jb_post_description_note'); ?></small><br />
			<textarea name="jb_description" cols="55" rows="15" id="jb_description"><?php echo $_POST['jb_description']; ?><?php echo $this->job_post['description']; ?></textarea>
		</div>
		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>"/>
		</div>
	</form>
</div>
