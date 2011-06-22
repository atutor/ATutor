hi I'm an administrator

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="tab" value="<?php echo $this->current_tab; ?>"/>
	<input type="hidden" name="course_id" value="<?php echo $this->course_id; ?>"/>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('search'); ?></legend>
		<?php if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE)): ?>
			<div class="row">
				<label for="course"><?php echo _AT('course'); ?></label><br/>
				<select name="course_id" id="course">
				<?php
				
				while ($courses_row = mysql_fetch_assoc($this->result)) {
					if ($courses_row['course_id'] == $this->course_id) {
						echo '<option value="'.$courses_row['course_id'].'" selected="selected">'.validate_length($courses_row['title'], 45,VALIDATE_LENGTH_FOR_DISPLAY).'</option>';
					} else {
						echo '<option value="'.$courses_row['course_id'].'">'.validate_length($courses_row['title'],45,VALIDATE_LENGTH_FOR_DISPLAY).'</option>';
					}
				}
				?></select>
			</div>
		<?php endif; ?>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('login_name').', '._AT('first_name').', '._AT('second_name').', '._AT('last_name') .', '._AT('email'); ?>)</label><br />
			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<br/>
			<?php echo _AT('search_match'); ?>:
			<input type="radio" name="match" value="all" id="match_all" <?php echo $this->checked_match_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> <input type="radio" name="match" value="one" id="match_one" <?php echo $this->checked_match_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</fieldset>
	</div>
</form>

<?php print_paginator($this->page, $this->tab_counts[$this->current_tab], $this->page_string_w_tab . SEP . $this->order .'='. $this->col, $this->results_per_page); ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="tab" value="<?php echo $this->current_tab; ?>" />
<input type="hidden" name="course_id" value="<?php echo $this->course_id; ?>"/>

<ul id="subnavlist">
	<?php for ($i = 0; $i< $this->num_tabs; $i++): ?>
		<?php if ($this->current_tab == $i): ?>
			<li class="active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.$this->page_string; ?>" class="active"><strong><?php echo _AT($this->tabs[$i]); ?> - <?php echo $this->tab_counts[$i]; ?></strong></a></li>
		<?php else: ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.$this->page_string; ?>"><?php echo _AT($this->tabs[$i]); ?> - <?php echo $this->tab_counts[$i]; ?></a></li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>


<table class="data" style="width:95%;" summary="" rules="cols" >
<colgroup>
	<?php if ($this->col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($this->col == 'first_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($this->col == 'second_name'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($this->col == 'last_name'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php elseif($this->col == 'email'): ?>
		<col span="5" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $this->orders[$this->order]; ?>=login<?php echo $this->page_string_w_tab;?>"><?php echo _AT('login_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $this->orders[$this->order]; ?>=first_name<?php echo $this->page_string_w_tab;?>"><?php echo _AT('first_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $this->orders[$this->order]; ?>=second_name<?php echo $this->page_string_w_tab;?>"><?php echo _AT('second_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $this->orders[$this->order]; ?>=last_name<?php echo $this->page_string_w_tab;?>"><?php echo _AT('last_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $this->orders[$this->order]; ?>=email<?php echo $this->page_string_w_tab;?>"><?php echo _AT('email'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<?php if ($this->current_tab == 0): ?>
			<input type="submit" name="role"     value="<?php echo _AT('privileges');  ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove');    ?>" /> 
			<input type="submit" name="alumni"   value="<?php echo _AT('mark_alumni'); ?>" />
		<?php elseif ($this->current_tab == 1): ?>
			<input type="submit" name="role" value="<?php echo _AT('privileges'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" /> 

		<?php elseif ($this->current_tab == 2): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" />
		
		<?php elseif ($this->current_tab == 3): ?>
			<input type="submit" name="enroll" value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" />

		<?php elseif ($this->current_tab == 4): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 

		<?php endif; ?></td>
</tr>
</tfoot>
<tbody>
<?php if ($this->tab_counts[$this->current_tab]): ?>
	<?php while ($row = mysql_fetch_assoc($this->enrollment_result)): ?>
		<tr onmousedown="document.selectform['m<?php echo $row['member_id']; ?>'].checked = !document.selectform['m<?php echo $row['member_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['member_id']; ?>');" id="rm<?php echo $row['member_id']; ?>">
			<td><input type="checkbox" name="id[]" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" onmouseup="this.checked=!this.checked" title="<?php echo AT_print($row['login'], 'members.login'); ?>" /></td>
			<td><?php echo AT_print($row['login'], 'members.login'); ?></td>
			<td><?php echo AT_print($row['first_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['second_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['last_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['email'], 'members.email'); ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>