<?php
    @session_start();
    if (!defined('AT_INCLUDE_PATH')) { exit; }

    /**
     * Extending the course dates to make them accessible to Calendar Module
     * @param     :    Course id, Member id
     * @return    :    array (course release and end dates) in format that can be used by fullcalendar
     * @author    :    Anurup Raveendran, Herat Gandhi
     */
    function courses_extend_date($member_id, $course_id) {
        
        global $db;
        $course = array();     
        
        //create array of enrolled or pending enrollment courses for current user
        $sql = "SELECT course_id FROM %scourse_enrollment WHERE member_id = %d";
        $rows_enrolled = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
        $enrolled_count = count( $rows_enrolled);
        
        foreach($rows_enrolled as $row){
            $c++;
            if($c == $enrolled_count){
                 $enrolled .= $row['course_id'];
            }else{
                 $enrolled .= $row['course_id'].",";
            }
        }
        
        // get the course details along with the relevant dates

        $sql = "SELECT * FROM %scourses WHERE course_id =%d";     
        $rows_courses = queryDB($sql, array(TABLE_PREFIX, $course_id));
        $row_count  = count($rows_courses);    

        if ($row_count > 0) {
            $index = 0; 
            foreach($rows_courses as $row){           
                if(strpos( $row['release_date'].'', '0000-00-00' ) === false) {
                    $unix_ts = strtotime($row['release_date']);
                    $time    = date('h:i A',$unix_ts);
                    // release_date
                    $course[$index] = array(
                                "id"        => rand(10000,15000) . '',
                                "title"     => _AT('calendar_course_start') . $row['title'],
                                "start"     => $row['release_date'],
                                "end"       => $row['release_date'],
                                "allDay"    => false,
                                "color"     => 'green',
                                "textColor" => 'white',
                                "editable"  => false                        
                            );                          
                    $index++;
                }
                //end date
                if (strpos( $row['end_date'].'', '0000-00-00' ) === false) {
                    $unix_ts = strtotime($row['end_date']);
                    $time    = date('h:i A',$unix_ts);
                    $course[$index] = array(
                            "id"        => rand(10000,15000).'',
                            "title"     => _AT('calendar_course_end') . $row['title'],
                            "start"     => $row['end_date'],
                            "end"       => $row['end_date'],
                            "allDay"    => false,
                            "color"     => 'maroon',
                            "textColor" => 'white',
                            "editable"  => false 
                        );
                    $index++;
                }
            }
        }        
        return $course;
    }
?>
