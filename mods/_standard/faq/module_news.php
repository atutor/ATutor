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
function faq_news() {
    global $db, $enrolled_courses, $system_courses;
    $news = array();

    if ($enrolled_courses == ''){
        return $news;
    } 

    $sql = "SELECT * FROM %sfaq_topics T INNER JOIN %sfaq_entries E ON T.topic_id = E.topic_id WHERE T.course_id IN %s ORDER BY E.revised_date DESC";
    $rows_faqs = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $enrolled_courses));
    
    if(count($rows_faqs) > 0){
        foreach($rows_faqs as $row){
            $news[] = array('time'=>$row['revised_date'], 
            'alt'=>_AT('faq'),'object'=>$row,
            'course'=>$system_courses[$row['course_id']]['title'], 
            'thumb'=>'images/home-faq_sm.png', 
            'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('mods/_standard/faq/index.php#'.$row['entry_id']).'"'.
            (strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($row['question'], 'faqs.question').'"' : '') .'>'. 
            AT_print(validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'faqs.question') .'</a>');
        }
    }
    return $news;
}

?>