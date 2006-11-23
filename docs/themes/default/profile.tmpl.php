<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
		<h3>
		<?php if($this->row['first_name'] || $this->row['last_name']) {
			echo AT_print($this->row['first_name'] .' '. $this->row['second_name'] .' '. $this->row['last_name'], 'members.first_name');
		} else {
			echo "--";
		}
		?>
			
    (<?php echo AT_print($this->row['login'], 'members.login'); ?>)
		</h3>
	</div>

	<div class="row">
		<p><a href="inbox/send_message.php?id=<?php echo $this->row['member_id']; ?>"><?php echo _AT('send_message'); ?></a></p>
	</div>

	<div class="row">
		<p><strong><?php echo _AT('email'); ?>:</strong>
		<?php if($this->row['private_email']): ?>
			<?php echo _AT('private'); ?>
		<?php else: ?>
			<a href="mailto:<?php echo $this->row['email']; ?>"><?php echo $this->row['email']; ?></a>
		<?php endif; ?></p>
	</div>

	<div class="row">
		<p><strong><?php echo _AT('web_site'); ?>:</strong>
		<?php if ($this->row['website']) { 
			echo '<a href="'.$this->row['website'].'">'.AT_print($this->row['website'], 'members.website').'</a>'; 
		} else {
			echo "--";
		}
		?></p>
	</div>

	<div class="row">
		<p><strong><?php echo _AT('phone'); ?>:</strong>
		<?php if ($this->row['phone']) { 
			echo $this->row['phone'];
		} else {
			echo "--";
		}
		?></p>
	</div>

	<div class="row">
		<p><strong><?php echo _AT('status'); ?>:</strong>
		<?php echo $this->status; ?>
		<?php 
		if ($this->privs) { 
			$priv_string = "(";
			foreach ($this->privs as $priv) {
				$priv_string .= _AT($priv).', ';
			}				
			$priv_string = substr($priv_string, 0, -2);
			echo $priv_string .')';
		}
		?></p>
	</div>
</div>
</form>