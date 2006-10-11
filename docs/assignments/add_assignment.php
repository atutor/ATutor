<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ASSIGNMENTS);

// initial values for controls
$id = 0;
$today = getdate();

// Are we editing an existing assignment or creating a new assignment?
if (isset ($_GET['id'])){
	// editing an existing assignment
	$id = intval($_GET['id']); 

	$sql = "SELECT * FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] AND assignment_id=$id";

	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		// should not happen
		$msg->addFeedback('ASSIGNMENT_NOT_FOUND');
		header('Location: index_instructor.php');
		exit;
	}

	// get values of existing assignment from database
	$title			= $row['title'];
	$assign_to		= $row['assign_to'];
	$multi_submit	= $row['multi_submit'];

	$array1			= explode (' ', $row['date_due'], 2);
	$array_date_due	= explode ('-', $array1[0],3);
	$array_time_due	= explode (':', $array1[1]);
	$dueyear		= $array_date_due[0];
	$duemonth		= $array_date_due[1];
	$dueday			= $array_date_due[2];
	$duehour		= $array_time_due[0];
	$dueminute		= $array_time_due[1];

	if ($dueyear == '0000'){
		$has_due_date = 'false';
	} else {
		$has_due_date = 'true';
	}

	// use date from database
	$array2 = explode (' ', $row['date_cutoff'], 2);
	$array_date_cutoff = explode ('-', $array2[0],3);
	$array_time_cutoff = explode (':', $array2[1]);
	$cutoffyear		= $array_date_cutoff[0];
	$cutoffmonth	= $array_date_cutoff[1];
	$cutoffday		= $array_date_cutoff[2];
	$cutoffhour		= $array_time_cutoff[0];
	$cutoffminute	= $array_time_cutoff[1];

	if ($cutoffyear == '0000'){
		$late_submit	= '0'; // allow late submissions always
	} else if ($row['date_cutoff'] == $row['date_due']){
		$late_submit	= '1'; // allow late submissions never
		// use today's date as default
		$cutoffday		= $today['mday'];
		$cutoffmonth	= $today['mon'];
		$cutoffyear		= $today['year'];
		$cutoffhour		= $today['hours'];
		$cutoffminute	= $today['minutes'];
		// round the minute to the next highest multiple of 5 
		$cutoffminute = round($cutoffminute / '5' ) * '5' + '5';
		if ($cutoffminute > '55'){ $cutoffminute = '55'; }
	} else {
		$late_submit	= '2'; // allow late submissions until (date)
	}
}
else if (isset($_POST['cancel'])) {
	// cancel, nothing happened
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
}
else if (isset($_POST['submit'])) {
	// user has submitted form to update database
	$id = intval ($_POST['id']);

	if ($_POST['multi_submit'] == 'on'){
		$multi_submit = '1';
	}

	// get values from form that was just submitted
	$title			= $addslashes($_POST['title']);
	$assign_to		= intval($_POST['assign_to']);
	$has_due_date	= $addslashes($_POST['has_due_date']);
	$late_submit	= intval($_POST['late_submit']);

	$dueday			= intval($_POST['day_due']);
	$duemonth		= intval($_POST['month_due']);
	$dueyear		= intval($_POST['year_due']);
	$duehour		= intval($_POST['hour_due']);
	$dueminute		= intval($_POST['min_due']);

	$cutoffday		= intval($_POST['day_cutoff']);
	$cutoffmonth	= intval($_POST['month_cutoff']);
	$cutoffyear		= intval($_POST['year_cutoff']);
	$cutoffhour		= intval($_POST['hour_cutoff']);
	$cutoffminute	= intval($_POST['min_cutoff']);

	// ensure title is not empty
	if (trim($title) == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}

	// If due date is set and user has selected 'accept late submission until'
	// then ensure cutoff date is greater or equal to due date.
	if (($has_due_date == 'true') && ($late_submit == '2')){
		if ($cutoffyear < $dueyear){
			$msg->addError('CUTOFF_DATE_WRONG');
		} else if ($cutoffyear == $dueyear){
			if ($cutoffmonth < $duemonth){
				$msg->addError('CUTOFF_DATE_WRONG');
			} else if ($cutoffmonth == $duemonth){
				if ($cutoffday < $dueday){
					$msg->addError('CUTOFF_DATE_WRONG');
				} else if ($cutoffday == $dueday){
					if ($cutoffhour < $duehour){
						$msg->addError('CUTOFF_DATE_WRONG');
					} else if ($cutoffhour == $duehour) {
						if ($cutoffminute < $dueminute){
							$msg->addError('CUTOFF_DATE_WRONG');
						}
					}
				}
			}
		}
	}

	if (!$msg->containsErrors()) {
		$multi_submit = 0;

		// create the date strings
		$date_due = '0';
		$date_cutoff = '0';

		// note: if due date is NOT set then ignore the late submission date
		if ($has_due_date == 'true'){
			$date_due = $dueyear. '-' .str_pad ($duemonth, 2, "0", STR_PAD_LEFT). '-' .str_pad ($dueday, 2, "0", STR_PAD_LEFT). ' '.str_pad ($duehour, 2, "0", STR_PAD_LEFT). ':' .str_pad ($dueminute, 2, "0", STR_PAD_LEFT) . ':00';
		}

		if ($late_submit == '1'){ // never accept late submissions
			$date_cutoff = $date_due; // cutoff date will be same as due date
		} else if ($late_submit == '2'){ // accept late submissions until date
			$date_cutoff = $cutoffyear. '-' .str_pad ($cutoffmonth, 2, "0", STR_PAD_LEFT). '-' .str_pad ($cutoffday, 2, "0", STR_PAD_LEFT). ' '.str_pad ($cutoffhour, 2, "0", STR_PAD_LEFT). ':' .str_pad ($cutoffminute, 2, "0", STR_PAD_LEFT) . ':00';
		}

		// Are we creating a new assignment or updating an existing assignment?
		if ($id == '0'){
			// creating a new assignment
			$sql = "INSERT INTO ".TABLE_PREFIX."assignments VALUES (0, $_SESSION[course_id],
				'$title',
				'$assign_to',
				'$date_due',
				'$date_cutoff',
				'$multi_submit'
				)";

			$result = mysql_query($sql,$db);
			$msg->addFeedback('ASSIGNMENT_ADDED');
		} else { // updating an existing assignment
			$assign_to = 'assign_to';

			$sql = "UPDATE ".TABLE_PREFIX."assignments SET title='$title', assign_to=$assign_to, date_due='$date_due', date_cutoff='$date_cutoff' WHERE assignment_id='$id' AND course_id=$_SESSION[course_id]";

			$result = mysql_query($sql,$db);
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
		header('Location: index_instructor.php');
		exit;
	}
} else { // creating a new assignment
	$title			= '';
	$assign_to		= '0';
	$multi_submit	= '1';
	$has_due_date	= 'false';
	$late_submit	= '0'; // 0 == always, 1 == never, 2 = until (date)

	$dueday		= $today['mday'];
	$duemonth	= $today['mon'];
	$dueyear	= $today['year'];
	$duehour	= '12';
	$dueminute	= '0';

	$cutoffday		= $today['mday'];
	$cutoffmonth	= $today['mon'];
	$cutoffyear		= $today['year'];
	$cutoffhour		= '12';
	$cutoffminute	= '0';
}

