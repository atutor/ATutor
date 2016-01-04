<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function forums_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = 'SELECT E.approved, E.last_cid, C.* FROM '.TABLE_PREFIX.'course_enrollment E, '.TABLE_PREFIX.'courses C WHERE C.course_id in '. $enrolled_courses . '  AND E.member_id='.$_SESSION['member_id'].' AND E.course_id=C.course_id ORDER BY C.title';
	$rows_en_courses = queryDB($sql, array());

    if(count($rows_en_courses) > 0){
	    foreach($rows_en_courses as $row){
			$all_forums = get_forums($row['course_id']);

			if (is_array($all_forums)){
				foreach($all_forums as $forums){				
					if (is_array($forums)){					
						foreach ($forums as $forum_obj){
                            $latest_post =get_last_post($forum_obj['forum_id']);
							$forum_obj['course_id'] = $row['course_id'];
							$link_title = $forum_obj['title'];
							
							// attached the first 120 characters of the message to the news item
                            if (strlen($latest_post[0]['body'] ) > 120){
                               $last_post = substr($latest_post[0]['body'], 0, 120) . '...';
                            } else {
                                $last_post = $latest_post[0]['body'];
                            }
                            
                            // if this is the only message in a thread, replace parent_id with the post_id
                            if($latest_post[0]['parent_id'] == 0){
                                $latest_post[0]['parent_id'] = $latest_post[0]['post_id'];
                            }
                            
                            if($latest_post[0]['subject'] !=''){
                                $news[] = array('time'=>$forum_obj['last_post'], 
                                    'object'=>$forum_obj, 
                                    'alt'=>_AT('forum'),
                                    'thumb'=>'images/pin.png',
                                    'course'=>$system_courses[$row['course_id']]['title'],
                                    'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'pu='.urlencode('mods/_standard/forums/forum/view.php?fid='.$forum_obj['forum_id'].SEP.'pid='.$latest_post[0]['parent_id']).'"'.
                                    (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'forums.title').'"' : '') .'>'. 
                                    AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'forums.title') .': '. $latest_post[0]['subject'] .' </a> - '.$last_post);
                            }						
						}
					}
				}
			}
		}
	}
	return $news;
}


function get_course_groups($course_id){
    $sql="SELECT GT.*, G.*  FROM %sgroups_types GT 
        JOIN %sgroups G
        ON GT.type_id = G.type_id
        WHERE GT.course_id =%d";
    $course_groups = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course_id));
    return $course_groups;
}

function get_last_post($forum_id){
    $sql = 'SELECT subject, body, post_id, parent_id, last_comment FROM %sforums_threads 
        WHERE forum_id = %d 
        ORDER BY post_id 
        DESC LIMIT 1  ';
    $latest_post = queryDB($sql, array(TABLE_PREFIX, $forum_id));
    return $latest_post;
}

function get_forums($course) {
	if ($course) {
		$sql	= "SELECT F.*, DATE_FORMAT(F.last_post, '%%Y-%%m-%%d %%H:%%i:%%s') AS last_post FROM %sforums_courses FC 
		INNER JOIN %sforums F 
		USING (forum_id) 
		WHERE FC.course_id=%d 
		GROUP BY FC.forum_id 
		ORDER BY F.title";
	    $rows_forums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course));
	} else {
		$sql	= "SELECT F.*, FC.course_id, DATE_FORMAT(F.last_post, '%%Y-%%m-%%d %%H:%%i:%%s') AS last_post FROM %sforums_courses FC 
		INNER JOIN %sforums F 
		USING (forum_id) 
		GROUP BY FC.forum_id 
		ORDER BY F.title";
	    $rows_forums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX));
	}

	// 'nonshared' forums are always listed first:
	$forums['nonshared'] = array();
	$forums['shared']    = array();
	$forums['group']     = array();

	foreach($rows_forums as $row){
		// for each forum, check if it's shared or not:
		if (is_shared_forum($row['forum_id'])) {
			$forums['shared'][] = $row;
		} else {
			$forums['nonshared'][] = $row;
		}
	}
		
	// retrieve the group forums if course is given
    $course_groups = get_course_groups($course);
	if (!$course_groups || !$course) {
		return $forums;
	}

	if (isset($course_groups)) {
		foreach($course_groups as $groups){
            $sql = "SELECT F.*, G.group_id FROM %sforums_groups G 
                     INNER JOIN %sforums F 
                     USING (forum_id) 
                     WHERE G.group_id IN (%s) 
                     ORDER BY F.title";
            $rows_gforums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $groups['group_id']));

            foreach($rows_gforums as $row){
                $row['title'] = get_group_title($row['group_id']);
                $forums['group'][] = $row;
            }
		}
	}
	return $forums;	
}

function is_shared_forum($forum_id) {
	$sql = "SELECT COUNT(*) AS cnt FROM %sforums_courses WHERE forum_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $forum_id), TRUE);

	if ($row['cnt'] > 1) {
		return TRUE;
	} // else:
	
	return FALSE;
}
?>
