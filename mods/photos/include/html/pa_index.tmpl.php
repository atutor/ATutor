<div>
	<!-- Photo album options and page numbers -->
	<div class="topbar">
		<div class="summary">
				<a><?php echo _AT('profile_gallery'); ?></a> | 
				<a><?php echo _AT('my_albums'); ?></a> | 
				<a><?php echo _AT('course_albums'); ?></a> |
				<a href="<?php echo AT_PA_BASENAME; ?>create_album.php"><?php echo _AT('create_album');?></a>
		</div>
		<div class="paginator">
			<ul>
				<li><a>1</a></li>
				<li><a>2</a></li>
				<li><a>3</a></li>
			</ul>
		</div>
	</div>

	<div class="album_panel">
		<!-- loop through this -->
		<?php if(!empty($this->albums)): 
			$pa = new PhotoAlbum();
		?>
		<?php foreach($this->albums as $index=>$row): ?>
		<div class="album">
			<!-- TODO: If photo is not presense, print another image? -->
			<?php 
			$photo_info = $pa->getPhotoInfo($row['photo_id']); 
			if (!empty($photo_info)):
			?>
			<div class="image"><a><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$row['id'].SEP.'pid='.$row['photo_id'].SEP.'ph='.getPhotoFilePath($photo_info['id'], '', $photo_info['created_date']);?>" title="<?php echo htmlentities_utf8($photo_info['description']); ?>" alt="<?php echo htmlentities_utf8($photo_info['alt_text']); ?>" /></a></div>
			<?php endif; //image ?>
			<div class="info">
				<h4><a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$row['id'];?>"><?php echo htmlentities_utf8($row['name']); ?></a></h4>
				<p><?php echo htmlentities_utf8($row['description']); ?></p>
				<p><?php echo _AT('location').': '.htmlentities_utf8($row['location']); ?></p>
				<span>
				<p><?php echo _AT('last_updated', AT_date(_AT('forum_date_format'), $row['last_updated'], AT_DATE_MYSQL_DATETIME));?></p>
				<p><?php echo _AT('created').': '.AT_date(_AT('forum_date_format'), $row['created_date'], AT_DATE_MYSQL_DATETIME); ?></p>
				</span>
				<p><a href="<?php echo AT_PA_BASENAME;?>edit_album.php?id=<?php echo $row['id'];?>">Edit</a> | <a href="<?php echo AT_PA_BASENAME;?>delete_album.php?id=<?php echo $row['id'];?>"><?php echo _AT('delete');?></a></p>
			</div>
		</div>
		<?php endforeach; endif; ?>
		<!-- end loop -->
	</div>

	<!-- page numbers -->
	<div class="topbar">
		<div class="paginator">
			<ul>
				<li><a>1</a></li>
				<li><a>2</a></li>
				<li><a>3</a></li>
			</ul>
		</div>
	</div>
</div>