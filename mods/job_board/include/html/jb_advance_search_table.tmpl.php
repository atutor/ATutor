<?php echo _AT('jb_search_filter_blub'); ?>
<table>
	<tr>
		<td><?php echo _AT('jb_categories'); ?></td>
		<td>
			<div>
			<input type="checkbox" name="jb_search_categories[]" value="0" id="jb_search_category_0" <?php if(!empty($_GET['jb_search_categories']) && in_array(0, $_GET['jb_search_categories'])){echo 'checked="checked"';} ?>/>
			<label for="jb_search_category_0"><?php echo _AT('jb_any_categories');?></label>
			<?php foreach($this->job_obj->getCategories() as $category): ?>
			<div>
				<?php 					
					if(!empty($_GET['jb_search_categories'])){
						if(in_array($category['id'], $_GET['jb_search_categories'])){
							$jb_search_categories_checked =  'checked="checked"';
						} else {
							$jb_search_categories_checked = '';
						}
					}
				?>
				<input type="checkbox" name="jb_search_categories[]" value="<?php echo $category['id']; ?>" id="<?php echo 'jb_search_category_'.$category['id']; ?>" <?php echo $jb_search_categories_checked; ?>/>
				<label for="<?php echo 'jb_search_category_'.$category['id']; ?>"><?php echo $this->job_obj->getCategoryNameById($category['id']); ?></label>
			</div>
			<?php endforeach; ?>
			</div>
		</td>
	</tr>
	<tr>
		<td><label for="jb_search_bookmark"><?php echo _AT('jb_bookmarks'); ?></td>
		<td><input type="checkbox" name="jb_search_bookmark" id="jb_search_bookmark" <?php if($_GET['jb_search_bookmark']!=''){echo 'checked="checked"';} ?> /></td>
	</tr>
	<tr>
		<td><label for="jb_search_archive"><?php echo _AT('jb_archive'); ?></td>
		<td><input type="checkbox" name="jb_search_archive" id="jb_search_archive" <?php if($_GET['jb_search_archive']!=''){echo 'checked="checked"';} ?> /></td>
	</tr>
</table>
