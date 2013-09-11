<?php
    @session_start();
    if (!defined('AT_INCLUDE_PATH')) { exit; }

    /**
     * Extending the assignment dates to make them accessible to Calendar Module
     * @param     :    Course id, Member id
     * @return    :    array (assignment due and cut off dates) in format that can be used by fullcalendar
     * @author    :    Anurup Raveendran, Herat Gandhi
     */
    function assignments_extend_date($member_id, $course_id) {

        global $db;
        $assignments = array();
        
        // get course title
        $sql = "SELECT title 
                FROM ". TABLE_PREFIX . "courses 
                WHERE course_id = '". $course_id . "'";
                
        $result       = mysql_query($sql,$db) or die(mysql_error());
        $row          = mysql_fetch_assoc($result);
        $course_title = $row['title'];
        
        $sql = "SELECT assignment_id,title,date_due,date_cutoff
                FROM " . TABLE_PREFIX . "assignments
                WHERE course_id = '" . $course_id . "'";

        $result     = mysql_query($sql,$db) or die(mysql_error());
        $row_count  = mysql_num_rows($result);

        if ($row_count > 0) {
            $index = 0;
            while ($row = mysql_fetch_assoc($result)) {
                
                $assignment_id = $row['assignment_id'];
                $unix_ts       = strtotime($row['date_due']);
                $time          = date('h:i A',$unix_ts);
                
                if (strpos( $row['date_due'] . '', '0000-00-00' ) === false) {
                    $assignments[$index] = array(
                                    "id"        => rand(5000,9000) . '',
                                    "title"     => _AT('calendar_assignment_due') . $row['title'],
                                    "start"     => $row['date_due'],
                                    "end"       => $row['date_due'],
                                    "allDay"    => false,
                                    "color"     => 'yellow',
                                    "textColor" => 'black',
                                    "editable"  => false
                                );
                                 
                    $unix_ts = strtotime($row['date_cutoff']);                  
                    $time    = date('h:i A',$unix_ts);
                    $index++;
                }
                if (strpos($row['date_cutoff'] . '', '0000-00-00' ) === false) {
                    $assignments[$index] = array(
                                "id"        => rand(5000,9000).'',
                                "title"     => _AT('calendar_assignment_cut') . $row['title'],
                                "start"     => $row['date_cutoff'],
                                "end"       => $row['date_cutoff'],
                                "allDay"    => false,
                                "color"     => 'red',
                                "textColor" => 'white',
                                "editable"  => false 
                            );            
                    $index++;
                }
            }
        }    
        return $assignments;
    }
?>
