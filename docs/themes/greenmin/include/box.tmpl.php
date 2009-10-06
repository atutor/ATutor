<?php 
if (!defined('AT_INCLUDE_PATH')) { exit; } 
global $_base_path;
?>

<br />
<h4 class="box">
	<input src="<?php echo $_base_path?>images/mswitch_minus.gif" 
	       onclick="elementToggle(this, '<?php echo $this->title; ?>'); return false;" 
	       alt="<?php echo _AT('show'). ' '. $this->title; ?>" 
	       title="<?php echo _AT('show'). ' '. $this->title; ?>"
	       style="float:right" type="image">	
	<?php echo $this->title; ?>
</h4>
<div class="box">
		<?php echo $this->dropdown_contents; ?>
</div>