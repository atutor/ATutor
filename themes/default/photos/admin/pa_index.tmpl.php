<?php 
	//init
	$pa = new PhotoAlbum();
?>

<div class="paginator">
	<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_ADMIN_ALBUMS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
</div>
<div>
<form action="" method="post" name="form">
	<table class="data" rules="cols">
		<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo _AT('pa_album_name'); ?></th>
			<th><?php echo _AT('pa_album_type'); ?></th>
			<th><?php echo _AT('pa_album_description'); ?></th>
			<th><?php echo _AT('created_by'); ?></th>
			<th><?php echo _AT('pa_last_updated'); ?></th>
		</tr>		
		</thead>
		<tbody>
		<?php if(!empty($this->albums)): ?>
		<?php foreach ($this->albums as $aid=>$row): ?>
		<tr id="r_<?php echo $aid; ?>" onmousedown="jQuery('#album_<?php echo $aid; ?>').attr('checked', true); rowselect(this);">
			<td><input type="radio" id="album_<?php echo $aid; ?>" name="aid" value="<?php echo $aid; ?>" /></td>
			<td><a href="<?php echo AT_PA_BASENAME."admin/edit_photos.php?aid=$aid"; ?>"><?php echo AT_print($row['name'], 'input.text'); ?></a></td>
			<td><?php echo $pa->getAlbumTypeName($row['type_id']); ?></td>
			<td><?php echo AT_print($row['description'], 'photo_albums.description'); ?></td>
			<td><?php echo AT_print(get_display_name($row['member_id']), 'members.full_name'); ?></td>
			<td><?php echo AT_date(_AT('forum_date_format'), $row['last_updated'], AT_DATE_MYSQL_DATETIME); ?></td>
		</tr>
		<?php endforeach; ?>

		</tbody>
		<tfoot>
		<tr>
			<td colspan="6">
				<input type="submit" value="<?php echo _AT('edit'); ?>" name="edit" />
				<input type="submit" value="<?php echo _AT('delete'); ?>" name="delete" />
			</td>
		</tr>
		</tfoot>
		<?php else: ?>
			<tr>
			<td colspan="5"><?php echo _AT('none_found'); ?></td>
			</tr>
		<?php endif; ?>
	</table>
</form>
</div>
<div class="paginator">
	<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTOS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
</div>
