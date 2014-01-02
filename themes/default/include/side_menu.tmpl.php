<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>
<?php if (($this->course_id > 0) && $this->side_menu): ?>
	<?php foreach ($this->side_menu as $dropdown_file): ?>
		<?php if (file_exists($dropdown_file)) { require($dropdown_file); } ?>
	<?php endforeach; ?>
<span id="side_bar_on" title="<?php echo _AT('side_menu_opened'); ?>" aria-live="polite"></span>
<span id="side_bar_off" title="<?php echo _AT('side_menu_closed'); ?>"  aria-live="polite"></span>
<div style="position:absolute; bottom:0px;">&nbsp;</div>
<?php endif; ?>
