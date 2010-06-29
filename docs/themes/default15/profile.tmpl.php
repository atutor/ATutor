<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
global $display_name_formats, $moduleFactory;

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
					echo '<a href="'.htmlspecialchars($this->row['website'], ENT_COMPAT, "UTF-8").'">'.AT_print($this->row['website'], 'members.website').'</a>'; 
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

			<?php $mod = $moduleFactory->getModule('_standard/profile_pictures'); 
			if ($mod->isEnabled() === TRUE): ?>
				<dt><?php echo _AT('picture'); ?></dt>
				<dd><?php if (profile_image_exists($this->row['member_id'])): ?>
					<a href="get_profile_img.php?id=<?php echo $this->row['member_id'].SEP.'size=o'; ?>"><?php print_profile_img($this->row['member_id']); ?></a>
					<?php else: ?>
						<?php echo _AT('none'); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
		</dl>
		<div style="clear: both; size: 1em"></div>
	</div>
</div>
