<tr>
	<td style="border-top: 1px solid #006699; background: #EFEFEF;">
		<table border="0" width="100%" cellspacing="2" cellpadding="0" bgcolor="white">
		<tr>
		<td bgcolor="#DEDEDE">
			<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
			<td><small><b><?php				
				echo $_SESSION['courtyard_id'];
				
				if ($_SESSION['house_id']) {
					if ($_SESSION['status'] == USER_ADMIN) {
						echo ' <small>('._AC('admin').')</small>';
					} else if ($_SESSION['status'] == USER_GROUP_ADMIN) {
						echo ' <small>('._AC('house_admin').')</small>';
					}
				} else if ($_SESSION['status'] == USER_ADMIN) {
					echo ' <small>('._AC('admin').')</small>';
				}
				?>.</b></small></td>
			<td align="right"><b><small>
				<select>
					<option>hello</option>
				</select>
			</small></b></td>
			</tr>
			</table>
		</td></tr>
	</table>
</td>
</tr>
