<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /*//For testing
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');*/
    
    /*******
     * this function named [module_name]_cron is run by the global cron script at the module's specified
     * interval.
     */
    function calendar_cron() {
        require('includes/classes/events.class.php');
        require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
                    
        global $db;
        
        //Iterate through each member's preference
        $sql       = "SELECT * FROM " . TABLE_PREFIX . "calendar_notification WHERE 1=1";
        $result    = mysql_query($sql,$db);
        
        $event_obj = new Events();
                
        while ($row = mysql_fetch_assoc($result)) {
            //Send email only when preference is 1
            if ($row['status'] == 1) {
                $all_events = array();
                $mail = new ATutorMailer;
                
                //Get personal events
                $personal_events = $event_obj -> get_personal_events($row['memberid']);
                foreach ($personal_events as $event) {
                    $all_events[]  = $event;
                }
                
                //Get course events
                $sql_q      = "SELECT course_id FROM " . TABLE_PREFIX . "course_enrollment 
                                WHERE member_id = " . $row['memberid'];
                $result_q   = mysql_query($sql_q,$db);
                while ($row_q = mysql_fetch_assoc($result_q)) {
                    $course_events = $event_obj -> get_atutor_events($row['memberid'],$row_q['course_id']);
                    foreach ($course_events as $event) {
                        $all_events[]  = $event;
                    }
                }
                
                //Iterate through each event and keep only those events which will start tomorrow
                $email_msg = _AT('calendar_noti_mail_1') . "\n";
                $index = 1; 
                foreach ($all_events as $id => $event) {
                    if (strtotime(substr($event['start'],0,10)) == strtotime('tomorrow')) {
                        $email_msg .= _AT('calendar_noti_mail_2') . " #" . $index                       . " \n"; 
                        $email_msg .= _AT('calendar_noti_mail_3') . ": " . substr($event['start'],0,10) . " \n";
                        $email_msg .= _AT('calendar_noti_mail_4') . ": "   . substr($event['end'],0,10)   . " \n";
                        $email_msg .= _AT('calendar_noti_mail_5') . ": " . $event['title']              . " \n\n"; 
                        $index++;
                    }
                }
                
                //Send email using ATutor mailer
                $mail->From     = $_config['contact_email'];
                $mail->FromName = $_config['site_name'];
                $mail->AddAddress($_config['contact_email']);
                $mail->Subject = $stripslashes(_AT('calendar_noti_title'));
                $mail->Body    = $email_msg;
                
                $sql_email      = "SELECT email FROM " . TABLE_PREFIX . "members
                                    WHERE member_id = " . $row['memberid'];
                $result_email   = mysql_query($sql_email,$db);
                $row_email      = mysql_fetch_row($result_email);
                
                $mail->AddBCC($row_email[0]);
                $mail->Send();
                unset($mail);
                
                //For testing
                //echo "<br/>".$email_msg."<br/>".$row_email[0];               
            }
        }
    }
    //For testing
    //calendar_cron();
?>
