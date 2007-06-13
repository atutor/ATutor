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

class ContentManager
{
	/* db handler	*/
	var $db;

	/*	array		*/
	var $_menu;

	/*	array		*/
	var $_menu_info;

	/* int			*/
	var $course_id;

	// private
	var $num_sections;

	// private
	var $max_depth;

	// private
	var $content_length;

	/* constructor	*/
	function ContentManager(&$db, $course_id) {
		$this->db = $db;
		$this->course_id = $course_id;
	}

	function initContent( ) {
		if (!($this->course_id > 0)) {
			return;
		}
		$sql = "SELECT content_id, content_parent_id, ordering, title, UNIX_TIMESTAMP(release_date) AS u_release_date FROM ".TABLE_PREFIX."content WHERE course_id=$this->course_id ORDER BY content_parent_id, ordering";
		$result = mysql_query($sql, $this->db);

		/* x could be the ordering or even the content_id	*/
		/* don't really need the ordering anyway.			*/
		/* $_menu[content_parent_id][x] = array('content_id', 'ordering', 'title') */
		$_menu = array();

		/* number of content sections */
		$num_sections = 0;

		$max_depth = array();
		$_menu_info = array();

		while ($row = mysql_fetch_assoc($result)) {
			$num_sections++;
			$_menu[$row['content_parent_id']][] = array('content_id'=> $row['content_id'],
														'ordering'	=> $row['ordering'], 
														'title'		=> htmlspecialchars($row['title']));

			$_menu_info[$row['content_id']] = array('content_parent_id' => $row['content_parent_id'],
													'title'				=> htmlspecialchars($row['title']),
													'ordering'			=> $row['ordering'],
													'u_release_date'      => $row['u_release_date']);

			if ($row['content_parent_id'] == 0){
				$max_depth[$row['content_id']] = 1;
			} else {
				$max_depth[$row['content_id']] = $max_depth[$row['content_parent_id']]+1;
			}
		}

		$this->_menu = $_menu;

		$this->_menu_info =  $_menu_info;

		$this->num_sections = $num_sections;

		if (count($max_depth) > 1) {
			$this->max_depth = max($max_depth);
		} else {
			$this->max_depth = 0;
		}

		$this->content_length = count($_menu[0]);
	}


	function getContent($parent_id=-1, $length=-1) {
		if ($parent_id == -1) {
			$my_menu_copy = $this->_menu;
			if ($length != -1) {
				$my_menu_copy[0] = array_slice($my_menu_copy[0], 0, $length);
			}
			return $my_menu_copy;
		}
		return $this->_menu[$parent_id];
	}


	function &getContentPath($content_id) {
		$path = array();

		$path[] = array('content_id' => $content_id, 'title' => $this->_menu_info[$content_id]['title']);

		$this->getContentPathRecursive($content_id, $path);

		$path = array_reverse($path);
		return $path;
	}


	function getContentPathRecursive($content_id, &$path) {
		$parent_id = $this->_menu_info[$content_id]['content_parent_id'];

		if ($parent_id > 0) {
			$path[] = array('content_id' => $parent_id, 'title' => $this->_menu_info[$parent_id]['title']);
			$this->getContentPathRecursive($parent_id, $path);
		}
	}


