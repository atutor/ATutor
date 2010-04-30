<?php 
if (!defined('AT_INCLUDE_PATH')) { exit; } 
global $_base_path;

$compact_title = str_replace(' ', '', $this->title);
?>

<br />
<h4 class="box"><span><?php echo $this->title ?></span><input class="fl-force-right" src="" alt="" title="" type="image" /></h4>
<div class="box" id="menu_<?php echo $compact_title ?>">
	<?php echo $this->dropdown_contents; ?>
</div>
