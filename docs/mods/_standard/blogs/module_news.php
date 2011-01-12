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

    $sql = "SELECT G.group_id, G.title, G.modules, T.course_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types  T USING (type_id) WHERE T.course_id IN $enrolled_courses ORDER BY G.title";


    $result = mysql_query($sql, $db);
    if ($result){
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
                    // retrieve the last posted date/time from this blog
                    $sql = "SELECT MAX(date) AS date FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOGS_GROUP." AND owner_id={$row['group_id']}";
                    $date_result = mysql_query($sql, $db);
                    $row2 = mysql_fetch_assoc($date_result);					
                    $last_updated = ' - ' . _AT('last_updated', AT_date(_AT('forum_date_format'), $row2['date'], AT_DATE_MYSQL_DATETIME));
                
                    $link_title = $row['title'];
                    $news[] = array('time'=>$row2['date'], 
                    'object'=>$row, 
                    'alt'=>_AT('blogs'),
                    'course'=>$system_courses[$row['course_id']]['title'],
                    'thumb'=>'images/home-blogs_sm.png', 
                    'link'=>'<a href="bounce.php?course='.$row['course_id'].'&p='.urlencode('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$row['group_id']).'"'.
                      (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'blog_posts.title').'"' : '') .'>'. 
                      AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'blog_posts.title') .'</a>');
                }
            }
        }
    }
    return $news;
}

?>
