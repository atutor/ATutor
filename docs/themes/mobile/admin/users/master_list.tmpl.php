
<form name="importForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('update_list'); ?></h3>
		<label for="file"><?php echo _AT('file'); ?></label><br />
		<input type="file" name="file" size="40" id="file" />
	</div>
	
	<div class="row">
		<fieldset>
		<legend><?php echo _AT('master_not_in_list'); ?></legend>
		<input type="radio" name="override" id="o0" value="0" checked="checked" /><label for="o0"><?php echo _AT('leave_unchanged'); ?></label>
		<input type="radio" name="override" id="o1" value="1" /><label for="o1"><?php echo _AT('disable');     ?></label>
		</fieldset>
	</div>

	<div class="row buttons">
		<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
	</div>
</div>
</form>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
		</div>

		<div class="row">
			<fieldset>
			<legend><?php echo _AT('account_status'); ?></legend>
			<input type="radio" name="status" value="1" id="s0" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('not_created'); ?></label> 

			<input type="radio" name="status" value="2" id="s1" <?php if ($_GET['status'] == 2) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('created'); ?></label> 

			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label> 
			</fieldset>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('student_id'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$this->num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><strong><?php echo $i; ?></strong></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>


<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />

<table summary="Table lists results by Student ID, Login Name, First Name, Second Name, and Last Name." class="data" rules="cols" >
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('student_id'); ?></th>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('last_name'); ?></th>
</tr>
</thead>
<?php if ($this->num_results > 0): ?>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php while($row = mysql_fetch_assoc($this->result)): ?>
		<tr onmousedown="document.form['m<?php echo $row['public_field']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['public_field']; ?>">
			<td><input type="radio" name="id" value="<?php 
				if ($row['member_id']) {
					echo $row['member_id'];
				} else {
					echo '-'.$row['public_field'];
				}
				?>" id="m<?php echo $row['public_field']; ?>" /></td>
			<td><label for="m<?php echo $row['public_field']; ?>"><?php echo $row['public_field']; ?></label></td>
			<td><?php
				if ($row['member_id']) {
					echo $row['login'];
				} else {
					echo '-';
				}
				?></td>

			<td><?php
				if ($row['member_id']) {
					echo $row['last_name'];
				} else {
					echo '-';
				}
				?></td>
		</tr>
	<?php endwhile; ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>

</table>
</form>