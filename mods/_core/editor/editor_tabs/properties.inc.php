<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: properties.inc.php 8794 2009-09-16 16:06:54Z cindy $

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<div class="row">
		<?php echo _AT('release_date');  ?><br />
		<?php if ($_POST['day']) { ?>
			<?php
				$today_day   = $_POST['day'];
				$today_mon   = $_POST['month'];
				$today_year  = $_POST['year'];

				$today_hour  = $_POST['hour'];
				$today_min   = $_POST['min'];		
		}?>
		<?php require(AT_INCLUDE_PATH.'html/release_date.inc.php');	?>
		<?php echo _AT('applies_to_all_sub_pages'); ?>
	</div>

	<div class="row">
		<label for="keys"><?php echo _AT('keywords'); ?></label><br />
		<textarea name="keywords" class="formfield" cols="73" rows="2" id="keys"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea>
	</div>

	<div class="row">
		<input type="hidden" name="button_1" value="-1" />
		<?php
			if ($contentManager->getNumSections() > (1 - (bool)(!$cid))) {
				echo '<p>' , _AT('editor_properties_insturctions_related') , '</p>';
			}
			?><br />
			<table border="0">
			<tr>
				<th><?php echo _AT('related_topics'); ?></th>
			</tr>
			<tr>
				<td><?php echo _AT('home'); ?></td>
			</tr>
			<?php $contentManager->printActionMenu($contentManager->_menu, 0, 0, '', array(), "related_content"); ?>
		</table>
	</div>