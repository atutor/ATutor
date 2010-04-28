<?php 
if (!defined('AT_INCLUDE_PATH')) { exit; } 
global $_base_path;

$compact_title = str_replace(' ', '', $this->title);
?>

<br />
<script language="javascript" type="text/javascript">
	printSubmenuHeader("<?php echo $this->title; ?>");
</script>
<div class="box" id="menu_<?php echo $compact_title ?>">
	<?php echo $this->dropdown_contents; ?>
</div>

<script language="javascript" type="text/javascript">
if (ATutor.getcookie("m_<?php echo $this->title; ?>") == "0")
{
	jQuery("#menu_<?php echo $compact_title; ?>").hide();
}
else
{
	jQuery("#menu_<?php echo $compact_title; ?>").show();
}
</script>