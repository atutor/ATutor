
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="table-surround">
<table class="data" summary="Table listing instructor requets" >
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('login_name');     ?></th>
	<!-- REMOVED FOR MOBILE <th scope="col"><?php echo _AT('first_name');   ?></th>-->
	<!-- <th scope="col"><?php echo _AT('last_name');    ?></th>  -->
	<!--  <th scope="col"><?php echo _AT('email');        ?></th> -->
	<th scope="col"><?php echo _AT('notes');        ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
	<input type="submit" name="deny" value="<?php echo _AT('deny'); ?>" /> 
	<input type="submit" name="approve" value="<?php echo _AT('approve'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
	if ($row = mysql_fetch_assoc($this->result)) {
		do {
			echo '<tr onkeydown ="document.form[\'i'.$row['member_id'].'\'].checked = true;rowselect(this);" onmousedown="document.form[\'i'.$row['member_id'].'\'].checked = true;rowselect(this);" id="r_'.$row['member_id'].'">';
			echo '<td><input type="radio" name="id" value="'.$row['member_id'].'" id="i'.$row['member_id'].'" /></td>';
			echo '<td><label for="i'.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</label></td>';
			// REMOVED FOR MOBILE
			// echo '<td>'.AT_print($row['first_name'], 'members.first_name').'</td>';
		    // echo '<td>'.AT_print($row['last_name'], 'members.last_name').'</td>';
			// echo '<td>'.AT_print($row['email'], 'members.email').'</td>';
			
			echo '<td>'.AT_print($row['notes'], 'instructor_approvals.notes').'</td>';

			echo '</tr>';
		} while ($row = mysql_fetch_assoc($this->result));
	} else {
		echo '<tr><td colspan="6">'._AT('none_found').'</td></tr>';
	}
?>
</tbody>
</table>
</div>
</form>
