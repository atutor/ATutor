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
    
    /**
     * This file is used to send emails for sharing calendars.
     */    
        $_user_location = 'public';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    if (!$_SESSION['valid_user']) {
        require(AT_INCLUDE_PATH.'header.inc.php');
        $info = array('INVALID_USER', $_SESSION['course_id']);
        $msg->printInfos($info);
        require(AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }
    if ($_POST['cancel']) {
        //user pressed cancel button
        header('Location: index.php');
        exit;
    } else if ($_POST['submit']) {
        $missing_fields = array();
        
        if ($_POST['to'] == 2 && isset($_POST['selection_error'])) {
            $msg->addError('NO_RECIPIENTS');
        }
        
        //Verify input fields
        if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
            $missing_fields[] = _AT('to');
        }
        
        if ($_POST['to'] == 3 && $_POST['emails'] == '') {
            $missing_fields[] = 'email';        
        }

        if ($missing_fields) {
            $missing_fields = implode(', ', $missing_fields);
            $msg->addError(array('EMPTY_FIELDS', $missing_fields));
        }
        
        if ($_POST['to'] == 3 && $_POST['emails'] != '') {
            if( filter_var($_POST['emails'], FILTER_VALIDATE_EMAIL) ) {
            
            } else {
                $msg->addError('INVALID_EMAIL');
            }
        }
        
        if ($_POST['to'] == 1) {

            $sql    = "SELECT * FROM %smembers WHERE member_id IN (SELECT member_id FROM %scourse_enrollment WHERE status=%d and course_id=%d and member_id <> %d )";
            $nowrow = queryDB($sql,array(TABLE_PREFIX, TABLE_PREFIX, AT_STATUS_STUDENT, $_SESSION['course_id'], $_SESSION['member_id']));
            
            if (count($norow) < 1) {
                $msg->addError('NO_RECIPIENTS');
            }
        }
        
        if (!$msg->containsErrors()) {
            if ($_POST['to'] == 1) {
                // choose all members associated with course
                $sql    = "SELECT * FROM %smembers WHERE member_id IN (SELECT member_id FROM %scourse_enrollment WHERE status=%d and course_id=%d and member_id <> %d)";                                   
                $rows_recipients = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, AT_STATUS_STUDENT, $_SESSION['course_id'], $_SESSION['member_id']));
                
            } else if ($_POST['to'] == 2) {
                // choose particular login
               $sql     = "SELECT * FROM %smembers WHERE member_id = %d";
               $rows_recipients = queryDB($sql, array(TABLE_PREFIX, $_POST['selection']));
            } else {
                //user entered email address
            }
            
            require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
            $mail = new ATutorMailer;
            
            if ($_POST['to'] == 1 || $_POST['to'] == 2) {
                foreach($rows_recipients as $row){
                    $mail->AddBCC($row['email']);
                }        
            } else {
                $mail->AddBCC($_POST['emails']);
            }
            
            if (isset($_POST['subject']) && $_POST['subject'] != '') {
                $calname = $_POST['subject'];
            } else {
                $calname = _AT('calendar_of') . " " . get_display_name($_SESSION['member_id']);
            }
            
            $body      = get_display_name($_SESSION['member_id']) . _AT('calendar_email_part1') . '"' . $calname . 
                         '"' . _AT('calendar_email_part2');
                         

            $sql       = "SELECT * FROM %smembers WHERE member_id = %d";
            $rows_members    = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
            
            $fromemail = $_config['contact_email'];
            
            foreach($rows_members as $row){
                $fromemail = $row['email'];
            }
            
            $body .= AT_BASE_HREF . "mods/_standard/calendar/index_public.php?mid=".
                    urlencode(base64_encode($_SESSION['member_id'])) . "&email=1&cid=".
                    $_SESSION['course_id'] . "&calname=" . urlencode($calname);
                
            $mail->From     = $fromemail;
            $mail->FromName = $_config['site_name'];
            $mail->AddAddress($fromemail);
            $mail->Subject = $stripslashes(_AT('calendar_mail_title'));
            $mail->Body    = $body;

            if (!$mail->Send()) {
               //echo 'There was an error sending the message';
               $msg->printErrors('SENDING_ERROR');
               exit;
            }
            
            /*//For testing email
            $to      = 'herat_000@yahoo.co.in';
            $subject = $stripslashes(_AT("calendar_mail_title"));
            $message = $body;
            $headers = 'From: abc@gmail.com' . "\r\n" .
                'Reply-To: abc@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            
            mail($to, $subject, $message, $headers);*/
            
            unset($mail);
            
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
            header('Location: index.php');
            exit;
        }
    }
    require(AT_INCLUDE_PATH.'header.inc.php');
?>

<style type="text/css">
    .fc-forme-hide{
        display:none;
    }
</style>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<div class="input-form">
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo  _AT('to'); ?><br />
        
        <input type="radio" name="to" value="1" id="all" <?php if( $_POST['to'] == 1 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="all"><?php echo _AT('calendar_sendall'); ?></label>
        
        <input type="radio" name="to" value="2" id="list" <?php if( $_POST['to'] == 2 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').removeClass('fc-forme-hide');" />
        <label for="list"><?php echo _AT('calendar_sellist'); ?></label>
        
        <input type="radio" name="to" value="3" id="manual" <?php if( $_POST['to'] == 3 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').removeClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="manual"><?php echo _AT('calendar_manemail'); ?></label>
    </div>
    
    <div class="row">
        
        <span id="emails" <?php if( $_POST['to'] != 3 ) echo "class='fc-forme-hide'"; ?> >
        <label for="emails1"> <?php echo _AT('calendar_mailtxt'); ?>: </label>
        <input type="text" id="emails1" name="emails" value="<?php echo strip_tags($_POST['emails']); ?>"/>
        </span>
        
        <span id="selection" <?php if( $_POST['to'] != 2 ) echo "class='fc-forme-hide'"; ?>>
               
            <?php
                $sql    = "SELECT login,member_id FROM %smembers WHERE member_id IN (SELECT member_id FROM %scourse_enrollment WHERE status=%d and course_id=%d and member_id <> %d)";
                $rows_recipients = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, AT_STATUS_STUDENT, $_SESSION['course_id'], $_SESSION['member_id']));
                $norow  = count($rows_recipients);
                
                if ($norow > 0) {
                    echo "<label for='selection1'>" . _AT('calendar_membrselect') . ": </label>";
                    echo "<select name='selection' id='selection1'>";
                    foreach($rows_recipients as $row){
                        echo "<option value='" . $row['member_id'] . "'>" . $row['login'] . "</option>";                    
                    }
                    echo "</select>";
                } else {
                    echo "<div id='selection_error'>" . _AT('calendar_no_one_else') . "</div>";
                    echo "<input type='hidden' name='selection_error' value='only one' />";
                }
            ?>

        </span>
    
    </div>

    <div class="row">
        <label for="subject"> <?php echo _AT('calendar_titletxt'); ?> </label><br />
        <input type="text" name="subject" size="40" id="subject" value="<?php echo strip_tags($_POST['subject']); ?>" /><br/>
        <label>
            <?php echo _AT('calendar_optional_fld') . get_display_name($_SESSION['member_id']) . '"'; ?> 
        </label>
    </div>    

    <div class="row buttons">
        <input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
        <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
    </div>
</div>
</form>

<?php 
    require(AT_INCLUDE_PATH.'footer.inc.php');
?>