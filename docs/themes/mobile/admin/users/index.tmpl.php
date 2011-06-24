
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="left" style="width: 90%;">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'real_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'email'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'last_login'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_core/users/admins/index.php?<?php echo $orders[$order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('login_name');        ?></a></th>
	<th scope="col"><a href="mods/_core/users/admins/index.php?<?php echo $orders[$order]; ?>=real_name<?php echo $page_string; ?>"><?php echo _AT('real_name');   ?></a></th>
	<!-- REMOVED FOR MOBILE THEME -->
	<!-- <th scope="col"><a href="mods/_core/users/admins/index.php?<?php echo $orders[$order]; ?>=email<?php echo $page_string; ?>"><?php echo _AT('email');           ?></a></th> -->
	<!--<th scope="col"><a href="mods/_core/users/admins/index.php?<?php echo $orders[$order]; ?>=last_login<?php echo $page_string; ?>"><?php echo _AT('last_login'); ?></a></th> -->
	<th scope="col"><?php echo _AT('account_status'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="view_log" value="<?php echo _AT('view_log'); ?>" />
		<input type="submit" name="password" value="<?php echo _AT('password'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php if (mysql_num_rows($this->result) == 0) { ?>
	<tr>
		<td colspan="6"><?php echo _AT('no_admins_found'); ?></td>
	</tr>
<?php } else {
		while ($row = mysql_fetch_assoc($this->result)): ?>
			<tr onmousedown="document.form['m<?php echo $row['login']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['login']; ?>">
				<td><input type="radio" name="login" value="<?php echo $row['login']; ?>" id="m<?php echo $row['login']; ?>" /></td>
				<td><label for="m<?php echo $row['login']; ?>"><?php echo $row['login'];      ?></label></td>
				<td><?php echo $row['real_name'];  ?></td>
				<!--  REMOVED FOR MOBILE THEME
				<td><?php echo $row['email'];      ?></td>
				<td><?php 
					if ($row['last_login'] == '0000-00-00 00:00:00') {
						echo _AT('never');
					} else {
						echo $row['last_login'];
					} ?></td> -->
				<td><?php 
					if ($row['privileges'] == 1) { 
						echo _AT('priv_admin_super');
					} else if ($row['privileges'] > 0) {
						echo _AT('active_admin');
					} else {
						echo _AT('inactive_admin');
					}
				 ?> </td>
			</tr>
	 	<?php endwhile; ?>
	<?php } ?>
</tbody>
</table>
</form>
