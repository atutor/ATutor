<div class="edit_photo_frame">
<?php if (!empty($this->photos)): ?>
<form action="" method="post" class="input-form">
	<?php foreach($this->photos as $k=>$photo): ?>
	<div class="edit_photo_box">
		<div class="info">
			<label for="description_<?php echo $photo['id']; ?>"><?php echo _AT('description');?></label><br/>
			<textarea name="description_<?php echo $photo['id']; ?>" id="description_<?php echo $photo['id']; ?>"><?php echo AT_print($photo['description'], 'input.text');?></textarea>

			<p><label for="alt_text_<?php echo $photo['id']; ?>"><?php echo _AT('pa_alt_text');?></label><br/>
			<textarea name="alt_text_<?php echo $photo['id']; ?>" id="alt_text_<?php echo $photo['id']; ?>" class="alt_text"><?php echo AT_print($photo['alt_text'], 'input.text');?></textarea></p>
		</div>
		<div class="action">
			<img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo AT_print($photo['description'], 'input.text'); ?>" alt="<?php echo AT_print($photo['alt_text'], 'input.text');?>" /><br/> 
			<input name="album_cover" id="photo_<?php echo $photo['id']; ?>" type="radio" value="<?php echo $photo['id']; ?>" <?php echo ($this->album_info['photo_id']==$photo['id'])?' checked="checked"':''; ?>/>
			<label for="photo_<?php echo $photo['id']; ?>"><?php echo _AT('pa_album_cover'); ?></label><br/>
			
			<input name="delete_<?php echo $photo['id']; ?>" id="delete_<?php echo $photo['id']; ?>" type="checkbox" value="<?php echo $photo['id']; ?>"/>
			<label for="delete_<?php echo $photo['id']; ?>"><?php echo _AT('delete');?></label>
		</div>
	</div>
	<?php endforeach; ?>
	<div class="row">
		<input type="hidden" name="aid" value="<?php echo $this->album_info['id']; ?>" />
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="button" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel');?>" class="button"/>
	</div>
</form>
<?php else: ?>
	<div class="edit_photo_box">
		<p><?php echo _AT('pa_no_photos'); ?></p>
	</div>
<?php endif; ?>
</div>