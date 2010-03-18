<?php
	//init
	$pa = new PhotoAlbum();
	$album_size = sizeof($this->albums);
	$photo_size = sizeof($this->photos);
?>

<div id="uploader-contents">
	<div class="album_panel">
		<div class="topbar">
			<div class="search_bar">
				<form action="<?php echo AT_PA_BASENAME.'search.php'; ?>" id="pa_search_form" name="pa_search_form" method="post">
					<input type="text" class="s" name="pa_search" value="<?php echo $this->search_input; ?>" title="<?php echo _AT('search');?>" />
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
			<h4><?php echo _AT('pa_albums') . ' ' .  _AT('results'). ': ' . $album_size. ' ' .  _AT('results'); ?></h4>
			<?php if($album_size > AT_PA_SEARCH_MIN_ALBUM): ?>
			<div class="search_slider_left"><a href="<?php echo $_SERVER['PHP_SELF']; ?>#n" onclick="slide('right');"><img src="<?php echo AT_PA_BASENAME; ?>images/prev.png" alt="<?php echo _AT('previous'); ?>" /></a></div>
			<?php endif; ?>
			<div class="search_slider search_slider_a" id="search_slider_a">
			<ul>
				<?php 				
				foreach($this->albums as $index=>$album): 
				$photo_info = $pa->getPhotoInfo($album['photo_id']); 				
				?>
				<li>
				<div class="search_photo_frame">
					<?php if (!empty($photo_info)): ?>
					<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$album['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$album['id'].SEP.'pid='.$album['photo_id'].SEP.'ph='.getPhotoFilePath($photo_info['id'], '', $photo_info['created_date']);?>" title="<?php echo htmlentities_utf82($photo_info['description']); ?>" alt="<?php echo htmlentities_utf82($album['name']); ?>" /></a>
					<?php else: ?>
					<a href="<?php echo AT_PA_BASENAME.'albums.php?id='.$album['id'];?>"><img class="no-image" title="<?php echo _AT('pa_no_image'); ?>" alt="<?php echo _AT('pa_no_image'); ?>" /></a>
					<?php endif; //album ?>
					<span><?php echo $album['name']; ?></span>
				</div>
				</li>
				<?php endforeach; ?>
			</ul>			
			</div>
			<?php if($album_size > AT_PA_SEARCH_MIN_ALBUM): ?>
			<div class="search_slider_right""><a href="<?php echo $_SERVER['PHP_SELF']; ?>#n" onclick="slide('left');"><img src="<?php echo AT_PA_BASENAME; ?>images/next.png" alt="<?php echo _AT('next'); ?>" /></a></div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<!-- photo panel -->
		<div class="album" style="min-width: 720px;">
			<h4><?php echo _AT('pa_photos') . ' ' .  _AT('results'). ': ' . $photo_size. ' ' .  _AT('results'); ?></h4>
			<?php if(!empty($this->photos)): ?>
			<!-- dynamic paginator -->
			<?php if($photo_size > AT_PA_PHOTO_SEARCH_PER_PAGE): ?>
			<div class="paginator"><div class="paging">
				<ul>
				<?php
					$pages = ceil($photo_size/AT_PA_PHOTO_SEARCH_PER_PAGE);
					for($i=1; $i <=$pages; $i++){
						echo '<li>';
						echo '<a id="p_'.$i.'" href="'. $_SERVER['PHP_SELF'] . '#n" ' . "onclick='go_to_page($i, $pages)' title='"._AT('page') . ' ' ."$i'>$i</li>";
						echo '</li>';
					}
				?>
				</ul>
			</div></div>
			<?php endif; ?>
			<!-- end dynamic paginator -->

			<div class="search_slider search_slider_p" id="search_slider_p">
			<ul>
			<!-- loop through this -->
			<?php 
			$loop_counter = 0;	//counts the loop
			foreach($this->photos as $key=>$photo): 
			?>
			<?php 
				if ($loop_counter==0){
					echo '<li>';
				}
				$loop_counter++;				
			?>
			<div class="photo_frame">
				<a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$photo['id'].SEP.'aid='.$photo['album_id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$photo['album_id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf82($photo['description'], false); ?>" alt="<?php echo htmlentities_utf82($photo['alt_text']);?>" /></a>
			</div>
			<?php 
				if ($loop_counter>=AT_PA_PHOTO_SEARCH_PER_PAGE) {
					echo '</li>';
					$loop_counter = 0;
				}	
			?>
			<?php endforeach; ?>
			<!-- end loop -->
			</ul></div>
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
var album_size = <?php echo $album_size; ?>; //size of albums
var photo_size = <?php echo $photo_size; ?>; //size of photos
var ALBUM_PIC_WIDTH = <?php echo AT_PA_ALBUM_PIC_WIDTH; ?>;	//check the CSS and constants.inc.php
var PHOTO_PIC_WIDTH = <?php echo AT_PA_PHOTO_PIC_WIDTH; ?>;	

/* 
 * Slide the album list 
 * @param	string		left/right
 */
function slide(direction){
	//variables	
	album_ul = jQuery('#search_slider_a').find('ul');	
	if (direction=='left'){
		album_cnt++;
	} else {
		album_cnt--;
	}
	var album_offset = -1 * ALBUM_PIC_WIDTH * album_cnt;

	//action
	if (album_size * ALBUM_PIC_WIDTH + album_offset > 0 && album_cnt >= 0){
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

/*
 * click on the page and will slide accordingly 
 * @param	int	 page number
 */
function go_to_page(page, max_page){
	//variables	
	photo_ul = jQuery('#search_slider_p').find('ul');
	photo_offset = -1 * PHOTO_PIC_WIDTH * (page - 1) * 5;
	
	//action
	if (page >= 1 && page <= max_page){
		jQuery("a[id^='p_']").removeClass('current');	//remove all selector prefixed with "p_"
		jQuery('#p_'+page).addClass('current');	//set current 
		photo_ul.animate({left: photo_offset});
	}
}


//]]>
</script>