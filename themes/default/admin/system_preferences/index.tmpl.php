
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">

<table class="data" summary="Title and URL of News feeds">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('url'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3">
		<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
		<input type="submit" name="edit"    value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="delete"  value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php 

if(count($this->rows_feeds) == 0){
?>

	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php } else { ?>
	<?php 
	    foreach($this->rows_feeds as $row){
		$title_file = AT_CONTENT_DIR.'feeds/'.$row['feed_id'].'_rss_title.cache'; ?>
		<tr onkeydown="document.form['f_<?php echo $row['feed_id']; ?>'].checked = true; rowselect(this);" onmousedown="document.form['f_<?php echo $row['feed_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['feed_id']; ?>">
			<td valign="top"><input type="radio" id="f_<?php echo $row['feed_id']; ?>" name="fid" value="<?php echo $row['feed_id']; ?>" /></td>
			<td><label for="f_<?php echo $row['feed_id']; ?>"><?php if (file_exists($title_file)) { readfile($title_file); } ?></label></td>
			<td><?php echo $row['url']; ?></td>
		</tr>
	<?php }  ?>

<?php } ?>
</tbody>
</table>
</form>