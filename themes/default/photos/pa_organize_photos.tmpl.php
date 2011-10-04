<div>
	<div class="album_panel">
		<?php if(!empty($this->photos)): ?>
		<p><?php echo _AT('pa_organize_photo_blurb'); ?></p>
		<form action="<?php echo AT_PA_BASENAME . 'edit_photos.php?aid='.$this->album_info['id'].SEP.'org=1'; ?>" id="reorder-images-form" class="fl-reorderer-horizontalLayout" style="float:left;">
		<!-- loop through this -->
		<?php foreach($this->photos as $key=>$photo):?>
		<div class="photo_wrapper">
			<a class="photo_frame">
				<img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo AT_print($photo['description'], 'input.text'); ?>" alt="<?php echo AT_print($photo['alt_text'], 'input.text'); ?>>" />
				<input name="image_<?php echo $photo['id']; ?>" value="<?php echo $photo['ordering']; ?>" type="hidden" />
			</a>
		</div>
		<?php endforeach; ?>
		<!-- end loop -->
		<input type="hidden" name="submit" value="<?php echo _AT('save_changes'); ?>" class="button"/>
		</form>
		<?php else: ?>
		<div class="edit_photo_box">
			<p><?php echo _AT('pa_no_photos'); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
	atutor.formBasedImageReorderer();	
</script>
