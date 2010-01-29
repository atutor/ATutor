<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF'];?>" name="create_album" method="post">
	<div class="row">
		<div class="left_row"><label for="album_name"><?php echo _AT('album_name'); ?></label></div>
		<input id="album_name" name="album_name" type="text" />
	</div>
	<?php
	//if the user has the privilege to create course albums, then allow them to choose
	if ($course_album_privilege || true): ?>
	<div class="row">
		<div class="left_row"<label for="album_type"><?php echo _AT('album_type'); ?></label></div>
		<label for="my_album"><?php echo _AT('my_albums'); ?><label><input type="radio" name="album_type" id="my_album" value="1" />
		<label for="course_album"><?php echo _AT('course_albums'); ?></label><input type="radio" name="album_type" id="course_album" value="2" />

	</div>
	<?php endif; ?>
	<div class="row">
		<div class="left_row"<label for="album_location"><?php echo _AT('album_location'); ?></label></div>
		<input id="album_location" name="album_location" type="text" />
	</div>
	<div class="row">
		<div class="left_row"<label for="album_description"><?php echo _AT('album_description'); ?></label></div>
		<textarea id="album_description" name="album_description"></textarea>
	</div>
	<!--
	<div class="row">
		<div class="left_row"><label for="album_photos"><?php echo _AT('album_photos'); ?></label></div>
		<input id="album_photos" name="album_photos" type="file" />
	</div>
	-->
	<div class="row">
		<input name="submit" type="submit" value="<?php echo _AT('create_album');?>" class="button"/>
		<input name="cancel" type="submit" value="<?php echo _AT('cancel');?>" class="button"/>
	</div>
</form>
</div>