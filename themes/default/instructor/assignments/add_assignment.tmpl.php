<?php exit; // this template does not seem to be use. ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $this->id ?>" />
<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_assignment'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php  echo _AT('title'); ?></label><br/>
		<input type="text" name="title" size="50" id="title" value="<?php echo AT_print($this->title, 'assignment.title'); ?>" />
	</div>

	<div class="row">
		<label for="assignto"><?php  echo _AT('assign_to'); ?></label><br/>

		<?php // Are we editing an assignment?
			if ($this->id != '0'){
				// editing an existing assignment 
				if ($this->assign_to == '0'){ 
					echo _AT('all_students'); 
				} else { // name of group goes here
					
					$type_row = mysql_fetch_assoc($this->result_group);
					echo $type_row['title'];
				}
				?>
			<?php } else { // creating a new assignment
			?>
				<select name="assign_to" size="5" id="assignto">
					<option value="0" <?php if ($this->assign_to == '0'){ echo 'selected="selected"'; } ?> label="<?php  echo _AT('all_students'); ?>"><?php  echo _AT('all_students'); ?></option>
					<optgroup label="<?php  echo _AT('specific_groups'); ?>">
						<?php
							
							while ($type_row = mysql_fetch_assoc($result_assign)) {
								echo '<option value="'.$type_row['type_id'].'" ';
								if ($this->assign_to == $type_row['type_id']) {
									echo 'selected="selected"';
								}
								echo '>'.$type_row['title'].'</option>';
							}
						?>
					</optgroup>
				</select>
			<?php }	?>
	</div>	

	<div class="row">
		<?php  echo _AT('due_date'); ?><br />
		<input type="radio" name="has_due_date" value="false" id="noduedate" <?php if ($this->has_due_date == 'false') { echo 'checked="checked"'; } ?> 
		onfocus="disable_dates (true, '_due');" />
		<label for="noduedate" title="<?php echo _AT('due_date'). ': '. _AT('none');  ?>"><?php echo _AT('none'); ?></label><br />

		<input type="radio" name="has_due_date" value="true" id="hasduedate" <?php if ($this->has_due_date == 'true'){echo 'checked="checked"'; } ?> 
		onfocus="disable_dates (false, '_due');" />
		<label for="hasduedate"  title="<?php echo _AT('due_date') ?>"><?php  echo _AT('date'); ?></label>

		<?php
			$today_day  = $dueday;
			$today_mon  = $duemonth;
			$today_year = $dueyear;
			$today_hour = $duehour;
			$today_min  = $dueminute;
			
			$name = '_due';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</div>

	<div class="row">
		<?php  echo _AT('accept_late_submissions'); ?><br />
		<input type="radio" name="late_submit" value="0" id="always"  <?php if ($this->late_submit == '0'){echo 'checked="checked"';} ?> 
		onfocus="disable_dates (true, '_cutoff');" />

		<label for="always" title="<?php echo _AT('accept_late_submissions'). ': '. _AT('always');  ?>"><?php echo _AT('always'); ?></label><br />

		<input type="radio" name="late_submit" value="1" id="never"  <?php if ($this->late_submit == '1'){echo 'checked="checked"';} ?>
		onfocus="disable_dates (true, '_cutoff');" />

		<label for="never" title="<?php echo _AT('accept_late_submissions'). ': '. _AT('never');  ?>"><?php  echo _AT('never'); ?></label><br />

		<input type="radio" name="late_submit" value="2" id="until"  <?php if ($this->late_submit == '2'){echo 'checked="checked"';} ?>
		onfocus="disable_dates (false, '_cutoff');" />

		<label for="until" title="<?php echo _AT('accept_late_submissions'). ': '. _AT('until');  ?>"><?php  echo _AT('until'); ?></label>

		<?php
			$today_day  = $cutoffday;
			$today_mon  = $cutoffmonth;
			$today_year = $cutoffyear;
			$today_hour = $cutoffhour;
			$today_min  = $cutoffminute;
			
			$name = '_cutoff';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</div>
	<?php
	/****
	 * not included in the initial release.
	 *
	<div class="row">
		<?php  echo _AT('options'); <br/>
		<input type="checkbox" name="multi_submit" id="multisubmit" <?php if ($multi_submit == '1'){ echo 'checked="checked"'; }  />
		<label for="multisubmit"><?php  echo _AT('allow_re_submissions'); </label>
	</div>
	***/
	?>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" class="button"/>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="button"/>
	</div>
	</fieldset>
</div>

</form>