<div>
	<!-- Photo album options and page numbers -->
	<div class="topbar">
		<div class="summary">
				<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id']; ?>"><?php echo _AT('edit_photos');?></a> | 
				<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id'].SEP.'org=1'; ?>"><?php echo _AT('organize_photos');?></a> | 
				<a>Add More Photos</a> |
		</div>
		<div class="paginator">
			<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTO_PERS_PAGE, AT_PA_PAGE_WINDOW);  ?>
		</div>
	</div>

	<div class="add_photo">
		<div>
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data" name="add_photos" class="input-form" method="post">
				<div class="row">
					<p><?php echo _AT('add_more_photos');?></p>
				</div>
				<div class="row">
					<label for="photo_comment"><?php echo _AT('comment'); ?></label><br/>
					<textarea name="photo_comment" id="photo_comment"></textarea>
				</div>
				<div class="row">
					<input type="file" name="photo" />
					<input type="hidden" name="id" value="<?php echo $this->album_info['id'];?>" />
					<input type="submit" name="upload" value="<?php echo _AT("upload");?>"class="button"/>
				</div>
			</form>
		</div>
	</div>

	<div class="album_panel">
		<!-- loop through this -->
		<?php foreach($this->photos as $key=>$photo): ?>
		<div class="photo_frame">
			<a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$photo['id'].SEP.'aid='.$this->album_info['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf8($photo['description'], false); ?>" alt="<?php echo htmlentities_utf8($photo['alt_text']);?>" /></a>
		</div>
		<?php endforeach; ?>
		<!-- end loop -->
	</div>

	<!-- page numbers -->
	<div class="topbar">
		<div class="paginator">
			<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTO_PERS_PAGE, AT_PA_PAGE_WINDOW);  ?>
		</div>
	</div>

	<!-- comments -->
	<div class="comment_panel">
		<div class="comment_feeds">
			<?php if (!empty($this->comments)): ?>
			<?php foreach($this->comments as $k=>$comment_array): ?>
				<div class="comment_box">
					<!-- TODO: Profile link and img -->
					<div><a href=""><strong><?php echo htmlentities_utf8(AT_print(get_display_name($comment_array['member_id']), 'members.full_name')); ?></a></strong> <?php echo htmlentities_utf8($comment_array['comment']); ?></div>
					<div>
						<div class="comment_text"></div>
						<div class="comment_actions">
							<!-- TODO: if author, add in-line "edit" -->
							<?php echo AT_date(_AT('forum_date_format'), $comment_array['created_date'], AT_DATE_MYSQL_DATETIME);?>
							<?php if ($this->action_permission): ?>
							<a href=""><?php echo _AT('edit');?></a>							
							<a href="<?php echo AT_PA_BASENAME.'delete_comment.php?aid='.$this->album_info['id'].SEP.'comment_id='.$comment_array['id']?>"><?php echo _AT('delete');?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; endif;?>
			<!-- TODO: Add script to check, comment cannot be empty. -->
			<div>
				<form action="<?php echo AT_PA_BASENAME;?>addComment.php" method="post" class="input-form">
					<div class="row"><label for="comments"><?php echo _AT('comments');?></label></div>
					<div class="row"><textarea name="comment" id="comment">Write a comment...</textarea></div>		
					<div class="row">
						<input type="hidden" name="aid" value="<?php echo $this->album_info['id'];?>" />
						<input type="submit" name="submit" value="<?php echo _AT('comment');?>" class="button"/>
					</div>
				</form>
			</div>
		</div>		
	</div>
</div>