// ensure the dates are valid
if ($dueyear == '0'){
	// use today's date as default
	$dueday		= $today['mday'];
	$duemonth	= $today['mon'];
	$dueyear	= $today['year'];
	$duehour	= $today['hours'];
	$dueminute	= $today['minutes'];
	// round the minute to the next highest multiple of 5 
	$dueminute = round($dueminute / '5' ) * '5' + '5';
	if ($dueminute > '55'){ $dueminute = '55'; }
}
if ($cutoffyear == '0'){
	// use today's date as default
	$cutoffday		= $today['mday'];
	$cutoffmonth	= $today['mon'];
	$cutoffyear		= $today['year'];
	$cutoffhour		= $today['hours'];
	$cutoffminute	= $today['minutes'];
	// round the minute to the next highest multiple of 5 
	$cutoffminute = round($cutoffminute / '5' ) * '5' + '5';
	if ($cutoffminute > '55'){ $cutoffminute = '55'; }
}

$onload = 'document.form.title.focus();';

// enable/disable date controls
if ($has_due_date == 'false'){ 
	$onload .= ' disable_dates (true, \'_due\');';
}

if ($late_submit != '2'){
	$onload .= ' disable_dates (true, \'_cutoff\');';
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<div class="input-form">	

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php  echo _AT('title'); ?></label><br/>
		<input type="text" name="title" size="50" id="title" value="<?php echo htmlspecialchars($title); ?>" />
	</div>

	<div class="row">
		<label for="assignto"><?php  echo _AT('assign_to'); ?></label><br/>

		<?php // Are we editing an assignment?
			if ($id != '0'){
				// editing an existing assignment 
				if ($assign_to == '0'){ 
					echo _AT('all_students'); 
				} else { // name of group goes here
					$sql = "SELECT title FROM ".TABLE_PREFIX."groups_types WHERE type_id=$assign_to AND course_id=$_SESSION[course_id]";
					$result = mysql_query($sql, $db);
					$type_row = mysql_fetch_assoc($result);
					echo $type_row['title'];
				}
				?>
			<?php } else { // creating a new assignment
			?>
				<select name="assign_to" size="5" id="assignto">
					<option value="0" <?php if ($assign_to == '0'){ echo 'selected="selected"'; } ?> label="<?php  echo _AT('all_students'); ?>"><?php  echo _AT('all_students'); ?></option>
					<optgroup label="<?php  echo _AT('specific_groups'); ?>">
						<?php
							$sql = "SELECT type_id, title FROM ".TABLE_PREFIX."groups_types WHERE course_id={$_SESSION['course_id']} ORDER BY title";
							$result = mysql_query($sql, $db);
							while ($type_row = mysql_fetch_assoc($result)) {
								echo '<option value="'.$type_row['type_id'].'" ';
								if ($assign_to == $type_row['type_id']) {
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
		<input type="radio" name="has_due_date" value="false" id="noduedate" <?php if ($has_due_date == 'false') { echo 'checked="checked"'; } ?> 
		onfocus="disable_dates (true, '_due');" />
		<label for="noduedate" title="<?php echo _AT('due_date'). ': '. _AT('none');  ?>"><?php echo _AT('none'); ?></label><br />

		<input type="radio" name="has_due_date" value="true" id="hasduedate" <?php if ($has_due_date == 'true'){echo 'checked="checked"'; } ?> 
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
		<input type="radio" name="late_submit" value="0" id="always"  <?php if ($late_submit == '0'){echo 'checked="checked"';} ?> 
		onfocus="disable_dates (true, '_cutoff');" />

		<label for="always" title="<?php echo _AT('accept_late_submissions'). ': '. _AT('always');  ?>"><?php echo _AT('always'); ?></label><br />

		<input type="radio" name="late_submit" value="1" id="never"  <?php if ($late_submit == '1'){echo 'checked="checked"';} ?>
		onfocus="disable_dates (true, '_cutoff');" />

		<label for="never" title="<?php echo _AT('accept_late_submissions'). ': '. _AT('never');  ?>"><?php  echo _AT('never'); ?></label><br />

		<input type="radio" name="late_submit" value="2" id="until"  <?php if ($late_submit == '2'){echo 'checked="checked"';} ?>
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
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<script language="javascript" type="text/javascript">
function disable_dates (state, name) {
	document.form['day' + name].disabled=state;
	document.form['month' + name].disabled=state;
	document.form['year' + name].disabled=state;
	document.form['hour' + name].disabled=state;
	document.form['min' + name].disabled=state;
}
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>