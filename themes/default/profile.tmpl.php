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
// $Id: edit.php 3111 2005-01-18 19:32:00Z joel $


global $display_name_formats, $moduleFactory, $db, $_modules;

?>
<div class="input-form">
	<div class="row">
		<p><a href="inbox/send_message.php?id=<?php echo $this->row['member_id']; ?>"><?php echo _AT('send_message'); ?></a></p>
		<dl id="public-profile">
			<?php $mod = $moduleFactory->getModule('_standard/profile_pictures'); 
			if ($mod->isEnabled() === TRUE): ?>
				<dt><?php echo _AT('picture'); ?></dt>
				<dd><?php if (profile_image_exists($this->row['member_id'])): ?>
				<?php
					//run a check to see if any personal album exists, if not, create one.
					$sql = 'SELECT * FROM '.TABLE_PREFIX.'pa_albums WHERE member_id='.$this->row['member_id'].' AND type_id='.AT_PA_TYPE_PERSONAL;
					$result = mysql_query($sql, $db);
					if ($result){
						$row = mysql_fetch_assoc($result);	//album info.
						$aid = $row['id'];
					}
					if (in_array('mods/_standard/photos/index.php', $_modules)){
						$img_link = AT_PA_BASENAME.'albums.php?id='.$aid;
					} else {
						$img_link = 'get_profile_img.php?id='.$this->row['member_id'].SEP.'size=o';
					}
				?>
					<a href="<?php echo $img_link ?>"><?php print_profile_img($this->row['member_id'], 2); ?></a>
					<?php else: ?>
						<?php echo _AT('none'); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>

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
			</dd>
			
			<dt><?php echo _AT('status'); ?></dt>
			<dd><?php echo $this->status; ?></dd>			
		</dl>

	</div>
</div>
