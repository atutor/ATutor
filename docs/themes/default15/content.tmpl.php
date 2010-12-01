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
if (!defined('AT_INCLUDE_PATH')) { exit; } ?>
<?php
// print the AccessForAll alternatives tool bar
// see /content.php for details of the alt_parts() array
// images for the toolbar can be customized by adding images of the same name to a theme's images directory
echo '<div id="alternatives_shortcuts">';
print_alternative_tools($this->cid,$this->theme_image_path,$this->alt_parts,$this->has_sign_lang_alternative,$this->has_visual_alternative,$this->has_audio_alternative,$this->has_text_alternative);
echo '</div>';
?>
<?php if ($this->shortcuts): ?>
<fieldset id="shortcuts"><legend><?php echo _AT('shortcuts'); ?></legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?>

<?php if ($_SESSION["prefs"]["PREF_SHOW_CONTENTS"] && $this->content_table <> "") { ?>
<div id="content-table">
	<?php echo $this->content_table; ?>
</div>
<?php } ?>

<div id="content-text">
	<?php echo $this->body; ?>
</div>

<div id="content-info">
	<?php echo $this->content_info; ?>
</div>