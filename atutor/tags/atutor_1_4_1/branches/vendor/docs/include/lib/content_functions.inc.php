<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

/* @See editor/edit_content.php, editor/add_new_content.php */
function print_select_menu($parent_id, &$_menu, $related_content_id, $depth=0, $path='') {
	global $cid;

	$top_level = $_menu[$parent_id];

	if ( is_array($top_level) ) {
		$counter = 1;
		foreach ($top_level as $x => $content) {
			if ($cid != $content['content_id']) {
				echo '<option value="'.$content['content_id'].'"';
				if ($related_content_id == $content['content_id']) {
					echo ' selected="selected"';
				}
				echo '>';
				echo str_pad('', $depth, '-');
				echo $path.$counter;
				echo ' '.$content['title'];
				echo '</option>';
			}
			print_select_menu($content['content_id'], $_menu, $related_content_id, $depth+1, $path.$counter.'.');
					
			$counter++;
		}
	}
}


function is_alpha($input) {
	return (("a" <= $input && $input <= "z") || ("A" <= $input && $input <= "Z")) ? true : false;
}

/* @See editor/edit_content.php										*/
function print_move_select($parent_id, &$_menu, $my_parent_id, $depth=0, $path='') {
	global $cid, $_template;

	if ( $cid == $parent_id) {
		return;
	}

	$top_level = $_menu[$parent_id];

	if ( is_array($top_level) ) {
		$counter = 1;
		foreach ($top_level as $x => $content) {
			if ($cid != $content['content_id']) {
				echo '<option value="'.$content['content_id'].'">';
				echo str_pad('', $depth, '-');
				echo _AT('child_of').': ';
				echo $path.$counter;
				echo ' '.$content['title'];
				echo '</option>';
			}
			print_move_select($content['content_id'], $_menu, $my_parent_id, $depth+1, $path.$counter.'.');
								
			$counter++;
		}
	}
}


