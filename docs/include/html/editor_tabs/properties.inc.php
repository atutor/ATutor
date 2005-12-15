<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

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
	</div>

	<div class="row">
		<label for="keys"><?php echo _AT('keywords'); ?></label><br />
		<textarea name="keywords" class="formfield" cols="73" rows="2" id="keys"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea>
	</div>

	<div class="row">
		<input type="hidden" name="button_1" value="-1" />
		<?php
			if ($contentManager->getNumSections() > (1 - (bool)(!$cid))) {
				echo '<p>' 
					, _AT('editor_properties_instructions', 
						'<img src="'.$_base_path.'images/after.gif" alt="'._AT('after_topic', '').'" title="'._AT('after_topic', '').'" />', 
						'<img src="'.$_base_path.'images/before.gif" alt="'._AT('before_topic', '').'" title="'._AT('before_topic', '').'" />',
						'<img src="'.$_base_path.'images/child_of.gif" alt="'._AT('child_of', '').'" title="'._AT('child_of', '').'"  />')
					, '</p>';

				echo '<p>' , _AT('editor_properties_insturctions_related') , '</p>';
			}

			$old_pid = $_POST['pid'];
			$old_ordering = $_POST['ordering'];

			if (isset($_POST['move'])) {
				$arr = explode('_', key($_POST['move']), 2);
				$new_pid = $_POST['new_pid'] = $arr[0];
				$new_ordering = $_POST['new_ordering'] = $arr[1];
			} else {
				$new_pid = $_POST['new_pid'];
				$new_ordering = $_POST['new_ordering'];
			}

			echo '<input type="hidden" name="new_ordering" value="'.$new_ordering.'" />';
			echo '<input type="hidden" name="new_pid" value="'.$new_pid.'" />';

			?><br />
			<table border="0">
			<tr>
				<th colspan="2"><?php echo _AT('move'); ?></th>
				<th><?php echo _AT('related_topics'); ?></th>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo _AT('home'); ?></td>
			</tr>
			<?php

				$content_menu = $contentManager->_menu;
				if ($cid == 0) {
					$old_ordering = count($contentManager->getContent($pid))+1;
					$old_pid = 0;

					$current = array('content_id' => -1,
									'ordering' => $old_ordering,
									'title' => $_POST['title']);

					$content_menu[$old_pid][] = $current;
				}

				if (($old_pid != $new_pid) || ($old_ordering != $new_ordering) || ($cid == 0)) {

					$children = $content_menu[$old_pid];

					$children_current = array($children[$old_ordering-1]);
					unset($content_menu[$old_pid][$old_ordering-1]);

					if ($old_pid != $new_pid) {
						$num_children = count($content_menu[$old_pid]);
						$i = 1;
						foreach($content_menu[$old_pid] as $id => $child) {
							$content_menu[$old_pid][$id]['ordering'] = $i;
							$i++;
						}
					}

					$children = $content_menu[$new_pid];
					if (!isset($children)) {
						$children = array();
					}
				
					$children_above = array_slice($children, 0, $new_ordering-1);

					$children_below = array_slice($children, $new_ordering-1);

					$content_menu[$new_pid] = array_merge($children_above, $children_current, $children_below);

					$i=1;
					foreach($content_menu[$new_pid] as $id => $child) {
						$content_menu[$new_pid][$id]['ordering'] = $i;
						$i++;
					}
			
				}

				$contentManager->printMoveMenu($content_menu, 0, 0, '', array());

		?></table>
	</div>