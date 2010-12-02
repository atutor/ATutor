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
if (!defined('AT_INCLUDE_PATH')) { exit; } 

// print the AccessForAll alternatives tool bar
// see /content.php for details of the alt_infos() array
// images for the toolbar can be customized by adding images of the same name to a theme's images directory
?>
<div id="alternatives_shortcuts">
<?php 
	foreach ($this->alt_infos as $alt_info){
		echo '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$cid.(($_GET['alternative'] == $alt_info['0']) ? '' : htmlentities_utf8(SEP).'alternative='.$alt_info[0]).'">
			<img src="'.AT_BASE_HREF.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[3] : $alt_info[4]).'" alt="'.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[2] : $alt_info[1]).'" title="'.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[2] : $alt_info[1]).'" border="0" class="img1616"/></a>';
	} 
?>
</div>
 
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