/* @See include/html/menu_menu.inc.php	*/
/* @See tools/sitemap/index.php			*/
function print_menu_collapse($parent_id,
							 &$_menu, 
							 $depth, 
							 $path='',
							 $children,
							 $g,
							 $truncate = true, 
							 $ignore_state = false) {
	
	global $temp_path, $cid, $_my_uri, $this, $_base_path;

	$top_level = $_menu[$parent_id];

	if ( is_array($top_level) ) {
		$counter = 1;
		$num_items = count($top_level);
		foreach ($top_level as $garbage => $content) {

			$link = ' ';
			if (!$ignore_state) {
				$link .= '<a name="menu'.$content['content_id'].'"></a>';
			}

			$on = false;
				
			if ( ($_SESSION['s_cid'] != $content['content_id']) || ($_SESSION['s_cid'] != $cid) ) {

				if (is_array($temp_path)) {
					$this = current($temp_path);

					if (is_array($_menu[$this['content_id']])) {
						/* open this because it's a parent of a content page we're viewing */
						/* this ensures that the path to the page is always expanded */
						$_SESSION['menu'][$this['content_id']] = 1;
					}

					if ($content['content_id'] == $this['content_id']) {
						$this = next($temp_path);
						$link .= '<b>';
						$on = true;
					}
				}
				//$link .= ' <a href="./?cid='.$content['content_id'].SEP.'g='.$g.'" title="'.$content['title'].'">';
				$link .= ' <a href="'.$_base_path.'?cid='.$content['content_id'].SEP.'g='.$g.'" title="';
				//if ($_SESSION['prefs'][PREF_NUMBERING]) {
				      $link .= $path.$counter.' ';
				//}
				$link .= $content['title'].'">';
				/*
				if ($truncate && (strlen($content['title']) > (40-$depth*4))) {
					$content['title'] = '<small><small>'.substr($content['title'], 0, (40-$depth*4)-4) . '...</small></small>';
				} else if ($truncate && (strlen($content['title']) > (35-$depth*4))) {
					$content['title'] = '<small><small>'.$content['title'] . '</small></small>';
				} else if ($truncate && (strlen($content['title']) > (26-$depth*4))) {
					$content['title'] = '<small>'.$content['title'] . '</small>';
					//$content['title'] = '<small><small>'.$content['title'].'</small>';
					//$content['title'] = rtrim(substr($content['title'], 0, (26-$depth*4)-4)) . '...';
				}
				*/
				if ($truncate && (strlen($content['title']) > (26-$depth*4)) ) {
					$content['title'] = rtrim(substr($content['title'], 0, (26-$depth*4)-4)).'...';
				}
				$link .= $content['title'];
				$link .= '</a>';
				if ($on) {
					$link .= '</b>'."\n";
				}
			} else {
				$link .= '<a href="'.$_my_uri.'"><img src="'.$_base_path.'images/clr.gif" alt="'._AT('you_are_here').': '.$content['title'].'" height="1" width="1" border="0" /></a><b title="'.$content['title'].'">';
				if ($truncate && (strlen($content['title']) > (26-$depth*4)) ) {
					$content['title'] = rtrim(substr($content['title'], 0, (26-$depth*4)-4)).'...';
				}
				$link .= $content['title'].'</b>'."\n";
				$on = true;
			}

			if ($ignore_state) {
				$on = true;
			}

			if ( is_array($_menu[$content['content_id']]) ) {
				/* has children */
				for ($i=0; $i<$depth; $i++) {
					if ($children[$i] == 1) {
						echo '<img src="'.$_base_path.'images/tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="menuimage8" />';
					} else {
						echo '<img src="'.$_base_path.'images/tree/tree_space.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
					}
				}

				if (($counter == $num_items) && ($depth > 0)) {
					echo '<img src="'.$_base_path.'images/tree/tree_end.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
					$children[$depth] = 0;
				} else {
					echo '<img src="'.$_base_path.'images/tree/tree_split.gif" alt="" border="0" width="16" height="16"   class="menuimage8" />';
					$children[$depth] = 1;
				}

				if ($_SESSION['s_cid'] == $content['content_id']) {
					if (is_array($_menu[$content['content_id']])) {
						$_SESSION['menu'][$content['content_id']] = 1;
					}
				}

				if ($_SESSION['menu'][$content['content_id']] == 1) {
					if ($on) {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'"  class="menuimage8" />';

					} else {
						echo '<a href="'.$_my_uri.'collapse='.$content['content_id'].'">';
						echo '<img src="'.$_base_path.'images/tree/tree_collapse.gif" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').' '.$content['title'].'"  class="menuimage8" />';
						echo '</a>';
					}
				} else {
					if ($on) {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'" class="menuimage8" />';

					} else {
						echo '<a href="'.$_my_uri.'expand='.$content['content_id'].'">';
						echo '<img src="'.$_base_path.'images/tree/tree_expand.gif" alt="'._AT('expand').'" border="0" width="16" height="16" 	title="'._AT('expand').' '.$content['title'].'"  class="menuimage8" />';
						echo '</a>';
					}
				}

			} else {
				/* doesn't have children */
				if ($counter == $num_items) {
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo '<img src="'.$_base_path.'images/tree/tree_vertline.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
						} else {
							echo '<img src="'.$_base_path.'images/tree/tree_space.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
						}
					}
					if ($parent_id == 0) {
						// special case for the last one:
						echo '<img src="'.$_base_path.'images/tree/tree_split.gif" alt="" border="0"  class="menuimage8" />';
					} else {
						echo '<img src="'.$_base_path.'images/tree/tree_end.gif" alt="" border="0" class="menuimage8" />';
					}
				} else {
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo '<img src="'.$_base_path.'images/tree/tree_vertline.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
						} else {
							echo '<img src="'.$_base_path.'images/tree/tree_space.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
						}
					}
	
					echo '<img src="'.$_base_path.'images/tree/tree_split.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
				}
				echo '<img src="'.$_base_path.'images/tree/tree_horizontal.gif" alt="" border="0" width="16" height="16"  class="menuimage8" />';
			}

			if ($_SESSION['prefs'][PREF_NUMBERING]) {
				echo $path.$counter;
			}
			
			echo $link;
			
			echo '<br />';

			if ( $ignore_state || ($_SESSION['menu'][$content['content_id']] == 1)) {

				$depth ++;
				print_menu_collapse($content['content_id'],
									$_menu, 
									$depth, 
									$path.$counter.'.', 
									$children,
									$g, 
									$truncate, 
									$ignore_state);
				$depth--;

			}
			$counter++;
		}
	}
}

?>