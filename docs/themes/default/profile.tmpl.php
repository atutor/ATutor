<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: profile.tmpl.php 3928 2005-03-16 20:21:45Z shozubq $

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<?php echo _AT('login_name'); ?><br />
		<?php echo AT_print($this->row['login'], 'members.login') . ' - '; ?>
		<a href="inbox/send_message.php?id=<?php echo $this->row['member_id']; ?>"><?php echo _AT('send_message'); ?></a>
	</div>

	<div class="row">
		<?php echo _AT('first_name'); ?><br />
		<?php echo AT_print($this->row['first_name'],'members.first_name'); ?>
	</div>

	<div class="row">
		<?php echo _AT('last_name'); ?><br />
		<?php echo AT_print($this->row['last_name'],'members.last_name'); ?>
	</div>

	<div class="row">
		<?php  echo _AT('role_in_course'); ?><br /><?php
			if ($this->row['status']) {
				echo _AT('instructor');
			} else {
				echo _AT('student1');
			} 
		?>
	</div>

	<div class="row">
		<?php echo _AT('web_site'); ?><br />
		<?php
			if ($this->row['website']) {
				echo '<a href="'.$this->row['website'].'">'.AT_print($this->row['website'], 'members.website').'</a>';
			}
		?>
	</div>
</div>
</form>