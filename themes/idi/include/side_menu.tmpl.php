<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

<?php if (($this->course_id > 0) && $this->side_menu): ?>
	<?php foreach ($this->side_menu as $dropdown_file): ?>
		<?php if (file_exists($dropdown_file)) { require($dropdown_file); } ?>
	<?php endforeach; ?>
<div style="position:absolute; bottom:0px;">&nbsp;</div>
<?php endif; ?>
