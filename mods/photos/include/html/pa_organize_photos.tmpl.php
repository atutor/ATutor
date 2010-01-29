<div>
	<!-- Photo album options and page numbers -->
	<div class="topbar">
		<div class="summary"><?php echo _AT('organize_photos'); ?><br/></div>
	</div>

	<div class="album_panel">
		<?php if(!empty($this->photos)): ?>
		<p><?php echo _AT('organize_photo_blurb'); ?></p>
		<form action="<?php echo 'edit_photos.php?aid='.$this->album_info['id'].SEP.'org=1'; ?>" id="reorder-images-form" class="flc-imageReorderer fl-imageReorderer fl-reorderer-horizontalLayout" style="float:left;">
		<!-- loop through this -->
		<?php foreach($this->photos as $key=>$photo):?>
		<div class="photo_wrapper">
			<a class="flc-imageReorderer-item fl-imageReorderer-item photo_frame">
				<img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf8($photo['description'], false); ?>" alt="<?php echo htmlentities_utf8($photo['alt_text'], false); ?>>" />
				<input name="image_<?php echo $photo['id']; ?>" value="<?php echo $photo['ordering']; ?>" type="hidden" />
			</a>
		</div>
		<?php endforeach; ?>
		<!-- end loop -->
		<input type="hidden" name="submit" value="<?php echo _AT('save_changes'); ?>" class="button"/>
		</form>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
	demo.formBasedImageReorderer();	
</script>
