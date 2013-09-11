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
        
        // get the course details along with the relevant dates
        $sql = "SELECT M.first_name, M.last_name, C.title, C.release_date, C.end_date
                FROM " . TABLE_PREFIX . "courses C , " . TABLE_PREFIX . "members M , ".
                TABLE_PREFIX . "course_enrollment E WHERE C.course_id = '".
                $course_id . "' AND M.member_id = '" . $member_id."' 
                AND E.member_id = M.member_id";   
        
        
        $result     = mysql_query($sql,$db) or die(mysql_error());
        $row_count  = mysql_num_rows($result);     
        
        if ($row_count > 0) {
            $index = 0; 
            $row   = mysql_fetch_assoc($result);
                            
            if(strpos( $row['release_date'].'', '0000-00-00' ) === false) {
                $unix_ts = strtotime($row['release_date']);
                $time    = date('h:i A',$unix_ts);
                // release_date
                $course[$index] = array(
                            "id"        => rand(10000,15000) . '',
                            "title"     => _AT('calendar_course_start') . $row['title']/*. 
                                           _AT('calendar_course_token')*/,
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
                        "title"     => _AT('calendar_course_end') . $row['title']/*.
                                       _AT('calendar_course_token')*/,
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
        return $course;
    }
?>
