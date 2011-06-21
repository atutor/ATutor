
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('account_status'); ?><br />
			<input type="radio" name="status" value="0" id="s0" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('disabled'); ?></label> 

			<input type="radio" name="status" value="1" id="s1" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unconfirmed'); ?></label> 

			<input type="radio" name="status" value="2" id="s2" <?php if ($_GET['status'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('student'); ?></label>

			<input type="radio" name="status" value="3" id="s3" <?php if ($_GET['status'] == 3) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('instructor'); ?></label>

			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] === '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('login_name').', '._AT('first_name').', '._AT('second_name').', '._AT('last_name') .', '._AT('email'); ?>)</label><br />

			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<br/>
			<?php echo _AT('search_match'); ?>:
			<input type="radio" name="include" value="all" id="match_all" <?php echo $checked_include_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> 
			<input type="radio" name="include" value="one" id="match_one" <?php echo $checked_include_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
		</div>

		<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
			<div class="row">
				<label for="searchid"><?php echo _AT('search'); ?> (<?php echo _AT('student_id'); ?>)</label><br />
				<input type="text" name="searchid" id="searchid" size="20" value="<?php echo htmlspecialchars($_GET['searchid']); ?>" />
			</div>
		<?php endif; ?>

		<div class="row">
			<label for="last_login_have"><?php echo _AT('last_login'); ?></label><br />					
			<select name="last_login_have" id="last_login_have">
				<option value="-1">- <?php echo _AT('select'); ?> -</option>
				<option value="1" <?php if($_GET['last_login_have']=='1') { echo 'selected="selected"';}?>><?php echo _AT('have'); ?></option>
				<option value="0" <?php if(isset($_GET['last_login_have']) && $_GET['last_login_have']=='0') { echo 'selected="selected"';}?>><?php echo _AT('have_not'); ?></option>
			</select> <?php echo _AT('logged_in_within'); ?>: <input type="text" name="last_login_days" size="3" value="<?php echo htmlspecialchars($_GET['last_login_days']); ?>" /> <?php echo _AT('days'); ?> <br />
			
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>
<?php print_paginator($this->page, $this->num_results, $this->page_string . SEP . $this->order .'='. $col, $this->results_per_page); ?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />
<input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
<input type="hidden" name="include" value="<?php echo htmlspecialchars($_GET['include']); ?>" />

<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {  $col_counts = 1; } else { $col_counts = 0; } ?>
<table summary="" class="data" rules="rows">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="<?php echo 5 + $col_counts; ?>" />

	<?php elseif($col == 'first_name'): ?>
		<col span="<?php echo 2 + $col_counts; ?>" />
		<col class="sort" />
		<col span="5" />
	
	<?php elseif($col == 'last_name'): ?>
		<col span="<?php echo 4 + $col_counts; ?>" />
		<col class="sort" />
		<col span="3" />

	<?php elseif($col == 'status'): ?>
		<col span="<?php echo 6 + $col_counts; ?>" />
		<col class="sort" />
		<col />
	

	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col"><a href="mods/_core/users/users.php?<?php echo $this->orders[$this->order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('login_name');      ?></a></th>
	<th scope="col"><a href="mods/_core/users/users.php?<?php echo $this->orders[$this->order]; ?>=first_name<?php echo $page_string; ?>"><?php echo _AT('first_name'); ?></a></th>
	<th scope="col"><a href="mods/_core/users/users.php?<?php echo $this->orders[$this->order]; ?>=last_name<?php echo $page_string; ?>"><?php echo _AT('last_name');   ?></a></th>
	<th scope="col"><a href="mods/_core/users/users.php?<?php echo $this->orders[$this->order]; ?>=status<?php echo $page_string; ?>"><?php echo _AT('account_status'); ?></a></th>	
</tr>

</thead>
<?php if ($this->num_results > 0): ?>
	<tfoot>
	<tr>
		<td colspan="<?php echo 9 + $col_counts; ?>">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
			<input type="submit" name="password" value="<?php echo _AT('password'); ?>" />
			<?php if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, true)): ?>
				<input type="submit" name="enrollment" value="<?php echo _AT('enrollment'); ?>" />
			<?php endif; ?>
			
			
			<select name="change_status">
				<option value="-2"><?php echo _AT('more_options'); ?></option>
				<optgroup label="<?php echo _AT('status'); ?>">
					<option value="<?php echo AT_STATUS_STUDENT; ?>"><?php echo _AT('student'); ?></option>
					<option value="<?php echo AT_STATUS_INSTRUCTOR; ?>"><?php echo _AT('instructor'); ?></option>	
					<?php if ($_config['email_confirmation']): ?>
						<option value="<?php echo AT_STATUS_UNCONFIRMED; ?>"><?php echo _AT('unconfirmed'); ?></option>
					<?php endif; ?>
					<option value="<?php echo AT_STATUS_DISABLED; ?>"><?php echo _AT('disable'); ?></option>				
				</optgroup>
				<option value="-2" disabled="disabled">- - - - - - - - -</option>	
				<option value="-1"><?php echo _AT('delete'); ?></option>				
			</select>
			<input type="submit" name="apply" value="<?php echo _AT('apply'); ?>" />
			<input type="submit" name="apply_all" value="<?php echo _AT('apply_to_all_results'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>
		<?php while($row = mysql_fetch_assoc($this->result)): ?>
			<tr onmousedown="document.form['m<?php echo $row['member_id']; ?>'].checked = !document.form['m<?php echo $row['member_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['member_id']; ?>');" id="rm<?php echo $row['member_id']; ?>">
				<td><input type="checkbox" name="id[]" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" onmouseup="this.checked=!this.checked" /></td>
				<td><?php echo $row['login']; ?></td>
				
				<?php $startend_date_longs_format=_AT('startend_date_longs_format'); ?>
				<td><?php echo AT_print($row['first_name'], 'members.first_name'); ?></td>
				<td><?php echo AT_print($row['last_name'], 'members.last_name'); ?></td>
		
				<td><?php echo get_status_name($row['status']); ?></td>
			
				
			</tr>
		<?php endwhile; ?>
	</tbody>
<?php else: ?>
	<tr>
		<td colspan="<?php echo 9 + $col_counts; ?>"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</table>
</form>
