<?php 
    @session_start();
    if (!defined('AT_INCLUDE_PATH')) { exit; }

    /**
     * Extending the test dates to make them accessible to Calendar Module
     * @param     :    Course id, Member id
     * @return    :    array (test start and end dates) in format that can be used by fullcalendar
     * @author    :    Anurup Raveendran, Herat Gandhi
     */
    function tests_extend_date($member_id, $course_id) {

        $tests = array();
        
        // get course title

        $sql = "SELECT title  FROM %scourses  WHERE course_id = %d";               
        $row       = queryDB($sql,array(TABLE_PREFIX, $course_id), TRUE);
        
        $course_title = $row['title'];

        $sql = "SELECT title,test_id,start_date,end_date FROM %stests WHERE course_id = %d";
        $rows_tests    = queryDB($sql,array(TABLE_PREFIX, $course_id));
            
        if (count($rows_tests) > 0) {
            $index = 0;
            foreach($rows_tests as $row){
                if (strpos( $row['start_date'] . '', '0000-00-00' ) === false) {
                    $unix_ts = strtotime($row['start_date']);
                    $time    = date('h:i A',$unix_ts);
                    $tests[$index] = array(
                                "id"        => rand(20000,25000).'',
                                "title"     => _AT('calendar_test_start') . $row['title'],
                                "start"     => $row['start_date'],
                                "end"       => $row['start_date'],
                                "allDay"    => false,
                                "color"     => 'lime',
                                "textColor" => 'black',
                                "editable"=>false                        
                            );            
                    $unix_ts = strtotime($row['end_date']);        
                    $time    = date('h:i A',$unix_ts);
                    $index++;
                }
                if (strpos( $row['end_date'] . '', '0000-00-00' ) === false) {        
                    $tests[$index] = array(
                                "id"        => rand(20000,25000) . '',
                                "title"     => _AT('calendar_test_end') . $row['title'],
                                "start"     => $row['end_date'],
                                "end"       => $row['end_date'],
                                "allDay"    => false,
                                "color"     => 'purple',
                                "textColor" => 'white',
                                "editable"  => false                          
                            );
                    $index++;
                }
            }
        }
        
        return $tests;
    }
?>