	function addContent($course_id, $content_parent_id, $ordering, $title, $text, $keywords, $related, $formatting, $release_date) {
		
		if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['course_id'] != -1)) {
			return false;
		}

		// shift the new neighbouring content down
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 WHERE ordering>=$ordering AND content_parent_id=$content_parent_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);

		/* main topics all have minor_num = 0 */
		$sql = "INSERT INTO ".TABLE_PREFIX."content VALUES (NULL,$course_id, $content_parent_id, $ordering, NOW(), 0, $formatting, '$release_date', '$keywords', '', '$title','$text')";

		$err = mysql_query($sql, $this->db);

		/* insert the related content */
		$sql = "SELECT LAST_INSERT_ID() AS insert_id";
		$result = mysql_query($sql, $this->db);
		$row = mysql_fetch_assoc($result);
		$cid = $row['insert_id'];

		$sql = '';
		if (is_array($related)) {
			foreach ($related as $x => $related_content_id) {
				$related_content_id = intval($related_content_id);

				if ($related_content_id != 0) {
					if ($sql != '') {
						$sql .= ', ';
					}
					$sql .= '('.$cid.', '.$related_content_id.')';
					$sql .= ', ('.$related_content_id.', '.$cid.')';
				}
			}

			if ($sql != '') {
				$sql	= 'INSERT INTO '.TABLE_PREFIX.'related_content VALUES '.$sql;
				$result	= mysql_query($sql, $this->db);
			}
		}

		return $cid;
	}


	function editContent($content_id, $title, $text, $keywords, $new_content_ordering, $related, $formatting, $new_content_parent_id, $release_date) {
		if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
			return FALSE;
		}

		/* first get the content to make sure it exists	*/
		$sql	= "SELECT ordering, content_parent_id FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $this->db);
		if (!($row = mysql_fetch_assoc($result)) ) {
			return FALSE;
		}
		$old_ordering		= $row['ordering'];
		$content_parent_id	= $row['content_parent_id'];
		if (($content_parent_id != $new_content_parent_id) || ($old_ordering != $new_content_ordering)) {
			// remove the gap left by the moved content
			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering-1 WHERE ordering>=$old_ordering AND content_parent_id=$content_parent_id AND content_id<>$content_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			// shift the new neighbouring content down
			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 WHERE ordering>=$new_content_ordering AND content_parent_id=$new_content_parent_id AND content_id<>$content_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);
		}

		/* update the title, text of the newly moved (or not) content */
		$sql	= "UPDATE ".TABLE_PREFIX."content SET title='$title', text='$text', keywords='$keywords', formatting=$formatting, content_parent_id=$new_content_parent_id, ordering=$new_content_ordering, revision=revision+1, last_modified=NOW(), release_date='$release_date' WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $this->db);

		/* update the related content */
		$result	= mysql_query("DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id", $this->db);
		$sql = '';
		if (is_array($related)) {
			foreach ($related as $x => $related_content_id) {
				$related_content_id = intval($related_content_id);

				if ($related_content_id != 0) {
					if ($sql != '') {
						$sql .= ', ';
					}
					$sql .= '('.$content_id.', '.$related_content_id.')';
					$sql .= ', ('.$related_content_id.', '.$content_id.')';
				}
			}

			if ($sql != '') {
				/* delete the old related content */
				$result	= mysql_query("DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id", $this->db);

				/* insert the new, and the old related content again */
				$sql	= 'INSERT INTO '.TABLE_PREFIX.'related_content VALUES '.$sql;
				$result	= mysql_query($sql, $this->db);
			}
		}
	}


	function deleteContent($content_id) {
		if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
			return false;
		}

		/* check if exists */
		$sql	= "SELECT ordering, content_parent_id FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $this->db);
		if (!($row = @mysql_fetch_assoc($result)) ) {
			return false;
		}
		$ordering			= $row['ordering'];
		$content_parent_id	= $row['content_parent_id'];

		/* check if this content has sub content	*/
		$children = $this->_menu[$content_id];

		if (is_array($children) && (count($children)>0) ) {
			/* delete its children recursively first*/
			foreach ($children as $x => $info) {
				$this->deleteContentRecursive($info['content_id']);
			}
		}

		/* delete this content page					*/
		$sql	= "DELETE FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);

		/* delete this content from member tracking page	*/
		$sql	= "DELETE FROM ".TABLE_PREFIX."member_track WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
		$result = mysql_query($sql, $this->db);

		/* re-order the rest of the content */
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering-1 WHERE ordering>=$ordering AND content_parent_id=$content_parent_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);
		/* end moving block */

		/* remove the "resume" to this page, b/c it was deleted */
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET last_cid=0 WHERE course_id=$_SESSION[course_id] AND last_cid=$content_id";
		$result = mysql_query($sql, $this->db);

		return true;
	}


	/* private. call from deleteContent only. */
	function deleteContentRecursive($content_id) {
		/* check if this content has sub content	*/
		$children = $this->_menu[$content_id];

		if (is_array($children) && (count($children)>0) ) {
			/* delete its children recursively first*/
			foreach ($children as $x => $info) {
				$this->deleteContent($info['content_id']);
			}
		}

		/* delete this content page					*/
		$sql	= "DELETE FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);

		/* delete this content from member tracking page	*/
		$sql	= "DELETE FROM ".TABLE_PREFIX."member_track WHERE content_id=$content_id";
		$result = mysql_query($sql, $this->db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
		$result = mysql_query($sql, $this->db);
	}

	function & getContentPage($content_id) {
		$sql	= "SELECT *, DATE_FORMAT(release_date, '%Y-%m-%d %H:%i:00') AS release_date, release_date+0 AS r_date, NOW()+0 AS n_date FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);

		return $result;
	}
	
	/* @See editor/edit_content.php include/html/dropdowns/related_topics.inc.php include/lib/editor_tabs_functions.inc.php */
	function getRelatedContent($content_id, $all=false) {
		if ($content_id == 0) {
			return;
		}
		if ($content_id == '') {
			return;
		}
		$related_content = array();

		if ($all) {
			$sql = "SELECT * FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
		} else {
			$sql = "SELECT * FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id";
		}
		$result = mysql_query($sql, $this->db);

		while ($row = mysql_fetch_assoc($result)) {
			if ($row['related_content_id'] != $content_id) {
				$related_content[] = $row['related_content_id'];
			} else {
				$related_content[] = $row['content_id'];
			}
		}

		return $related_content;
	}


	function & cleanOutput($value) {
		return stripslashes(htmlspecialchars($value));
	}


	/* @See include/html/editor_tabs/properties.inc.php */
	/* Access: Public */
	function getNumSections() {
		return $this->num_sections;
	}

	/* Access: Public */
	function getMaxDepth() {
		return $this->max_depth;
	}

	/* Access: Public */
	function getContentLength() {
		return $this->content_length;
	}

	/* @See include/html/dropdowns/local_menu.inc.php */
	function getLocationPositions($parent_id, $content_id) {
		$siblings = $this->getContent($parent_id);
		for ($i=0;$i<count($siblings); $i++){
			if ($siblings[$i]['content_id'] == $content_id) {
				return $i;
			}
		}
		return 0;	
	}

	/* Access: Private */
	function getNumbering($content_id) {
		$path = $this->getContentPath($content_id);
		$parent = 0;
		$numbering = '';
		foreach ($path as $page) {
			$num = $this->getLocationPositions($parent, $page['content_id']) +1;
			$parent = $page['content_id'];
			$numbering .= $num.'.';
		}
		$numbering = substr($numbering, 0, -1);

		return $numbering;
	}

	/* Access: Private */
	function getPreviousContent($content_id, $order=0) {
		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];

		if (isset($this->_menu[$myParent][$myOrder-2]) && ($this->_menu[$myParent][$myOrder-2] != '') && ($order==0)) {
			// has sibling: checking if sibling has children
			
			$mySibling = $this->_menu[$myParent][$myOrder-2];
			
			if ( isset($this->_menu[$mySibling['content_id']]) && is_array($this->_menu[$mySibling['content_id']]) && ($order==0) ) {
				$num_children = count($this->_menu[$mySibling['content_id']]);

				// sibling has $num_children children
				return($this->getPreviousContent($this->_menu[$mySibling['content_id']][$num_children-1]['content_id'], 1));

			} else {
				// sibling has no children. return it
				return($this->_menu[$myParent][$myOrder-2]);
			}

		} else {
			if ($myParent == 0) {
				/* we're at the top */
				return '';
			}

			/* No more siblings */
			if ($order == 0) {
				return(array('content_id'	=> $myParent,
					 		 'ordering'		=> $this->_menu_info[$myParent]['ordering'],
							 'title'		=> $this->_menu_info[$myParent]['title']));
			} else {
				if ( isset($this->_menu[$content_id]) && is_array($this->_menu[$content_id]) ) {
					$num_children = count($this->_menu[$content_id]);
					return ($this->getPreviousContent($this->_menu[$content_id][$num_children-1]['content_id'], 1));

				} else {
					/* no children */
					return(array('content_id'	=> $content_id,
					 			 'ordering'		=> $this->_menu_info[$content_id]['ordering'],
								 'title'		=> $this->_menu_info[$content_id]['title']));
				}
			}
		}
	}

	/* Access: Private */
	function getNextContent($content_id, $order=0) {
		if (!$content_id) { return; }

		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];
		/* if this content has children, then take the first one. */
		if ( isset($this->_menu[$content_id]) && is_array($this->_menu[$content_id]) && ($order==0) ) {
			/* has children */
			return($this->_menu[$content_id][0]);
		} else {
			/* no children */
			if (isset($this->_menu[$myParent][$myOrder]) && $this->_menu[$myParent][$myOrder] != '') {
				/* Has sibling */
				return($this->_menu[$myParent][$myOrder]);
			} else {
				/* No more siblings */
				if ($myParent != 0) {
					return($this->getNextContent($myParent, 1));
				}
			}
		}
	}

	/* @See include/header.inc.php */
	function generateSequenceCrumbs($cid) {
		global $_base_path;

		$sequence_links = array();

		$first = $this->getNextContent(0); // get first
		if ($_SESSION['prefs']['PREF_NUMBERING'] && $first) {
			$first['title'] = $this->getNumbering($first['content_id']).' '.$first['title'];
		}
		if ($first) {
			$first['url'] = $_base_path.'content.php?cid='.$first['content_id'];
			$sequence_links['first'] = $first;
		}

		if (!$cid && $_SESSION['s_cid']) {
			$resume['title'] = $this->_menu_info[$_SESSION['s_cid']]['title'];

			if ($_SESSION['prefs']['PREF_NUMBERING']) {
				$resume['title'] = $this->getNumbering($_SESSION['s_cid']).' ' . $resume['title'];
			}

			$resume['url'] = $_base_path.'content.php?cid='.$_SESSION['s_cid'];

			$sequence_links['resume'] = $resume;
		} else {
			if ($cid) {
				$previous = $this->getPreviousContent($cid);
			}
			$next = $this->getNextContent($cid ? $cid : 0);

			if ($_SESSION['prefs']['PREF_NUMBERING']) {
				$previous['title'] = $this->getNumbering($previous['content_id']).' '.$previous['title'];
				$next['title'] = $this->getNumbering($next['content_id']).' '.$next['title'];
			}

			$next['url'] = $_base_path.'content.php?cid='.$next['content_id'];
			if (isset($previous['content_id'])) {
				$previous['url'] = $_base_path.'content.php?cid='.$previous['content_id'];
			}
			
			if (isset($previous['content_id'])) {
				$sequence_links['previous'] = $previous;
			} else if ($cid) {
				$previous['url']   = $_base_path . 'index.php';
				$previous['title'] = _AT('home');
				$sequence_links['previous'] = $previous;
			}
			if (!empty($next['content_id'])) {
				$sequence_links['next'] = $next;
			}
		}

		return $sequence_links;
	}

	/* @See include/html/dropdowns/menu_menu.inc.php */
	function printMainMenu( ) {
		$parent_id    = 0;
		$depth        = 0;
		$path         = '';
		$children     = array();
		$truncate     = true;
		$ignore_state = false;

		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See tools/sitemap/index.php */
	function printSiteMapMenu() {
		$parent_id    = 0;
		$depth        = 1;
		$path         = '';
		$children     = array();
		$truncate     = false;
		$ignore_state = true;

		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See index.php */
	function printTOCMenu($cid, $top_num) {
		$parent_id    = $cid;
		$depth        = 1;
		$path         = $top_num.'.';
		$children     = array();
		$truncate     = false;
		$ignore_state = false;

		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See index.php include/html/dropdowns/local_menu.inc.php */
	function printSubMenu($cid, $top_num) {
		$parent_id    = $cid;
		$depth        = 1;
		$path         = $top_num.'.';
		$children     = array();
		$truncate     = true;
		$ignore_state = false;
	
		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See include/html/menu_menu.inc.php	*/
	/* Access: PRIVATE */
	function printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state) {
		
		global $cid, $_my_uri, $_base_path, $rtl;
		static $temp_path;

		if (!isset($temp_path)) {
			if ($cid) {
				$temp_path	= $this->getContentPath($cid);
			} else {
				$temp_path	= $this->getContentPath($_SESSION['s_cid']);
			}
		}

		$highlighted = array();
		if (is_array($temp_path)) {
			foreach ($temp_path as $temp_path_item) {
				$_SESSION['menu'][$temp_path_item['content_id']] = 1;
				$highlighted[$temp_path_item['content_id']] = true;
			}
		}

		if ($this->start) {
			reset($temp_path);
			$this->start = false;
		}

		if ( isset($this->_menu[$parent_id]) && is_array($this->_menu[$parent_id]) ) {
			$top_level = $this->_menu[$parent_id];
			$counter = 1;
			$num_items = count($top_level);
			foreach ($top_level as $garbage => $content) {
				$link = '';
				if (!$ignore_state) {
					$link .= '<a name="menu'.$content['content_id'].'"></a>';
				}

				$on = false;

				if ( ($_SESSION['s_cid'] != $content['content_id']) || ($_SESSION['s_cid'] != $cid) ) {
					if (isset($highlighted[$content['content_id']])) {
						$link .= '<strong>';
						$on = true;
					}

					$link .= ' <a href="'.$_base_path.'content.php?cid='.$content['content_id'].'" title="';
					if ($_SESSION['prefs']['PREF_NUMBERING']) {
						$link .= $path.$counter.' ';
					}

					$link .= $content['title'].'">';

					if ($truncate && (strlen($content['title']) > (28-$depth*4)) ) {
						$content['title'] = rtrim(substr($content['title'], 0, (28-$depth*4)-4)).'...';
					}
					$link .= $content['title'];
					$link .= '</a>';
					if ($on) {
						$link .= '</strong>';
					}
				} else {
					$link .= '<a href="'.$_my_uri.'"><img src="'.$_base_path.'images/clr.gif" alt="'._AT('you_are_here').': '.$content['title'].'" height="1" width="1" border="0" /></a><strong title="'.$content['title'].'">';
					if ($truncate && (strlen($content['title']) > (26-$depth*4)) ) {
						$content['title'] = rtrim(substr($content['title'], 0, (26-$depth*4)-4)).'...';
					}
					$link .= trim($content['title']).'</strong>';
					$on = true;
				}

				if ($ignore_state) {
					$on = true;
				}

				if ( isset($this->_menu[$content['content_id']]) && is_array($this->_menu[$content['content_id']]) ) {
					/* has children */
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						} else {
							echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						}
					}

					if (($counter == $num_items) && ($depth > 0)) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						$children[$depth] = 0;
					} else if ($counter == $num_items) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						$children[$depth] = 0;
					} else {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						$children[$depth] = 1;
					}

					if ($_SESSION['s_cid'] == $content['content_id']) {
						if (is_array($this->_menu[$content['content_id']])) {
							$_SESSION['menu'][$content['content_id']] = 1;
						}
					}

					if (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1) {
						if ($on) {
							echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'" class="img-size-tree" />';

						} else {
							echo '<a href="'.$_my_uri.'collapse='.$content['content_id'].'">';
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_collapse.gif" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').' '.$content['title'].'" class="img-size-tree" />';
							echo '</a>';
						}
					} else {
						if ($on) {
							echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'" class="img-size-tree" />';

						} else {
							echo '<a href="'.$_my_uri.'expand='.$content['content_id'].'">';
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_expand.gif" alt="'._AT('expand').'" border="0" width="16" height="16" 	title="'._AT('expand').' '.$content['title'].'" class="img-size-tree" />';
							echo '</a>';
						}
					}

				} else {
					/* doesn't have children */
					if ($counter == $num_items) {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							} else {
								echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" class="img-size-tree" />';
					} else {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							} else {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
					}
					echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_horizontal.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
				}

				if ($_SESSION['prefs']['PREF_NUMBERING']) {
					echo $path.$counter;
				}
				
				echo $link;
				
				echo '<br />';

				if ( $ignore_state || (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1)) {

					$depth ++;

					$this->printMenu($content['content_id'],
										$depth, 
										$path.$counter.'.', 
										$children,
										$truncate, 
										$ignore_state);

										
					$depth--;

				}
				$counter++;
			}
		}
	}

	/* @See include/html/editor_tabs/properties.inc.php */
	function printMoveMenu($menu, $parent_id, $depth, $path, $children) {
		
		global $cid, $_my_uri, $_base_path, $rtl;

		static $end, $ignore;

		$top_level = $menu[$parent_id];

		if ( is_array($top_level) ) {
			$counter = 1;
			$num_items = count($top_level);
			foreach ($top_level as $garbage => $content) {

				$link = ' ';

				echo '<tr>';

				if (($parent_id == $_POST['new_pid']) && ($content['ordering'] < $_POST['new_ordering'])) {
					$text = _AT('before_topic', $content['title']);
					$img = 'before.gif';
				} else if ($parent_id != $_POST['new_pid']) {
					$text = _AT('before_topic', $content['title']);
					$img = 'before.gif';
				} else {
					$text = _AT('after_topic', $content['title']);
					$img = 'after.gif';
				}
				if ($ignore && ($_POST['cid'] > 0)) {
					$buttons = '<td><small>&nbsp;</small></td><td><small>&nbsp;</small></td><td>';
				} else if ($_POST['new_pid'] == $content['content_id']) {
					$buttons = '<td align="center"><small><input type="image" name="move['.$parent_id.'_'.$content['ordering'].']" src="'.$_base_path.'images/'.$img.'" alt="'.$text.'" title="'.$text.'" style="height:1.5em; width:1.9em;" /></small></td><td><small>&nbsp;</small></td><td>';
				} else {
					$buttons = '<td align="center"><small><input type="image" name="move['.$parent_id.'_'.$content['ordering'].']" src="'.$_base_path.'images/'.$img.'" title="'.$text.'" style="height:1.5em; width:1.9em;" /></small></td><td><input type="image" name="move['.$content['content_id'].'_1]" src="'.$_base_path.'images/child_of.gif" style="height:1.25em; width:1.7em;" alt="'._AT('child_of', $content['title']).'" title="'._AT('child_of', $content['title']).'" /></td><td>';
				}

				if (( $content['content_id'] == $cid ) || ($content['content_id'] == -1)) {
					$ignore = true;
					$link .= '<strong>'.trim($_POST['title']).' '._AT('current_location').'</strong>';
					$buttons = '<td colspan="2"><small>&nbsp;</small></td><td>';
				} else {
					$link .= '<input type="checkbox" name="related[]" value="'.$content['content_id'].'" id="r'.$content['content_id'].'" ';
					if (isset($_POST['related']) && in_array($content['content_id'], $_POST['related'])) {
						$link .= ' checked="checked"';
					}
					$link .= ' /><label for="r'.$content['content_id'].'">'.$content['title'].'</label>';
				}

				if ( is_array($menu[$content['content_id']]) && !empty($menu[$content['content_id']]) ) {
					/* has children */

					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo $buttons;
							unset($buttons);
							if ($end && ($i==0)) {
								echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							} else {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" />';
							}
						} else {
							echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						}
					}

					if (($counter == $num_items) && ($depth > 0)) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" />';
						$children[$depth] = 0;
					} else {
						echo $buttons;
						if (($num_items == $counter) && ($parent_id == 0)) {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" />';
							$end = true;
						} else {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" />';
						}
						$children[$depth] = 1;
					}

					if ($_SESSION['s_cid'] == $content['content_id']) {
						if (is_array($menu[$content['content_id']])) {
							$_SESSION['menu'][$content['content_id']] = 1;
						}
					}

					if ($_SESSION['menu'][$content['content_id']] == 1) {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'" />';

					} else {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'" />';
					}

				} else {
					/* doesn't have children */
					if ($counter == $num_items) {
						if ($depth) {
							echo $buttons;
							for ($i=0; $i<$depth; $i++) {
								if ($children[$i] == 1) {
									if ($end && ($i == 0)) {
										echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
									} else {
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" />';
									}
								} else {
									echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
								}
							}
						} else {
							echo $buttons;
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" />';
					} else {
						if ($depth) {
							echo $buttons;
							$print = false;
							for ($i=0; $i<$depth; $i++) {
								if ($children[$i] == 1) {
									if ($end && !$print) {
										$print = true;
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" />';
									} else {
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" />';
									}
								} else {
									echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" />';
								}
							}
							$print = false;
						} else {
							echo $buttons;
						}
		
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" />';
					}
					echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_horizontal.gif" alt="" border="0" width="16" height="16" />';
				}

				echo '<small> '.$path.$counter;
				
				echo $link;
				
				echo '</small></td></tr>';

				$this->printMoveMenu($menu,
									$content['content_id'],
									++$depth, 
									$path.$counter.'.', 
									$children);
				$depth--;

				$counter++;

				if ( $content['content_id'] == $cid ) {
					$ignore =false;
				}
			}
		}
	}


	/* returns the timestamp of release if this page has not yet been released, or is under a page that has not been released, true otherwise */
	/* finds the max(timestamp) of all parents and returns that, true if less than now */
	/* Access: public */
	function isReleased($cid) {
		if ($this->_menu_info[$cid]['content_parent_id'] == 0) {
			// this $cid has no parent, so we check its release date directly
			if ($this->_menu_info[$cid]['u_release_date'] <= time()) {	
				// yup! it's released
				return true;
			} else {
				// nope! not released
				return $this->_menu_info[$cid]['u_release_date'];
			}
		}
		// this is a sub page, need to check ALL its parents
		$parent = $this->isReleased($this->_menu_info[$cid]['content_parent_id']); // recursion

		if ($parent !== TRUE && $parent > $this->_menu_info[$cid]['u_release_date']) {
			return $parent;
		} else if ($this->_menu_info[$cid]['u_release_date'] <= time()) {
			return true;
		} else {
			return $this->_menu_info[$cid]['u_release_date'];
		}
	}
}

?>