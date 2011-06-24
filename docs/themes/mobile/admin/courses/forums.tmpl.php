
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="groups" style="width: 90%">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title');       ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('courses');  
	   ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>

</tfoot>
<tbody>

<tr>
	<th colspan="4"><?php echo _AT('shared_forums'); ?></th>
</tr>
<?php 
foreach ($this->shared_forums as $forum) {

?>
<!--     <tr onmousedown="document.form[\'f'.$forum['forum_id'].'\'].checked = true; rowselect(this);"  id="r_'.$forum['forum_id'].'"></tr> -->      
    <tr onmousedown="document.form['f<?php echo $forum['id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $forum['id']; ?>">
    <td><input type="radio" name="id" value= "<?php echo $forum['id']; ?>" id="f<?php echo $forum['id']; ?>"</td>
	<td><label for="f<?php echo $forum['id']; ?>"> <?php echo	AT_print($forum['title'], 'forums.title'); ?>  </label></td>
	<td><?php echo AT_print($forum['desc'], 'forums.description'); ?></td>
	<td> <?php foreach ($forum["courses"] as $course) {echo $course. "  ";} ?>
	</td>
	</tr>
<?php }?>

</tbody>
<tbody>
	<tr>
		<th colspan="4"><?php echo _AT('unshared_forums'); ?></th>
	</tr>
<?php if ($this->num_nonshared) : ?>
	<?php foreach ($this->all_forums['nonshared'] as $forum) : ?>
		<tr onmousedown="document.form['f<?php echo $forum['forum_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $forum['forum_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $forum['forum_id']; ?>" id="f<?php echo $forum['forum_id']; ?>" /></td>
			<td><label for="f<?php echo $forum['forum_id']; ?>"><?php echo AT_print($forum['title'], 'forums.title'); ?></label></td>
			<td><?php echo AT_print($forum['description'], 'forums.description'); ?></td>
			<td><?php echo $this->system_courses[$forum['course_id']]['title']; ?></td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="4"><strong><?php echo _AT('no_forums'); ?></strong></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>