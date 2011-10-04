<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF'];?>" name="create_album" method="post">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><div class="left_row"><label for="album_name"><?php echo _AT('pa_album_name'); ?></label></div>
		<input id="album_name" name="album_name" type="text" />
	</div>
	<?php
	//if the user has the privilege to create course albums, and she's in a course, then allow them to choose
	if ($_SESSION['course_id'] && ($course_album_privilege || true)): ?>
	<div class="row">
		<div class="left_row"<label for="album_type"><?php echo _AT('pa_album_type'); ?></label></div>
		<label for="my_album"><?php echo _AT('pa_my_albums'); ?></label><input type="radio" name="album_type" id="my_album" value="1" checked="checked" />
	<?php  if($_SESSION['is_admin']){?>
		<label for="course_album"><?php echo _AT('pa_course_albums'); ?></label><input type="radio" name="album_type" id="course_album" value="2" />
	<?php }?>
	</div>
	<?php endif; ?>
	<div class="row">
		<div class="left_row"<label for="album_permission"><?php echo _AT('pa_album_permission'); ?></label></div>
		<label for="album_permission_private"><?php echo _AT('pa_private'); ?></label><input type="radio" name="album_permission" id="album_permission_private" value="0" checked="checked" />
		<label for="album_permission_shared"><?php echo _AT('pa_shared'); ?></label><input type="radio" name="album_permission" id="album_permission_shared" value="1" />
	</div>
	<div class="row">
		<div class="left_row"<label for="album_location"><?php echo _AT('pa_album_location'); ?></label></div>
		<input id="album_location" name="album_location" type="text" />
	</div>
	<div class="row">
		<div class="left_row"<label for="album_description"><?php echo _AT('pa_album_description'); ?></label></div>
		<textarea id="album_description" name="album_description"></textarea>
	</div>
	<div class="row">
		<input name="submit" type="submit" value="<?php echo _AT('pa_create_album');?>" class="button"/>
		<input name="cancel" type="submit" value="<?php echo _AT('cancel');?>" class="button"/>
	</div>
</form>
</div>
