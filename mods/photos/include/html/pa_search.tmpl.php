<?php
	//init
	$pa = new PhotoAlbum();
?>

<div id="uploader-contents">
	<div class="album_panel">
		<div class="topbar">
			<div class="search_bar">
				<form action="<?php echo AT_PA_BASENAME.'search.php'; ?>" id="pa_search_form" name="pa_search_form" method="post">
					<input type="text" class="s" name="pa_search" value="<?php echo $this->search_input; ?>" />
					<input type="image" class="s_img" src="<?php echo AT_PA_BASENAME; ?>images/search_icon.png" alt="<?php echo _AT('search');?>" />
				</form>
			</div>
			<?php if($this->num_rows > AT_PA_ALBUMS_PER_PAGE): ?>
			<!-- page numbers -->
			<div class="paginator">
				<?php print_paginator($this->page, $this->num_rows, 'type='.$this->type, AT_PA_ALBUMS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
			</div>
			<?php endif; ?>
		</div>

		<!-- album panel -->
		<?php if(!empty($this->albums)): ?>
		<div class="album">
			<h4><?php echo _AT('pa_albums'); ?></h4>
			<div class="search_slider_left"><a href="<?php echo $_SERVER['PHP_SELF']; ?>#n" onclick="slide('right');"><?php echo _AT('previous'); ?></a></div>
			<div class="search_slider" id="search_slider_a">			
			<ul>
				<?php foreach($this->albums as $index=>$album): 
				$photo_info = $pa->getPhotoInfo($album['photo_id']); 
				?>		
				<li>
				<div class="search_photo_frame">
					<?php if (!empty($photo_info)): ?>
					<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$album['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$album['id'].SEP.'pid='.$album['photo_id'].SEP.'ph='.getPhotoFilePath($photo_info['id'], '', $photo_info['created_date']);?>" title="<?php echo htmlentities_utf82($photo_info['description']); ?>" alt="<?php echo htmlentities_utf82($photo_info['alt_text']); ?>" /></a>
					<?php else: ?>
					<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$album['id'];?>"><img class="no-image" title="<?php echo _AT('pa_no_image'); ?>" alt="<?php echo _AT('pa_no_image'); ?>" /></a>
					<?php endif; //album ?>
					<span><?php echo 'Pt: ' .$album['point']. '<br/>' . $album['name']; ?></span>
				</div>
				</li>
				<?php endforeach; ?>
			</ul>			
			</div>
			<div class="search_slider_right""><a href="<?php echo $_SERVER['PHP_SELF']; ?>#n" onclick="slide('left');"><?php echo _AT('next'); ?></a></div>
		</div>
		<?php endif; ?>
		
		<!-- photo panel -->
		<div class="album">
			<h4><?php echo _AT('pa_photos'); ?></h4>
			<?php if(!empty($this->photos)): ?>		
			<!-- loop through this -->
			<?php foreach($this->photos as $key=>$photo): ?>
			<div class="photo_frame">
				<a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$photo['id'].SEP.'aid='.$photo['album_id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$photo['album_id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf82($photo['description'], false); ?>" alt="<?php echo htmlentities_utf82($photo['alt_text']);?>" /></a>
			</div>
			<?php endforeach; ?>
			<!-- end loop -->
			<?php else: ?>
			<div class="edit_photo_box">
				<p><?php echo _AT('pa_no_photos'); ?></p>
			</div>
		<?php endif; ?>			
		</div>
	</div>
</div>


<script type="text/javascript">
//<![CDATA[
var album_cnt = 0;	//number of times, global
var album_size = <?php echo sizeof($this->albums); ?>; //size of albums

/* 
 * Slide the album list 
 * @param	string		left/right
 */
function slide(direction){
	//variables
	var PIC_WIDTH = 147;	//check the CSS
	album_ul = jQuery('#search_slider_a').find('ul');	
	if (direction=='left'){
		album_cnt++;
	} else {
		album_cnt--;
	}
	var album_offset = -1 * PIC_WIDTH * album_cnt;

	//action
	if (album_size * PIC_WIDTH + album_offset > 0){
		album_ul.animate({left: album_offset});
	} else {
		//undo counts
		if (direction=='left'){
			album_cnt--;
		} else {
			album_cnt++;
		}
	}
}
//]]>
</script>