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
	var $magic_quotes;

	// private
	var $allowed_tags = '<p><b><i><a><em><br><strong><blockquote><tt><li><ol><ul><img><hr>';

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

		if (get_magic_quotes_gpc() == 1) {
			$this->magic_quotes = true;
		} else {
			$this->magic_quotes = false;
		}
	}


	function initContent( ) {
		if ($this->course_id == '') {
			return;
		}
		$sql = "SELECT content_id, content_parent_id, ordering, title FROM ".TABLE_PREFIX."content WHERE course_id=$this->course_id ORDER BY content_parent_id, ordering";
		$result = mysql_query($sql);

		/* x could be the ordering or even the content_id	*/
		/* don't really need the ordering anyway.			*/
		/* $_menu[content_parent_id][x] = array('content_id', 'ordering', 'title') */
		$_menu = array();

		/*	$_menu_parents[content_id] = array('content_parent_id', 'title')	*/
		$_menu_parents = array();

		/* number of content sections */
		$num_sections = 0;
		
		$max_depth = array();

		while ($row = mysql_fetch_assoc($result)) {
			$num_sections++;
			$_menu[$row['content_parent_id']][] = array('content_id'=> $row['content_id'],
														'ordering'	=> $row['ordering'], 
														'title'		=> $row['title']);

			$_menu_info[$row['content_id']] = array('content_parent_id' => $row['content_parent_id'],
													'title'				=> $row['title'],
													'ordering'			=> $row['ordering']);

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
		if ( $_SESSION['is_admin'] != 1) {
			return false;
		}

		/* get the maximum ordering for this content_parent_id */
		$parents	  = $this->getContent($content_parent_id);
		if (is_array($parents)) {
			$max_ordering = count($parents);
		} else {
			$max_ordering = 0;
		}
		if ($ordering == $max_ordering) {
			/* we're adding at the end or the first topic							 */
			/* example: max_ordering = 0, insert at 1 if empty, max_ordering+1 o/w */
			$ordering++;
		} else {
			/* we're inserting in the beginning or middle, so shift					 */
			/* example: ordering = 2, shift those >2, insert at 3					 */
			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 WHERE ordering > $ordering AND course_id=$course_id AND content_parent_id=$content_parent_id";
			$err = mysql_query($sql, $this->db);
			$ordering++;
    	}

		/* cleanup the body: */
		//$text = strip_tags($text, $this->getAllowedTags());

		/* main topics all have minor_num = 0 */
		$sql = "INSERT INTO ".TABLE_PREFIX."content VALUES (0,$course_id, $content_parent_id, $ordering, NOW(), 0, $formatting, '$release_date', '', '$keywords', '$title','$text')";
		$err = mysql_query($sql, $this->db);

		/* insert the related content */
		$cid = mysql_insert_id($this->db);
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


	function editContent($content_id, $title, $text, $keywords, $new_ordering, $related, $formatting, $move, $release_date) {
		if ( $_SESSION['is_admin'] != 1) {
			return false;
		}

		/* first get the content to make sure it exists	*/
		$sql	= "SELECT ordering, content_parent_id FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $this->db);
		if (!($row = mysql_fetch_assoc($result)) ) {
			return false;
		}
		$old_ordering		= $row['ordering'];
		$content_parent_id	= $row['content_parent_id'];
		$new_content_parent_id = $content_parent_id;
		$new_content_ordering  = $row['ordering'];

		if ($move != -1) {
			if ($move == 0) {
				$new_content_parent_id = 0;
				$new_content_ordering  = 1;

			} else {
				$new_content_parent_id = $move;
				$new_content_ordering  = 1;
			}

			/* step 1:											*/
			/* remove the gap left by the moved content			*/
			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering-1 WHERE ordering>=$old_ordering AND content_parent_id=$content_parent_id AND content_id<>$content_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			/* step 2:											*/
			/* shift the new neighbouring content down			*/
			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 WHERE ordering>=$new_content_ordering AND content_parent_id=$new_content_parent_id AND content_id<>$content_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			/* step 3:											*/
			/* insert the new content at the bottom				*/

		} else if ($new_ordering != -1) {
			/* this content will be moved */

			if ($old_ordering < $new_ordering) {
				/* move it down				      */
				$start = $old_ordering;
				$end   = $new_ordering;

				/* and shift everything else up   */
				$sign = '-';
			} else {
				/* move it up					  */
				$start = $new_ordering;
				$end   = $old_ordering;

				/* and shift everything else down */
				$sign = '+';
			}

			$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering $sign 1 WHERE ordering>=$start AND ordering<=$end AND content_parent_id=$content_parent_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			$new_content_ordering = $new_ordering;
		} /* end moving block */

		/* cleanup the body: */
		//$text = strip_tags($text, $this->getAllowedTags());

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
		if ( $_SESSION['is_admin'] != 1) {
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

		/* delete this content page					*/
		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
		$result = mysql_query($sql, $this->db);

		/* re-order the rest of the content */
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering-1 WHERE ordering>=$ordering AND content_parent_id=$content_parent_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);
		/* end moving block */

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

		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
		$result = mysql_query($sql, $this->db);
	}


	function & getContentPage($content_id) {
		$sql	= "SELECT *, release_date+0 AS r_date, NOW()+0 AS n_date FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);

		return $result;
	}


	function getRelatedContent($content_id, $all=false) {
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

	function getAllowedTags() {
		return $this->allowed_tags;
	}

	function getNumSections() {
		return $this->num_sections;
	}

	function getMaxDepth() {
		return $this->max_depth;
	}

	function getContentLength() {
		return $this->content_length;
	}

	function getLocationPositions($parent_id, $content_id) {
		$siblings = $this->getContent($parent_id);
		for ($i=0;$i<count($siblings); $i++){
			if ($siblings[$i]['content_id'] == $content_id) {
				return $i;
			}
		}
		return 0;	
	}

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

	function getPreviousContent($content_id, $order=0) {
		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];

		if (($this->_menu[$myParent][$myOrder-2] != '') && ($order==0)) {
			// has sibling: checking if sibling has children
			
			$mySibling = $this->_menu[$myParent][$myOrder-2];
			
			if ( is_array($this->_menu[$mySibling['content_id']]) && ($order==0) ) {
				$num_children = count($this->_menu[$mySibling['content_id']]);

				// sibling has $num_children children
				
				return($this->getPreviousContent($this->_menu[$mySibling[content_id]][$num_children-1]['content_id'], 1));

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
				if ( is_array($this->_menu[$content_id]) ) {
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

	function getNextContent($content_id, $order=0) {
		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];

		/* if this content has children, then take the first one. */
		if ( is_array($this->_menu[$content_id]) && ($order==0) ) {
			/* has children */
			return($this->_menu[$content_id][0]);
		} else {
			/* no children */
			if ($this->_menu[$myParent][$myOrder] != '') {
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

	function generateSequenceCrumbs($cid) {
		global $_base_path;

		$next_prev_links = '';

		/* previous link */
		$previous	= $this->getPreviousContent($cid);
		$next		= $this->getNextContent($cid ? $cid : 0);

		if ($_SESSION['prefs'][PREF_NUMBERING]) {
			if ($previous != '') {
				$previous['title'] = $this->getNumbering($previous['content_id']).' '.$previous['title'];
			}

			if ($next != '') {
				$next['title'] = $this->getNumbering($next['content_id']).' '.$next['title'];
			}
		}

		if ($previous != '') {
			$previous['title'] = htmlspecialchars($previous['title']);
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= '<a href="'.$_base_path.'?cid='.$previous['content_id'].SEP.'g=7" accesskey="8" title="'._AT('previous').': '.$previous['title'].' Alt-8"><img src="'.$_base_path.'images/previous.gif" class="menuimage" border="0" alt="'._AT('previous').': '.$previous['title'].'" height="25" width="28" /></a>'."\n";
			}

			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= '<a href="'.$_base_path.'?cid='.$previous['content_id'].SEP.'g=7" accesskey="8" title="'._AT('previous').': '.$previous['title'].' Alt-8"> '._AT('previous').': '.$previous[title].'</a>'."\n";
			}
		} else if ($cid != 0) {
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= '<a href="'.$_base_path.'?g=7" accesskey="8" title="'._AT('previous').': '._AT('home').'"><img src="'.$_base_path.'images/previous.gif" class="menuimage" border="0" alt="'._AT('previous').': '._AT('home').' ALT-8" /></a>'."\n";
			}

			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= ' <a href="'.$_base_path.'?g=7" accesskey="8" title="'._AT('previous').': '.$previous['title'].' Alt-8"> '._AT('previous').': '._AT('home').'</a>'."\n";
			}
		} else {
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= '<img src="'.$_base_path.'images/previous.gif" class="menuimage" border="0" alt="'._AT('previous_none').'" title="'._AT('previous_none').'" style="filter:alpha(opacity=40);-moz-opacity:0.4" height="25" width="28" />'."\n";
			}
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= ' <small class="bigspacer"> '._AT('previous_none').'</small>';
			}
		}

		$next_prev_links .= ' <span class="spacer">|</span> ';

		/* resume link */
		if ($_SESSION['s_cid'] != $cid) {
			$next_prev_links .= ' ';
			$alt_title = htmlspecialchars($this->_menu_info[$_SESSION['s_cid']]['title']);
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= '<a href="'.$_base_path.'?cid='.$_SESSION['s_cid'].SEP.'g=7" accesskey="0" title="'._AT('resume').': '.$alt_title.' Alt-0"><img src="'.$_base_path.'images/resume.gif" class="menuimage" border="0" alt="'._AT('resume').': '.$alt_title.' ALT-0" height="25" width="28" /></a>'."\n";
			}

			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= ' <a href="'.$_base_path.'?cid='.$_SESSION['s_cid'].SEP.'g=7" accesskey="0" title="'._AT('resume').':'.$alt_title.':  Alt-0">'._AT('resume').': '.$alt_title.'</a>'."\n";
			}

			$next_prev_links .= ' <span class="spacer">|</span> ';
		}

		/* next link */

		if ($next != '') {
			$next['title'] = htmlspecialchars($next['title']);
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= '<a href="'.$_base_path.'?cid='.$next['content_id'].SEP.'g=7" accesskey="9" title="'._AT('next').': '.$next['title'].'  Alt-9">'._AT('next').': '.$next['title'].' </a>'."\n";
			}

			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= ' <a href="'.$_base_path.'?cid='.$next['content_id'].SEP.'g=7" accesskey="9" title="'._AT('next').': '.$next['title'].'  Alt-9"><img src="'.$_base_path.'images/next.gif" class="menuimage" border="0" alt="'._AT('next').': '.$next['title'].'" height="25" width="28" /></a>'."\n";
			}
		} else {
			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
				$next_prev_links .= '<small class="bigspacer">'._AT('next_none').' </small> ';
			}

			if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
				$next_prev_links .= '<img src="'.$_base_path.'images/next.gif" class="menuimage" border="0" alt="'._AT('next_none').'" style="filter:alpha(opacity=40);-moz-opacity:0.4" height="25" width="28" />'."\n";
			}
		}
		$next_prev_links .= '&nbsp;&nbsp;';

		return $next_prev_links."\n";
	}
}

?>