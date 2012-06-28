<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: ustep_content_conversion.php 8861 2009-11-02 21:34:00Z hwong $

/** 
 * Construct a tree based on table entries
 * @param	array	current node, (current parent)
 * @param	mixed	a set of parents, where each parents is in the format of [parent]=>children
 *					should remain the same throughout the recursion.
 * @return	A tree structure representation of the content entries.
 * @author	Harris Wong
 */
function buildTree($current, $content_array){
	$folder = array();
	foreach($current as $order=>$content_id){
		//if has children
		if (isset($content_array[$content_id])){
			$wrapper[$content_id] = buildTree($content_array[$content_id], $content_array);
		}

		//no children.
		if ($wrapper){
			$folder['order_'.$order] = $wrapper;
			unset($wrapper);
		} else {
			$folder['order_'.$order] = $content_id;
		}
	}	
	return $folder;
}


/**
 * Transverse the content tree structure, and reconstruct it with the IMS spec.  
 * This tree has the structure of [order=>array(id)], so the first layer is its order, second is the id
 * if param merge is true, if node!=null, merge it to top layer, and + offset to all others
 * @param	mixed	Tree from the buildTree() function, or sub-tree
 * @param	mixed	the current tree.
 * @return	A new content tree that meets the IMS specification.
 * @author	Harris Wong 
 */
function rebuild($tree, $node=''){
    $order_offset = 0;
    $folder = array();
    if (!is_array($tree)){
        return $tree;
    }
    if ($node!=''){
        $tree['order_0'] = $node;
        $order_offset += 1;
    }
    //go through the tree
    foreach($tree as $k=>$v){
        if (preg_match('/order\_([\d]+)/', $k, $match)==1){
            //if this is the order layer
            $folder['order_'.($match[1]+$order_offset)] = rebuild($v);
        } else {
            //if this is the content layer
            if(is_array($v)){
                $folder[$k] = rebuild($v, $k);
            }
        }
    }
    return $folder;
}


/**
 * Transverse the tree and update/insert entries based on the updated structure.
 * @param	array	The tree from rebuild(), and the subtree from the recursion.
 * @param	int		the ordering of this subtree respect to its parent.
 * @param	int		parent content id
 * @return	null (nothing to return, it updates the db only)
 */
function reconstruct($tree, $order, $content_parent_id, $table_prefix){
	global $db;

	//a content page.
	if (!is_array($tree)){
		$sql = 'UPDATE '.$table_prefix."content SET ordering=$order, content_parent_id=$content_parent_id WHERE content_id=$tree";
		if (!mysql_query($sql, $db)){
			//throw error
			echo mysql_error();
		}
		return;
	}
	foreach ($tree as $k=>$v){
        if (preg_match('/order\_([\d]+)/', $k, $match)==1){
			//order layer
			reconstruct($v, $match[1], $content_parent_id, $table_prefix);	//inherit the previous layer id
		} else {
			//content folder layer
			$sql = 'SELECT * FROM '.$table_prefix."content WHERE content_id=$k";
			$result = mysql_query($sql, $db);
			$old_content_row = mysql_fetch_assoc($result);
			$sql = 'INSERT INTO '.$table_prefix.'content (course_id, content_parent_id, ordering, last_modified, revision, formatting, release_date, keywords, content_path, title, use_customized_head, allow_test_export, content_type) VALUES ('
				.$old_content_row['course_id'] . ', '
				.$content_parent_id . ', '
				.$order . ', '
				.'\''. $old_content_row['last_modified'] . '\', '
				.$old_content_row['revision'] . ', '
				.$old_content_row['formatting'] . ', '
				.'\''. $old_content_row['release_date'] . '\', '
				.'\''. mysql_real_escape_string($old_content_row['keywords']) . '\', '
				.'\''. mysql_real_escape_string($old_content_row['content_path']) . '\', '
				.'\''. mysql_real_escape_string($old_content_row['title']) . '\', '
				.$old_content_row['use_customized_head'] . ', '
				.$old_content_row['allow_test_export'] . ', '
				. '1)';
			
			if (mysql_query($sql, $db)){
				$folder_id = mysql_insert_id();
				reconstruct($v, '', $folder_id, $table_prefix);
			} else {
				//throw error
				echo mysql_error();
			}
		}
	}
}
?>

