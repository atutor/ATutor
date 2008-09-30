<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: directory.tmpl.php 3111 2005-01-18 19:32:00Z joel $

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">

<div class="input-form">
	<div class="row">
		<?php echo _AT('online_status'); ?><br />
		<input type="radio" name="online_status" id="s1" value="1" <?php echo $this->on; ?>  /><label for="s1"><?php echo _AT('user_online');  ?></label>
		<input type="radio" name="online_status" id="s0" value="0" <?php echo $this->off; ?> /><label for="s0"><?php echo _AT('user_offline'); ?></label>
		<input type="radio" name="online_status" id="s2" value="2" <?php echo $this->all; ?> /><label for="s2"><?php echo _AT('all');          ?></label>
	</div>

		<div class="row">

			<label for="groups"><?php echo _AT('groups'); ?></label><br />
			<select name="group" id="groups">
				<option value="0" id="g0" ><?php echo _AT('entire_course'); ?></option>
			<?php while ($row = mysql_fetch_assoc($this->result_groups)): ?>
				<option value="<?php echo $row['group_id']; ?>" id="g<?php echo $row['group_id']; ?>" <?php if ($group == $row['group_id']) { echo 'selected="selected"'; } ?> ><?php echo $row['type_title'] . ': ' . $row['title']; ?></option>
			<?php endwhile; ?>
			</select>

		</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('filter'); ?>" />
		<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
	</div>
</div>
</form>

<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('full_name'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('online_status'); ?></th>
</tr>
</thead>
<tbody>
<?php
if ($this->final) {
	foreach ($this->final as $user_id=>$attrs) {
		echo '<tr onmousedown="document.location=\''.$this->base_href.'profile.php?id='.$user_id.'\'">';
		$type = 'class="user"';
		if ($system_courses[$_SESSION['course_id']]['member_id'] == $user_id) {
			$type = 'class="user instructor" title="'._AT('instructor').'"';
		}
		echo '<td><a href="profile.php?id='.$user_id.'" '.$type.'>'.AT_print($attrs['login'], 'members.login') . '</a></td>';

		//echo '<td>'.AT_print($attrs['first_name'] .' '. $attrs['second_name'] .' '. $attrs['last_name'],'members.first_name').'</td>';
		echo '<td>'.AT_print(get_display_name($user_id), 'members.full_name').'</td>';	
		
		if ($attrs['privileges'] != 0) {
			echo '<td>'._AT('assistant').'</td>';
		} else if ($attrs['approved'] == 'a') {
			/* if alumni display alumni */
			echo '<td>'._AT('alumni').'</td>';
		} else if ($attrs['approved'] == 'y') {
			if ($user_id == $system_courses[$_SESSION['course_id']]['member_id']) {
				echo '<td>'._AT('instructor').'</td>';
			} else {
				echo '<td>'._AT('enrolled').'</td>';
			}
		} else {
			echo '<td></td>';
		}
		
		if ($attrs['online'] == TRUE) {
			echo '<td><strong>'._AT('user_online').'</strong></td>';
		} else {
			echo '<td>'._AT('user_offline').'</td>';
		}

		echo '</tr>';
	}	
} else {
	echo '<tr><td colspan="3">' . _AT('none_found') . '</td></tr>';
}
?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
