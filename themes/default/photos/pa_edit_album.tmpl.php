<div class="input-form">
<form action="" name="create_album" method="post">
	<div class="row">
		<div class="left_row"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="album_name"><?php echo _AT('pa_album_name'); ?></label></div>
		<input id="album_name" name="album_name" type="text" value="<?php echo AT_print($this->album_info['name'], 'input.text');?>" />
	</div>
	<?php
	//if the user has the privilege to create course albums, then allow them to choose
	if ($course_album_privilege || true): ?>
	<div class="row">
		<div class="left_row"<label for="album_type"><?php echo _AT('pa_album_type'); ?></label></div>
		<label for="my_album"><?php echo _AT('pa_my_albums'); ?><label><input type="radio" name="album_type" id="my_album" value="1" <?php echo ($this->album_info['type_id']==AT_PA_TYPE_MY_ALBUM)?' checked="checked"':'';?>/>
		<label for="course_album"><?php echo _AT('pa_course_albums'); ?></label><input type="radio" name="album_type" id="course_album" value="2" <?php echo ($this->album_info['type_id']==AT_PA_TYPE_COURSE_ALBUM)?' checked="checked"':'';?>/>
	</div>
	<?php endif; ?>
	<div class="row">
		<div class="left_row"<label for="album_permission"><?php echo _AT('pa_album_permission'); ?></label></div>
		<label for="album_permission_private"><?php echo _AT('pa_private'); ?><label><input type="radio" name="album_permission" id="album_permission_private" value="0" <?php echo ($this->album_info['permission']==AT_PA_PRIVATE_ALBUM)?' checked="checked"':'';?>/>
		<label for="album_permission_shared"><?php echo _AT('pa_shared'); ?></label><input type="radio" name="album_permission" id="album_permission_shared" value="1" <?php echo ($this->album_info['permission']==AT_PA_SHARED_ALBUM)?' checked="checked"':'';?>/>
	</div>
	<div class="row">
		<div class="left_row"<label for="album_location"><?php echo _AT('pa_album_location'); ?></label></div>
		<input id="album_location" name="album_location" type="text" value="<?php echo AT_print($this->album_info['location'], 'input.text');?>"/>
	</div>
	<div class="row">
		<div class="left_row"<label for="album_description"><?php echo _AT('pa_album_description'); ?></label></div>
		<textarea id="album_description" name="album_description"><?php echo AT_print($this->album_info['description'], 'input.text');?></textarea>
	</div>
	<div class="row">
		<input type="hidden" name="aid" value="<?php echo $this->album_info['id']; ?>" />
		<input name="submit" type="submit" value="<?php echo _AT('save');?>" class="button"/>
		<input name="cancel" type="submit" value="<?php echo _AT('cancel');?>" class="button"/>
	</div>
</form>
</div>