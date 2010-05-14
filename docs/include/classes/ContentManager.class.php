<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
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

	/*	array		*/
	var $_menu_in_order;

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
		$this->course_id = intval($course_id);
	}

	function initContent( ) {
		if (!($this->course_id > 0)) {
			return;
		}
		$sql = "SELECT content_id, content_parent_id, ordering, title, UNIX_TIMESTAMP(release_date) AS u_release_date, content_type 
		          FROM ".TABLE_PREFIX."content 
		         WHERE course_id=$this->course_id 
		         ORDER BY content_parent_id, ordering";
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
														'title'		=> htmlspecialchars($row['title']),
														'content_type' => $row['content_type']);

			$_menu_info[$row['content_id']] = array('content_parent_id' => $row['content_parent_id'],
													'title'				=> htmlspecialchars($row['title']),
													'ordering'			=> $row['ordering'],
													'u_release_date'    => $row['u_release_date'],
													'content_type' => $row['content_type']);

			/* 
			 * add test content asscioations
			 * find associations per content page, and add it as a sublink.
			 * @author harris
			 */
			$test_rs = $this->getContentTestsAssoc($row['content_id']);
			while ($test_row = mysql_fetch_assoc($test_rs)){
				$_menu[$row['content_id']][] = array(	'test_id'	=> $test_row['test_id'],
														'title'		=> htmlspecialchars($test_row['title']),
														'content_type' => CONTENT_TYPE_CONTENT);
			}
			/* End of add test content asscioations */

			if ($row['content_parent_id'] == 0) {
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

		// generate array of all the content ids in the same order that they appear in "content navigation"
		$this->_menu_in_order[] = $next_content_id = $this->getNextContentID(0);
		while ($next_content_id > 0)
		{
			$next_content_id = $this->getNextContentID($next_content_id);
			
			if (in_array($next_content_id, $this->_menu_in_order)) break;
			else $this->_menu_in_order[] = $next_content_id;
		}
		
		$this->content_length = count($_menu[0]);
	}

	// This function is called by initContent to construct $this->_menu_in_order, an array to 
	// holds all the content ids in the same order that they appear in "content navigation"
	function getNextContentID($content_id, $order=0) {
		// return first root content when $content_id is not given
		if (!$content_id) {
			return $this->_menu[0][0]['content_id'];
		}
		
		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];
		
		// calculate $myOrder, add in the number of tests in front of this content page
		if (is_array($this->_menu[$myParent])) {
			$num_of_tests = 0;
			foreach ($this->_menu[$myParent] as $menuContent) {
				if ($menuContent['content_id'] == $content_id) break;
				if (isset($menuContent['test_id'])) $num_of_tests++;
			}
		}
		$myOrder += $num_of_tests;
		// end of calculating $myOrder
		
		/* if this content has children, then take the first one. */
		if ( isset($this->_menu[$content_id]) && is_array($this->_menu[$content_id]) && ($order==0) ) {
			/* has children */
			// if the child is a test, keep searching for the content id
			foreach ($this->_menu[$content_id] as $menuID => $menuContent)
			{
				if (!empty($menuContent['test_id'])) continue;
				else 
				{
					$nextMenu = $this->_menu[$content_id][$menuID]['content_id'];
					break;
				}
			}
			
			// all children are tests
			if (!isset($nextMenu))
			{
				if (isset($this->_menu[$myParent][$myOrder]['content_id'])) {
					// has sibling
					return $this->_menu[$myParent][$myOrder]['content_id'];
				}
				else { // no sibling
					$nextMenu = $this->getNextContentID($myParent, 1);
				}
			}
			return $nextMenu;
		} else {
			/* no children */
			if (isset($this->_menu[$myParent][$myOrder]) && $this->_menu[$myParent][$myOrder] != '') {
				/* Has sibling */
				return $this->_menu[$myParent][$myOrder]['content_id'];
			} else {
				/* No more siblings */
				if ($myParent != 0) {
					return $this->getNextContentID($myParent, 1);
				}
			}
		}
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

	function addContent($course_id, $content_parent_id, $ordering, $title, $text, $keywords, 
	                    $related, $formatting, $release_date, $head = '', $use_customized_head = 0, 
	                    $test_message = '', $allow_test_export = 1, $content_type = CONTENT_TYPE_CONTENT) {
		
		if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['course_id'] != -1)) {
			return false;
		}

		// shift the new neighbouring content down
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 
		         WHERE ordering>=$ordering 
		           AND content_parent_id=$content_parent_id 
		           AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $this->db);

		/* main topics all have minor_num = 0 */
		$sql = "INSERT INTO ".TABLE_PREFIX."content
		               (course_id,
		                content_parent_id,
		                ordering,
		                last_modified,
		                revision,
		                formatting,
		                release_date,
		                head,
		                use_customized_head,
		                keywords,
		                content_path,
		                title,
		                text,
						test_message,
						allow_test_export,
						content_type)
		        VALUES ($course_id, 
		                $content_parent_id, 
		                $ordering, 
		                NOW(), 
		                0, 
		                $formatting, 
		                '$release_date', 
		                '$head',
		                $use_customized_head,
		                '$keywords', 
		                '', 
		                '$title',
		                '$text',
						'$test_message',
						$allow_test_export,
						$content_type)";

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
	
	function editContent($content_id, $title, $text, $keywords,$related, $formatting, 
	                     $release_date, $head, $use_customized_head, $test_message, 
	                     $allow_test_export) {
		if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
			return FALSE;
		}

		/* update the title, text of the newly moved (or not) content */
		$sql	= "UPDATE ".TABLE_PREFIX."content 
		              SET title='$title', head='$head', use_customized_head=$use_customized_head, 
		                  text='$text', keywords='$keywords', formatting=$formatting, 
		                  revision=revision+1, last_modified=NOW(), release_date='$release_date', 
		                  test_message='$test_message', allow_test_export=$allow_test_export 
		            WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
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

	function moveContent($content_id, $new_content_parent_id, $new_content_ordering) {
		global $msg;
		
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
		$old_content_parent_id	= $row['content_parent_id'];
		
		$sql	= "SELECT max(ordering) max_ordering FROM ".TABLE_PREFIX."content WHERE content_parent_id=$old_content_parent_id AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $this->db);
		$row = mysql_fetch_assoc($result);
		$max_ordering = $row['max_ordering'];
		
		if ($content_id == $new_content_parent_id) {
			$msg->addError("NO_SELF_AS_PARENT");
			return;
		}
		
		if ($old_content_parent_id == $new_content_parent_id && $old_ordering == $new_content_ordering) {
			$msg->addError("SAME_LOCATION");
			return;
		}
		
		$content_path = $this->getContentPath($new_content_parent_id);
		foreach ($content_path as $parent){
			if ($parent['content_id'] == $content_id) {
				$msg->addError("NO_CHILD_AS_PARENT");
				return;
			}
		}
		
		// if the new_content_ordering is greater than the maximum ordering of the parent content, 
		// set the $new_content_ordering to the maximum ordering. This happens when move the content 
		// to the last element under the same parent content.
		if ($old_content_parent_id == $new_content_parent_id && $new_content_ordering > $max_ordering) 
			$new_content_ordering = $max_ordering;
		
		if (($old_content_parent_id != $new_content_parent_id) || ($old_ordering != $new_content_ordering)) {
			// remove the gap left by the moved content
			$sql = "UPDATE ".TABLE_PREFIX."content 
			           SET ordering=ordering-1 
			         WHERE ordering>$old_ordering 
			           AND content_parent_id=$old_content_parent_id 
			           AND content_id<>$content_id 
			           AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			// shift the new neighbouring content down
			$sql = "UPDATE ".TABLE_PREFIX."content 
			           SET ordering=ordering+1 
			         WHERE ordering>=$new_content_ordering 
			           AND content_parent_id=$new_content_parent_id 
			           AND content_id<>$content_id 
			           AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $this->db);

			$sql	= "UPDATE ".TABLE_PREFIX."content 
			              SET content_parent_id=$new_content_parent_id, ordering=$new_content_ordering 
			            WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
			$result	= mysql_query($sql, $this->db);
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

		/* delete the content tests association */
		$sql	= "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$content_id";
		$result = mysql_query($sql, $this->db);

		/* delete the content forum association */
		$sql	= "DELETE FROM ".TABLE_PREFIX."content_forums_assoc WHERE content_id=$content_id";
		$result = mysql_query($sql, $this->db);

		/* Delete all AccessForAll contents */
		require_once(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.class.php');
		$a4a = new A4a($content_id);
		$a4a->deleteA4a();

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

		/* delete the content tests association */
		$sql	= "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$content_id";
		$result = mysql_query($sql, $this->db);
	}

	function getContentPage($content_id) {
		$sql	= "SELECT *, DATE_FORMAT(release_date, '%Y-%m-%d %H:%i:00') AS release_date, release_date+0 AS r_date, NOW()+0 AS n_date FROM ".TABLE_PREFIX."content 
		            WHERE content_id=$content_id";
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

	/** 
	 * Return a list of tests associated with the selected content
	 * @param	int		the content id that all tests are associated with it.
	 * @return	array	list of tests
	 * @date	Sep 10, 2008
	 * @author	Harris
	 */
	function & getContentTestsAssoc($content_id){
		$sql	= "SELECT ct.test_id, t.title FROM (SELECT * FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$content_id) AS ct LEFT JOIN ".TABLE_PREFIX."tests t ON ct.test_id=t.test_id";
		$result = mysql_query($sql, $this->db);
		return $result;
	}

        /*TODO***************BOLOGNA***************REMOVE ME**********/
        function & getContentForumsAssoc($content_id){
		$sql	= "SELECT cf.forum_id, f.title FROM (SELECT * FROM ".TABLE_PREFIX."content_forums_assoc WHERE content_id=$content_id) AS cf LEFT JOIN ".TABLE_PREFIX."forums f ON cf.forum_id=f.forum_id";
		$result = mysql_query($sql, $this->db);
		return $result;
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

	function getPreviousContent($content_id) {
		if (is_array($this->_menu_in_order))
		{
			foreach ($this->_menu_in_order as $content_location => $this_content_id)
			{
				if ($this_content_id == $content_id) break;
			}
			
			for ($i=$content_location-1; $i >= 0; $i--)
			{
				$content_type = $this->_menu_info[$this->_menu_in_order[$i]]['content_type'];
				
				if ($content_type == CONTENT_TYPE_CONTENT || $content_type == CONTENT_TYPE_WEBLINK)
					return array('content_id'	=> $this->_menu_in_order[$i],
				    	         'ordering'		=> $this->_menu_info[$this->_menu_in_order[$i]]['ordering'],
				        	     'title'		=> $this->_menu_info[$this->_menu_in_order[$i]]['title']);
			}
		}
		return NULL;
	}
	
	function getNextContent($content_id) {
		if (is_array($this->_menu_in_order))
		{
			foreach ($this->_menu_in_order as $content_location => $this_content_id)
			{
				if ($this_content_id == $content_id) break;
			}
			
			for ($i=$content_location+1; $i < count($this->_menu_in_order); $i++)
			{
				$content_type = $this->_menu_info[$this->_menu_in_order[$i]]['content_type'];
				
				if ($content_type == CONTENT_TYPE_CONTENT || $content_type == CONTENT_TYPE_WEBLINK)
					return(array('content_id'	=> $this->_menu_in_order[$i],
				    	         'ordering'		=> $this->_menu_info[$this->_menu_in_order[$i]]['ordering'],
				        	     'title'		=> $this->_menu_info[$this->_menu_in_order[$i]]['title']));
			}
		}
		return NULL;
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
			$first['url'] = $_base_path.url_rewrite('content.php?cid='.$first['content_id']);
			$sequence_links['first'] = $first;
		}

		if (!$cid && $_SESSION['s_cid']) {
			$resume['title'] = $this->_menu_info[$_SESSION['s_cid']]['title'];

			if ($_SESSION['prefs']['PREF_NUMBERING']) {
				$resume['title'] = $this->getNumbering($_SESSION['s_cid']).' ' . $resume['title'];
			}

			$resume['url'] = $_base_path.url_rewrite('content.php?cid='.$_SESSION['s_cid']);

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

			$next['url'] = $_base_path.url_rewrite('content.php?cid='.$next['content_id']);
			if (isset($previous['content_id'])) {
				$previous['url'] = $_base_path.url_rewrite('content.php?cid='.$previous['content_id']);
			}
			
			if (isset($previous['content_id'])) {
				$sequence_links['previous'] = $previous;
			} else if ($cid) {
				$previous['url']   = $_base_path . url_rewrite('index.php');
				$previous['title'] = _AT('home');
				$sequence_links['previous'] = $previous;
			}
			if (!empty($next['content_id'])) {
				$sequence_links['next'] = $next;
			}
		}

		return $sequence_links;
	}

	/** Generate javascript to hide all root content folders, except the one with current content page
	 * access: private
	 * @return print out javascript function initContentMenu()
	 */
	function initMenu(){
		global $_base_path;
		
		echo '
function initContentMenu() {
	tree_collapse_icon = "'.$_base_path.'images/tree/tree_collapse.gif";
	tree_expand_icon = "'.$_base_path.'images/tree/tree_expand.gif";
		
';
		
		$sql = "SELECT content_id
		          FROM ".TABLE_PREFIX."content 
		         WHERE course_id=$this->course_id
		           AND content_type = ".CONTENT_TYPE_FOLDER;
		$result = mysql_query($sql, $this->db);

		// collapse all root content folders
		while ($row = mysql_fetch_assoc($result)) {
			echo '
	if (ATutor.getcookie("c'.$_SESSION['course_id'].'_'.$row['content_id'].'") == "1")
	{
		jQuery("#folder"+'.$row['content_id'].').show();
		jQuery("#tree_icon"+'.$row['content_id'].').attr("src", tree_collapse_icon);
		jQuery("#tree_icon"+'.$row['content_id'].').attr("alt", "'._AT("collapse").'");
		jQuery("#tree_icon"+'.$row['content_id'].').attr("title", "'._AT("collapse").'");
	}
	else
	{
		jQuery("#folder"+'.$row['content_id'].').hide();
		jQuery("#tree_icon"+'.$row['content_id'].').attr("src", tree_expand_icon);
		jQuery("#tree_icon"+'.$row['content_id'].').attr("alt", "'._AT("expand").'");
		jQuery("#tree_icon"+'.$row['content_id'].').attr("title", "'._AT("expand").'");
	}
';
		}
		
		// expand the content folder that has current content
		if (isset($_SESSION['s_cid']) && $_SESSION['s_cid'] > 0) {
			$current_content_path = $this->getContentPath($_SESSION['s_cid']);
			
			for ($i=0; $i < count($current_content_path)-1; $i++)
				echo '
	jQuery("#folder"+'.$current_content_path[$i]['content_id'].').show();
	jQuery("#tree_icon"+'.$current_content_path[$i]['content_id'].').attr("src", tree_collapse_icon);
	jQuery("#tree_icon"+'.$current_content_path[$i]['content_id'].').attr("alt", "'._AT("collapse").'");
	ATutor.setcookie("c'.$_SESSION['course_id'].'_'.$current_content_path[$i]['content_id'].'", "1", 1);
';
		}
		echo '}'; // end of javascript function initContentMenu()
	}
	
	/* @See include/html/dropdowns/menu_menu.inc.php */
	function printMainMenu( ) {
		if (!($this->course_id > 0)) {
			return;
		}
		
		global $_base_path;
		
		$parent_id    = 0;
		$depth        = 0;
		$path         = '';
		$children     = array();
		$truncate     = true;
		$ignore_state = true;

		$this->start = true;
		
		// if change the location of this line, change function switchEditMode(), else condition accordingly
		echo '<div id="editable_table">';
		
		if (authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN) && !is_mobile_theme())
		{
			echo "\n".'
			<div class="menuedit">
			<a href="'.$_base_path.'mods/_core/editor/edit_content_folder.php">
				<img id="img_create_top_folder" src="'.$_base_path.'images/folder_new.gif" alt="'._AT("add_top_folder").'" title="'._AT("add_top_folder").'" style="border:0;height:1.2em" />
			</a>'."\n".
			'<a href="'.$_base_path.'mods/_core/editor/edit_content.php">
				<img id="img_create_top_content" src="'.$_base_path.'images/page_add.gif" alt="'._AT("add_top_page").'" title="'._AT("add_top_page").'" style="border:0;height:1.2em" />
			</a>'."\n".
			'<a href="javascript:void(0)" onclick="javascript:switchEditMode();">
				<img id="img_switch_edit_mode" src="'.$_base_path.'images/medit.gif" alt="'._AT("enter_edit_mode").'" title="'._AT("enter_edit_mode").'" style="border:0;height:1.2em" />
			</a>
			</div>'."\n";
		}
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
		
		// javascript for inline editor
		echo '
<script type="text/javascript">
';
		// only expand the content folder that has the current content page
		$this->initMenu();
		
		echo '
function switchEditMode() {
	title_edit = "'._AT("enter_edit_mode").'";
	img_edit = "'.$_base_path.'images/medit.gif";
	
	title_view = "'._AT("exit_edit_mode").'";
	img_view = "'.$_base_path.'images/mlock.gif";
	
	if (jQuery("#img_switch_edit_mode").attr("src") == img_edit)
	{
		jQuery("#img_switch_edit_mode").attr("src", img_view);
		jQuery("#img_switch_edit_mode").attr("alt", title_view);
		jQuery("#img_switch_edit_mode").attr("title", title_view);
		inlineEditsSetup();
	}
	else
	{ // refresh the content navigation to exit the edit mode
		jQuery.post("'. $_base_path. 'mods/_core/content/refresh_content_nav.php", {}, 
					function(data) {jQuery("#editable_table").replaceWith(data); initContentMenu();});
	}
}

function inlineEditsSetup() {
	jQuery("#editable_table").find(".inlineEdits").each(function() {
		jQuery(this).text(jQuery(this).attr("title"));
	});
	
	var tableEdit = fluid.inlineEdits("#editable_table", {
		selectors : {
			text : ".inlineEdits",
			editables : "span:has(span.inlineEdits)"
		},
		defaultViewText: "",
		applyEditPadding: false,
		useTooltip: true,
		listeners: {
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue) 
				{
					rtn = jQuery.post("'. $_base_path. 'mods/_core/content/menu_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
						          function(data) {}, "json");
				}
			}
		}
	});

	jQuery(".fl-inlineEdit-edit").css("width", "80px")

};

initContentMenu();
</script>
';
		echo '</div>';
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
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state, 'sitemap');
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
	function printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state, $from = '') {
		global $cid, $_my_uri, $_base_path, $rtl, $substr, $strlen;
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
			
			echo '<div id="folder'.$parent_id.$from.'">'."\n";
			
			foreach ($top_level as $garbage => $content) {
				$link = '';
				//tests do not have content id
				$content['content_id'] = isset($content['content_id']) ? $content['content_id'] : '';

				if (!$ignore_state) {
					$link .= '<a name="menu'.$content['content_id'].'"></a>';
				}

				$on = false;

				if ( (($_SESSION['s_cid'] != $content['content_id']) || ($_SESSION['s_cid'] != $cid)) && ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)) 
				{ // non-current content nodes with content type "CONTENT_TYPE_CONTENT"
					if (isset($highlighted[$content['content_id']])) {
						$link .= '<strong>';
						$on = true;
					}

					//content test extension  @harris
					//if this is a test link.
					if (isset($content['test_id'])){
						$title_n_alt =  $content['title'];
						$in_link = 'mods/_standard/tests/test_intro.php?tid='.$content['test_id'];
						$img_link = ' <img src="'.$_base_path.'images/check.gif" title="'.$title_n_alt.'" alt="'.$title_n_alt.'" />';
					} else {
						$in_link = 'content.php?cid='.$content['content_id'];
						$img_link = '';
					}
					
					$full_title = $content['title'];
					$link .= $img_link . ' <a href="'.$_base_path.url_rewrite($in_link).'" title="';
					$base_title_length = 29;
					if ($_SESSION['prefs']['PREF_NUMBERING']) {
						$base_title_length = 24;
					}

					$link .= $content['title'].'">';

					if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
						$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
					}
					
					if (isset($content['test_id'])) {
						$link .= $content['title'];
					} else {
						$link .= '<span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">';
						if($_SESSION['prefs']['PREF_NUMBERING']){
						  $link .= $path.$counter;
						}
						$link .= '&nbsp;'.$content['title'].'</span>';
					}
					
					$link .= '</a>';
					if ($on) {
						$link .= '</strong>';
					}
					
					// instructors have privilege to delete content
					if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && !isset($content['test_id']) && !is_mobile_theme()) {
						$link .= '<a href="'.$_base_path.'/mods/_core/editor/delete_content.php?cid='.$content['content_id'].'"><img src="'.AT_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0" height="10" /></a>';
					}
				} 
				else 
				{ // current content page & nodes with content type "CONTENT_TYPE_FOLDER"
					$base_title_length = 26;
					if ($_SESSION['prefs']['PREF_NUMBERING']) {
						$base_title_length = 21;
					}
					
					if (isset($highlighted[$content['content_id']])) {
						$link .= '<strong>';
						$on = true;
					}

					if ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)
					{ // current content page
						$full_title = $content['title'];
						$link .= '<a href="'.$_my_uri.'"><img src="'.$_base_path.'images/clr.gif" alt="'._AT('you_are_here').': ';
						if($_SESSION['prefs']['PREF_NUMBERING']){
						  $link .= $path.$counter;
						}
						  $link .= $content['title'].'" height="1" width="1" border="0" /></a><strong style="color:red" title="'.$content['title'].'">'."\n";
						if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
							$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
						}
						$link .= '<a name="menu'.$content['content_id'].'"></a><span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">';
						if($_SESSION['prefs']['PREF_NUMBERING']){
						  $link .= $path.$counter;
						}
						$link .='&nbsp;'.$content['title'].'</span></strong>';
						
						// instructors have privilege to delete content
						if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && !is_mobile_theme()) {
							$link .= '<a href="'.$_base_path.'mods/_core/editor/delete_content.php?cid='.$content['content_id'].'"><img src="'.AT_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0" height="10" /></a>';
						}
					}
					else
					{ // nodes with content type "CONTENT_TYPE_FOLDER"
						$full_title = $content['title'];
						if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && !is_mobile_theme()) {
							$link .= '<a href="'.$_base_path."mods/_core/editor/edit_content_folder.php?cid=".$content['content_id'].'" title="'.$full_title. _AT('click_edit').'">'."\n";
						}
						else {
							$link .= '<span style="cursor:pointer" onclick="javascript: ATutor.course.toggleFolder(\''.$content['content_id'].$from.'\'); ">'."\n";
						}
						
						if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
							$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
						}
						if (isset($content['test_id']))
							$link .= $content['title'];
						else
							$link .= '<span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">';
						if($_SESSION['prefs']['PREF_NUMBERING']){
						  $link .= $path.$counter;
						}
						  $link .= '&nbsp;'.$content['title'].'</span>';
						
						if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && !is_mobile_theme()) {
							$link .= '</a>'."\n";
						}
						else {
							$link .= '</span>'."\n";
						}
						
						// instructors have privilege to delete content
						if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && !is_mobile_theme()) {
							$link .= '<a href="'.$_base_path.'mods/_core/editor/delete_content.php?cid='.$content['content_id'].'"><img src="'.AT_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0" height="10" /></a>';
						}

					}
					
					if ($on) {
						$link .= '</strong>';
					}
				}

				if ($ignore_state) {
					$on = true;
				}

				echo '<span>'."\n";
				
				if ( isset($this->_menu[$content['content_id']]) && is_array($this->_menu[$content['content_id']]) ) {
					/* has children */
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						} else {
							echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						}
					}

					if (($counter == $num_items) && ($depth > 0)) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 0;
					} else if ($counter == $num_items) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 0;
					} else {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 1;
					}

					if ($_SESSION['s_cid'] == $content['content_id']) {
						if (is_array($this->_menu[$content['content_id']])) {
							$_SESSION['menu'][$content['content_id']] = 1;
						}
					}

					if (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1) {
						if ($on) {
							echo '<a href="javascript:void(0)" onclick="javascript: ATutor.course.toggleFolder(\''.$content['content_id'].$from.'\'); "><img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" /></a>'."\n";
							
						} else {
							echo '<a href="'.$_my_uri.'collapse='.$content['content_id'].'">'."\n";
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').' '.$content['title'].'" class="img-size-tree" onclick="javascript: ATutor.course.toggleFolder(\''.$content['content_id'].$from.'\'); " />'."\n";
							echo '</a>'."\n";
						}
					} else {
						if ($on) {
							echo '<a href="javascript:void(0)" onclick="javascript: ATutor.course.toggleFolder(\''.$content['content_id'].$from.'\'); "><img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" /></a>'."\n";
							
						} else {
							echo '<a href="'.$_my_uri.'expand='.$content['content_id'].'">'."\n";
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_expand.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('expand').'" border="0" width="16" height="16" 	title="'._AT('expand').' '.$content['title'].'" class="img-size-tree" onclick="javascript: ATutor.course.toggleFolder(\''.$content['content_id'].$from.'\'); " />';
							echo '</a>'."\n";
						}
					}

				} else {
					/* doesn't have children */
					if ($counter == $num_items) {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							} else {
								echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" class="img-size-tree" />'."\n";
					} else {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							} else {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
					}
					echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_horizontal.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
				}

				
				echo $link;
				
				echo "\n<br /></span>\n\n";
				
				if ( $ignore_state || (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1)) {

					$depth ++;

					$this->printMenu($content['content_id'],
										$depth, 
										$path.$counter.'.', 
										$children,
										$truncate, 
										$ignore_state,
										$from);

										
					$depth--;

				}
				$counter++;
			} // end of foreach

			print "</div>\n\n";
		}
	}

	/* @See include/html/editor_tabs/properties.inc.php
	   @See editor/arrange_content.php
	   
	    $print_type: "movable" or "related_content"
	 */
	function printActionMenu($menu, $parent_id, $depth, $path, $children, $print_type = 'movable') {
		
		global $cid, $_my_uri, $_base_path, $rtl;

		static $end;

		$top_level = $menu[$parent_id];

		if ( is_array($top_level) ) {
			$counter = 1;
			$num_items = count($top_level);
			foreach ($top_level as $current_num => $content) {
				if (isset($content['test_id'])){
					continue;
				}

				$link = $buttons = '';

				echo '<tr>'."\n";
				
				if ($print_type == 'movable')
				{
					if ($content['content_id'] == $_POST['moved_cid']) {
						$radio_selected = ' checked="checked" ';
					}
					else {
						$radio_selected = '';
					}
				
					$buttons = '<td>'."\n".
					           '   <small>'."\n".
					           '      <input type="image" name="move['.$parent_id.'_'.$content['ordering'].']" src="'.$_base_path.'images/before.gif" alt="'._AT('before_topic', $content['title']).'" title="'._AT('before_topic', $content['title']).'" style="height:1.5em; width:1.9em;" />'."\n";

					if ($current_num + 1 == count($top_level))
						$buttons .= '      <input type="image" name="move['.$parent_id.'_'.($content['ordering']+1).']" src="'.$_base_path.'images/after.gif" alt="'._AT('after_topic', $content['title']).'" title="'._AT('after_topic', $content['title']).'" style="height:1.5em; width:1.9em;" />'."\n";
					
					$buttons .= '   </small>'."\n".
					           '</td>'."\n".
					           '<td>';
					
					if ($content['content_type'] == CONTENT_TYPE_FOLDER)
						$buttons .= '<input type="image" name="move['.$content['content_id'].'_1]" src="'.$_base_path.'images/child_of.gif" style="height:1.25em; width:1.7em;" alt="'._AT('child_of', $content['title']).'" title="'._AT('child_of', $content['title']).'" />';
					else
						$buttons .= '&nbsp;';
						
					$buttons .= '</td>'."\n".
					           '<td><input name="moved_cid" value="'.$content['content_id'].'" type="radio" id="r'.$content['content_id'].'" '.$radio_selected .'/></td>'."\n";
				}
				
				$buttons .= '<td>'."\n";
				if ($print_type == "related_content")
				{
					if ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)
					{
						$link .= '<input type="checkbox" name="related[]" value="'.$content['content_id'].'" id="r'.$content['content_id'].'" ';
						if (isset($_POST['related']) && in_array($content['content_id'], $_POST['related'])) {
							$link .= ' checked="checked"';
						}
						$link .= ' />'."\n";
					}
				}	
				
				if ($content['content_type'] == CONTENT_TYPE_FOLDER)
				{
					$link .= '<img src="'.$_base_path.'images/folder.gif" />';
				}
				$link .= '&nbsp;<label for="r'.$content['content_id'].'">'.$content['title'].'</label>'."\n";

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

				echo '<small>';
				  if($_SESSION['prefs']['PREF_NUMBERING']){
					echo $path.$counter;
				    }
				
				echo $link;
				
				echo '</small></td>'."\n".'</tr>'."\n";

				$this->printActionMenu($menu,
									$content['content_id'],
									++$depth, 
									$path.$counter.'.', 
									$children,
									$print_type);
				$depth--;

				$counter++;
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

	/* returns the first test_id if this page has pre-test(s) to be passed, 
	 * or is under a page that has pre-test(s) to be passed, 
	 * 0 if has no pre-test(s) to be passed
	 * -1 if one of the pre-test(s) has expired, the content should not be displayed in this case
	 * Access: public 
	 */
	function getPretest($cid) {
		$this_pre_test_id = $this->getOnePretest($cid);
		
		if ($this->_menu_info[$cid]['content_parent_id'] == 0) {
			// this $cid has no parent, so we check its release date directly
			return $this_pre_test_id;
		}
		
		// this is a sub page, need to check ALL its parents
		$parent_pre_test_id = $this->getOnePretest($this->_menu_info[$cid]['content_parent_id']);
		
		if ($this_pre_test_id > 0 || $this_pre_test_id == -1)
			return $this_pre_test_id;
		else if ($parent_pre_test_id > 0 || $parent_pre_test_id == -1)
			return $parent_pre_test_id;
		else
			return 0;
	}

	/* returns the first test_id if this content has pre-test(s) to be passed, 
	 * 0 if has no pre-test(s) to be passed
	 * -1 if one of the pre-test(s) has expired, the content should not be displayed in this case
	 * Access: public 
	 */
	function getOnePretest($cid) {
		global $db, $msg;
		include_once(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');
		
		$sql = "SELECT *, UNIX_TIMESTAMP(t.start_date) AS start_date, UNIX_TIMESTAMP(t.end_date) AS end_date 
		          FROM ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."content_prerequisites cp
		         WHERE cp.content_id=".$cid."
		           AND cp.type = '".CONTENT_PRE_TEST."'
		           AND cp.item_id=t.test_id";
		$result= mysql_query($sql, $db);
		
		while ($row = mysql_fetch_assoc($result))
		{
			// check to make sure we can access this test
			if (!$row['guests'] && ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS)) {
				$msg->addInfo('NOT_ENROLLED');
			}
			
			if (!$row['guests'] && !authenticate_test($row['test_id'])) {
				$msg->addInfo(array('PRETEST_NO_PRIV',$row['title']));
			}
			
			// if the test is not release, not allow student to view the content
			if ($row['start_date'] > time() || $row['end_date'] < time()) {
				$msg->addInfo(array('PRETEST_EXPIRED',$row['title']));
				return -1;
			}
			
			$sql = "SELECT tr.result_id, count(*) num_of_questions, sum(ta.score) score, sum(tqa.weight) total_weight
			          FROM ".TABLE_PREFIX."tests_results tr, ".TABLE_PREFIX."tests_answers ta, ".TABLE_PREFIX."tests_questions_assoc tqa 
			         WHERE tr.test_id = ".$row['test_id']."
			           AND tr.member_id = ".$_SESSION['member_id']."
			           AND tr.result_id = ta.result_id
			           AND tr.test_id = tqa.test_id
			           AND ta.question_id = tqa.question_id
			         GROUP BY tr.result_id";
			$result_score = mysql_query($sql, $db);
			
			$num_of_attempts = 0;
			while ($row_score = mysql_fetch_assoc($result_score))
			{
				// skip the test when:
				// 1. no pass score is defined. this is a survey.
				// 2. the student has passed the test 
				// 3. the test has no question
				if (($row['passscore'] == 0 && $row['passpercent'] == 0) ||
				    $row_score['num_of_questions'] == 0 ||
				    ($row['passscore']<>0 && $row_score['score']>=$row['passscore']) || 
				    ($row['passpercent']<>0 && ($row_score['score']/$row_score['total_weight']*100)>=$row['passpercent']))
				    continue 2;
				
				$num_of_attempts++;
			}
			
			if ($row['num_takes'] != AT_TESTS_TAKE_UNLIMITED && $num_of_attempts >= $row['num_takes'])
			{
				$msg->addInfo(array('PRETEST_FAILED',$row['title']));
			}
			else
				return $row['test_id'];
		}
		return 0;
	}

	/** 
	 * Return true if this content page allows export, else false.
	 * @param	int	content id
	 * @return	true if 'allow_test_export'==1 || is instructor || oauth export into Transformable
	 */
	function allowTestExport($content_id){
		if (isset($_SESSION['is_admin']) || (isset($_REQUEST['m']) && isset($_REQUEST['c']))) {
			return true;
		}
		$sql = "SELECT allow_test_export FROM ".TABLE_PREFIX."content WHERE content_id=$content_id";
		$result = mysql_query($sql, $this->db);
		if ($row = mysql_fetch_assoc($result)){
			if ($row['allow_test_export'] == 1){
				return true;
			}
			return false;
		}
		return false;
	}
}

?>