<div>
	<!-- Photo album options and page numbers -->
	<div class="album_panel">	
		<div class="topbar">
			<div class="search_bar">
				<form action="<?php echo AT_PA_BASENAME.'search.php'; ?>" id="pa_search_form" name="pa_search_form" method="post">
					<input type="text" class="s" name="pa_search" id="pa_search" title="<?php echo _AT('search');?>" />
					<input type="image" class="s_img" src="<?php echo AT_PA_BASENAME; ?>images/search_icon.png" alt="<?php echo _AT('search');?>" />
				</form>
			</div>
			<?php if($this->num_rows > AT_PA_ALBUMS_PER_PAGE): ?>
			<!-- page numbers -->
			<div class="paginator">
				<?php print_paginator($this->page, $this->num_rows, 'type='.$this->type, AT_PA_ALBUMS_PER_PAGE, AT_PA_PAGE_WINDOW); ?>
			</div>
			<?php endif; ?>
		</div>
	
		<!-- loop through this -->
		<?php if(!empty($this->albums)): ?>
		<?php foreach($this->albums as $index=>$row): 
			$pa = new PhotoAlbum($index);
		?>
		<div class="album">
			<!-- TODO: If photo is not presense, print another image? -->
			<div class="image">
			<?php 
			$photo_info = $pa->getPhotoInfo($row['photo_id']); 
			if (!empty($photo_info)):
			?>
			<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$row['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$row['id'].SEP.'pid='.$row['photo_id'].SEP.'ph='.getPhotoFilePath($photo_info['id'], '', $photo_info['created_date']);?>" title="<?php echo htmlentities_utf82($photo_info['description']); ?>" alt="<?php echo htmlentities_utf82($row['name']); ?>" /></a>
			<?php else: ?>
			<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$row['id'];?>"><img class="no-image" title="<?php echo _AT('pa_no_image'); ?>" alt="<?php echo _AT('pa_no_image'); ?>" /></a>
			<?php endif; //image ?>
			</div>
			<div class="info">
				<h4><a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$row['id'];?>"><?php echo htmlentities_utf82($row['name']); ?></a></h4>
				<p><?php echo htmlentities_utf82($row['description']); ?></p>
				<p><?php echo _AT('location').': '.htmlentities_utf82($row['location']); ?></p>
				<span>
				<!-- If this is shared album, display the author -->
				<?php if (isset($this->isSharedAlbum)): ?>
				<p><?php echo _AT('created_by').': '.AT_print(get_display_name($row['member_id']), 'members.full_name'); ?></p>
				<?php endif; ?>
				<p><?php echo _AT('last_updated', AT_date(_AT('forum_date_format'), $row['last_updated'], AT_DATE_MYSQL_DATETIME));?></p>
				<p><?php echo _AT('created').': '.AT_date(_AT('forum_date_format'), $row['created_date'], AT_DATE_MYSQL_DATETIME); ?></p>
				</span><br/>
				<?php 
					/* If the span has 3 rows, we need 2 <br> for the next span to sink to the bottom.  
					 * So if we have an extra "created_by" row, we need an extra <br> tag
					 */
					if (isset($this->isSharedAlbum)){
						echo '<br/>';
					}
				?>
				<?php if($pa->checkAlbumPriv($_SESSION['member_id'])): ?>
				<span><p><a href="<?php echo AT_PA_BASENAME;?>edit_album.php?id=<?php echo $row['id'];?>"><?php echo _AT('edit'); ?></a> | <a href="<?php echo AT_PA_BASENAME;?>delete_album.php?id=<?php echo $row['id'];?>"><?php echo _AT('delete');?></a></p></span>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
		<?php else: ?>
		<div class="album">
			<p><?php echo _AT('pa_no_album'); ?></p>
		</div>
		<?php endif; ?>
		<!-- end loop -->
		<!-- page numbers -->
		<?php if($this->num_rows > AT_PA_ALBUMS_PER_PAGE): ?>
		<div class="topbar">
			<div class="paginator">
				<?php print_paginator($this->page, $this->num_rows, 'type='.$this->type, AT_PA_ALBUMS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
			</div>
		</div>
		<?php endif; ?>
	</div>	
</div>