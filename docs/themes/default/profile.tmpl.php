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
global $display_name_formats, $_config;

?>
<div class="input-form">
	<div class="row">
		<p><a href="inbox/send_message.php?id=<?php echo $this->row['member_id']; ?>"><?php echo _AT('send_message'); ?></a></p>

		<dl id="public-profile">
			<dt><?php echo _AT('email'); ?></dt>
			<dd>
				<?php if($this->row['private_email']): ?>
					<?php echo _AT('private'); ?>
				<?php else: ?>
					<a href="mailto:<?php echo $this->row['email']; ?>"><?php echo $this->row['email']; ?></a>
				<?php endif; ?>
			</dd>
		
			<dt><?php echo _AT('web_site'); ?></dt>
			<dd>
				<?php if ($this->row['website']) { 
					echo '<a href="'.$this->row['website'].'">'.AT_print($this->row['website'], 'members.website').'</a>'; 
				} else {
					echo '--';
				} ?>
			</dd>

			<dt><?php echo _AT('phone'); ?></dt>
			<dd>
				<?php if ($this->row['phone']) { 
					echo $this->row['phone'];
				} else {
					echo '--';
				}
				?>
			</dt>

			<dt><?php echo _AT('status'); ?></dt>
			<dd><?php echo $this->status; ?></dd>
		</dl>
		<div style="clear: both; size: 1em"></div>
	</div>
</div>
