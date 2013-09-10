<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function blogs_news() {
    global $db, $enrolled_courses, $system_courses;
    $news = array();

    if ($enrolled_courses == ''){
        return $news;
    } 

    $sql = "SELECT G.group_id, G.title, G.modules, T.course_id FROM %sgroups G INNER JOIN %sgroups_types  T USING (type_id) WHERE T.course_id IN %s ORDER BY G.title";
    $rows_enrolled = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $enrolled_courses));
    if (count($rows_enrolled) > 0){
        foreach($rows_enrolled as $row){
            if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
                // check for group membership before showing news.
                $sql = "SELECT member_id FROM %sgroups_members WHERE member_id=%d AND group_id= %d";
                $row_group_member = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $row['group_id']), TRUE);
                
                // check for course instructor, show blog news if so
                $sql = "SELECT member_id from %scourses WHERE member_id =%d";
                $row_instructor = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
                
                if(count($row_group_member) > 0 || count($row_instructor ) > 0){                                
                    // retrieve the last posted date/time from this blog
                    $sql = "SELECT MAX(date) AS date FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d";
                    $row2 = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $row['group_id']), TRUE);				
                    $last_updated = ' - ' . _AT('last_updated', AT_date(_AT('forum_date_format'), $row2['date'], AT_DATE_MYSQL_DATETIME));
            
                    $link_title = $row['title'];
                    $news[] = array('time'=>$row2['date'], 
                    'object'=>$row, 
                    'alt'=>_AT('blogs'),
                    'course'=>$system_courses[$row['course_id']]['title'],
                    'thumb'=>'images/home-blogs_sm.png', 
                    'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$row['group_id']).'"'.
                      (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'blog_posts.title').'"' : '') .'>'. 
                      AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'blog_posts.title') .'</a>');
               }
            }
        }
    }
    return $news;
}

?>
