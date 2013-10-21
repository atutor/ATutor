<?php

function forums_delete($course) {

	$sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE course_id=$course";
	$rows_forums = queryDB($sql, array(TABLE_PREFIX, $course));
	
	if(count($rows_forums) > 0){
        foreach($rows_forums as $forum){

            $forum_id = $forum['forum_id'];

            $sql = "SELECT COUNT(*) AS cnt FROM %sforums_courses WHERE forum_id=%d";
            $row = queryDB($sql, array(TABLE_PREFIX, $forum_id), TRUE);

            if ($row['cnt'] == 1) {

                $sql	= "SELECT post_id FROM %sforums_threads WHERE forum_id=%d";
                $rows_threads = queryDB($sql, array(TABLE_PREFIX, $forum_id));
                if(count($rows_threads) > 0){
                    foreach($rows_threads as $row){

                        $sql	 = "DELETE FROM %sforums_accessed WHERE post_id=%d";
                        $result2 = queryDB($sql, array(TABLE_PREFIX, $row['post_id']));

                    }
                }
                $sql	= "DELETE FROM %sforums_subscriptions WHERE forum_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

                $sql    = "DELETE FROM %sforums_threads WHERE forum_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

                $sql    = "DELETE FROM %sforums WHERE forum_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum_id));
            
                $sql = "DELETE FROM %sforums_courses WHERE forum_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

                $sql = "DELETE FROM %sforums_groups WHERE forum_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

            } else if ($row['cnt'] > 1) {
                // this is a shared forum:
                // debug('unsubscribe all the students who will not be able to access this forum anymore.');
                $sql     = "SELECT course_id FROM %sforums_courses WHERE forum_id=%d AND course_id <> %d";
                $rows_cforums = queryDB($sql, array(TABLE_PREFIX, $forum['forum_id'], $course));
            
                if(count($rows_cforums) > 0){
                    foreach($rows_cforums as $row2){
                        $courses[] = $row2['course_id'];
                    }
                }
                $courses_list = implode(',', $courses);

                // list of all the students who are in other courses as well
                $sql     = "SELECT member_id FROM %scourse_enrollment WHERE course_id IN (%s)";
                $rows_enrolled = queryDB($sql, array(TABLE_PREFIX, $courses_list));	
                if(count($rows_enrolled) > 0){
                    foreach($rows_enrolled as $row2){		
                        $students[] = $row2['member_id'];
                    }
                }
                $students_list = implode(',', $students);
            
                if (isset($students_list)) {
            
                    // remove the subscriptions
                    $sql	= "SELECT post_id FROM %sforums_threads WHERE forum_id=%d]";
                    $rows_threads = queryDB($sql, array(TABLE_PREFIX, $forum['forum_id']));
                    if(count($rows_threads) > 0){
                        foreach($rows_threads as $row2){
                            $sql	 = "DELETE FROM %sforums_accessed WHERE post_id=%d AND member_id NOT IN (%s)";
                            $result3 = queryDB($sql, array(TABLE_PREFIX, $row2['post_id'], $students_list));
                        }
                    }

                    $sql	 = "DELETE FROM %sforums_subscriptions WHERE forum_id=%d AND member_id NOT IN (%d)";
                    $result3 = queryDB($sql, array(TABLE_PREFIX, $forum['forum_id'], $students_list));
                }

                $sql = "DELETE FROM %sforums_courses WHERE forum_id=%d AND course_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $forum['forum_id'], $course));
            }
        }
	}
	$sql = "OPTIMIZE TABLE %sforums_threads";
	$result = queryDB($sql, array(TABLE_PREFIX));
}

?>