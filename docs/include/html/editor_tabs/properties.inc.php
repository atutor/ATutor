<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
		<?php if ($_POST['day']) { ?>
		<tr>
			<td class="row1"><br /><?php print_popup_help('NOT_RELEASED'); ?><b><?php echo _AT('release_date');  ?>:</b></td>
			<td class="row1"><br /><?php

				$today_day   = $_POST['day'];
				$today_mon   = $_POST['month'];
				$today_year  = $_POST['year'];

				$today_hour  = $_POST['hour'];
				$today_min   = $_POST['min'];

				require(AT_INCLUDE_PATH.'html/release_date.inc.php');
		?>
	</td>
	</tr>
	<?php } else { ?>
	<tr>
	<td class="row1"><br /><?php print_popup_help('NOT_RELEASED'); ?><b><?php echo _AT('release_date');  ?>:</b></td>
	<td class="row1"><br /><?php
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');
			?>
	</td>
	</tr>
	
	<?php } ?>
			<tr><td height="1" class="row2" colspan="2"></td></tr><?php

	$top_level = $contentManager->getContent($row['content_parent_id']);

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1">
		<?php print_popup_help('KEYWORDS'); ?>
		<b><label for="keys"><?php echo _AT('keywords'); ?>:</label></b><br />
		<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keys"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea></p>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" class="row1"><input type="hidden" name="button_1" value="-1" /><?php
		
			if ($contentManager->getNumSections() > (1 - (bool)(!$cid))) {
				echo '<p>' 
					, _AT('editor_properties_instructions', 
						'<small><input type="image" src="'.$_base_path.'images/after.gif" alt="'._AT('after_topic', '').'" title="'._AT('after_topic', '').'" class="button2" style="height:1.5em; width:1.9em;" /></small>', 
						'<small><input type="image" src="'.$_base_path.'images/before.gif" alt="'._AT('before_topic', '').'" title="'._AT('before_topic', '').'" class="button2" style="height:1.5em; width:1.9em;" /></small>',
						'<input type="image" src="'.$_base_path.'images/child_of.gif" class="button2" style="height:1.25em; width:1.7em;" alt="'._AT('child_of', '').'" title="'._AT('child_of', '').'" />')
					, '</p>';

				echo '<p>' , _AT('editor_properties_insturctions_related') , '</p>';
			}

				echo '<br /><table border="0" cellspacing="0" cellpadding="1" class="tableborder" align="center" width="90%">';
				echo '<tr><th colspan="2" width="10%"><small>'._AT('move').'</small></th><th><small>'._AT('related_topics').'</th></tr>';
				echo '<tr><td><small>&nbsp;</small></td><td>&nbsp;</td><td>'._AT('home').'</td></tr>';

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

				$menu = $contentManager->_menu;
				if ($cid == 0) {
					$old_ordering = count($contentManager->getContent($pid))+1;
					$old_pid = 0;

					$current = array('content_id' => -1,
									'ordering' => $old_ordering,
									'title' => $_POST['title']);

					$menu[$old_pid][] = $current;
				}

				if (($old_pid != $new_pid) || ($old_ordering != $new_ordering) || ($cid == 0)) {

					$children = $menu[$old_pid];

					$children_current = array($children[$old_ordering-1]);
					unset($menu[$old_pid][$old_ordering-1]);

					if ($old_pid != $new_pid) {
						$num_children = count($menu[$old_pid]);
						$i = 1;
						foreach($menu[$old_pid] as $id => $child) {
							$menu[$old_pid][$id]['ordering'] = $i;
							$i++;
						}
					}

					$children = $menu[$new_pid];
					if (!isset($children)) {
						$children = array();
					}
				
					$children_above = array_slice($children, 0, $new_ordering-1);

					$children_below = array_slice($children, $new_ordering-1);

					$menu[$new_pid] = array_merge($children_above, $children_current, $children_below);

					$i=1;
					foreach($menu[$new_pid] as $id => $child) {
						$menu[$new_pid][$id]['ordering'] = $i;
						$i++;
					}
			
				}

				$contentManager->printMoveMenu($menu, 0, 0, '', array());

		?></table><br /></td>
		</tr>