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
     * This file provides calendar interface.
     */
    $_user_location	= 'public';
    
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    
    if (!$_SESSION['valid_user']) {
        require(AT_INCLUDE_PATH.'header.inc.php');
        $info = array('INVALID_USER', $_SESSION['course_id']);
        $msg->printInfos($info);
        require(AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }
   // global $db;
    
    //Check if patch is installed or not
    require('includes/classes/events.class.php');
    
    $eventObj = new Events();
    if($eventObj->get_atutor_events($_SESSION['member_id'],$_SESSION['course_id']) == "error") {
        require(AT_INCLUDE_PATH.'header.inc.php');
        echo _AT('calendar_patch_error');
        require(AT_INCLUDE_PATH.'footer.inc.php');
        exit();
    }
    

    //Change status of email notifications
    if (isset($_GET['noti']) && $_GET['noti'] == 1) {
      /*  $sql = "UPDATE " . TABLE_PREFIX . "calendar_notification SET status = 1 WHERE 
                 memberid = " . $_SESSION['member_id'];
        mysql_query($sql,$db);
        */
        $sql = "UPDATE %scalendar_notification SET status = 1 WHERE memberid = %d";
        queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
        
    } else if (isset($_GET['noti']) && $_GET['noti'] == 0) {
    
      /*  $sql = "UPDATE " . TABLE_PREFIX . "calendar_notification SET status = 0 WHERE 
                 memberid = " . $_SESSION['member_id'];
        mysql_query($sql,$db);
        */
        $sql = "UPDATE %scalendar_notification SET status = 0 WHERE memberid = %d";
        queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
    }

    //Change view according to session value
    if (!isset($_SESSION['fc-viewname'])) {
        $view_name = '\'month\'';
    } else {
        $view_name = $_SESSION['fc-viewname'];
    }
    $session_view_on = 0;
    if (isset($_SESSION['fc-viewname'])) {
        $session_view_on = 1;
    }
    $global_js_vars = "
        var view_name               = $view_name;
        var mid               = '".$_SESSION['member_id']."';
        var calendar_tooltip_event  = '" . _AT('calendar_tooltip_event') . "';
        var calendar_prv_mnth       = '" . _AT('calendar_prv_mnth') . "';
        var calendar_prv_week       = '" . _AT('calendar_prv_week') . "';
        var calendar_prv_day        = '" . _AT('calendar_prv_day') . "';
        var calendar_nxt_mnth       = '" . _AT('calendar_nxt_mnth') . "';
        var calendar_nxt_week       = '" . _AT('calendar_nxt_week') . "';
        var calendar_nxt_day        = '" . _AT('calendar_nxt_day') . "';
        var calendar_tooltip_cell   = '" . _AT('calendar_tooltip_cell') . "';
        var calendar_form_title_def = '" . _AT('calendar_form_title_def') . "';
        var calendar_creat_e        = '" . _AT('calendar_creat_e') . "';
        var calendar_cancel_e       = '" . _AT('calendar_cancel_e') . "';
        var calendar_del_e          = '" . _AT('calendar_del_e') . "';
        var calendar_edit_e         = '" . _AT('calendar_edit_e') . "';
        var calendar_uneditable     = '" . _AT('calendar_uneditable') . "';
        var session_view_on         = " . $session_view_on . ";";
        if ($session_view_on == 1) {
            $global_js_vars .= "
            var fc_year                 = " . $_SESSION['fc-year'] . ";
            var fc_month                = " . $_SESSION['fc-month'] . ";
            var fc_date                 = " . $_SESSION['fc-date'] . ";
            ";
        }
    $_custom_head .= 
    '<script language="javascript" type="text/javascript">' . $global_js_vars . '</script>
    <script language="javascript" type="text/javascript" src="' . AT_BASE_HREF .
     'mods/_standard/calendar/js/index_mystart.js"></script>';
    $_custom_css = $_base_path . 'mods/_standard/calendar/lib/fullcalendar/fullcalendar-theme.css'; // use a custom stylesheet
    require(AT_INCLUDE_PATH.'header.inc.php');
    
    // Get a list of this user's enrolled courses
    $sql = "SELECT course_id FROM %scourse_enrollment WHERE member_id=%d";
    $rows_enrolled = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
    
    foreach($rows_enrolled as $row){
        $courses[] = $row['course_id'];
    }
    debug_to_log($courses);
?>
<!-- Loader wheel to indicate on-going transfer of data -->

<script language="javascript" type="text/javascript">

    var userid = "<?php echo $_SESSION['member_id']; ?>";
    </script>
<div style="left:50%; z-index:20000; position:absolute; top:50%" id="loader">
    <img src="mods/_standard/calendar/img/loader.gif" alt="Loading" /> 
</div>
<!-- Right side calendar menu -->
<div style="float:right;width:20%" class="box">
    <fieldset>
        <legend>
            <h4>
                <?php echo _AT('calendar_options'); ?>
            </h4>
        </legend>
        <ul class="social_side_menu">
            <li>
                <a  href="mods/_standard/calendar/file_import.php"><?php echo _AT('calendar_import_file')?></a> 
            </li>
            <li>
                <a id="export" href="mods/_standard/calendar/export.php"><?php echo _AT('calendar_export_file')?></a>
            </li>
            <li>
                <a  href="mods/_standard/calendar/send_mail.php"><?php echo _AT('calendar_share'); ?></a>
            </li>
            <li>
                <?php echo _AT('calendar_notification');?>:&nbsp;
                <?php
                    //Find current status of notification
                    $sql    = "SELECT * FROM %scalendar_notification WHERE memberid=%d";
                    $row_notification = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
                    $status = 0;  
                    
                    if(count($row_notification) == 0){
                   // if (is_null($result) || !$result) {
                        //Not any entry for user, make one default entry
                    /*    $sql = "INSERT INTO " . TABLE_PREFIX . "calendar_notification VALUES (".
                               $_SESSION['member_id'] . ",0)";
                        mysql_query($sql, $db);
                        */
                        $sql = "INSERT INTO %scalendar_notification VALUES (%d,0)";
                        queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
                        $status = 0;
                   // } else if (is_resource($result) && mysql_num_rows($result) > 0) {
                      } else if(count($row_notification) > 0){
                        //There is an entry in the table, find the value
                        //$row = mysql_fetch_row($result);
                        if ($row_notification['status'] == 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                    } else {
                        //Not any entry for user, make one default entry
                      /*  $sql = "INSERT INTO " . TABLE_PREFIX . "calendar_notification VALUES (".
                               $_SESSION['member_id'] . ",0)";
                        mysql_query($sql, $db);
                        */
                        $sql = "INSERT INTO %scalendar_notification VALUES (%d,0)";
                        queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
                        $status = 0;
                    }
                    //Put button to reflect current status
                    if ($status == 1) {
                        echo _AT('calendar_noti_on');
                        echo "<br/><a href='mods/_standard/calendar/index.php?noti=0'>" . _AT('calendar_noti_turn'). " " . _AT('calendar_noti_off') . "</a>";
                    } else {
                        echo _AT('calendar_noti_off');
                        echo "<br/><a href='mods/_standard/calendar/index.php?noti=1'>" . _AT('calendar_noti_turn'). " " . _AT('calendar_noti_on') . "</a>";
                    }
                ?>
            </li>
    <?php
        /**
         * Check if user has token for Google Account. If yes then display disconnect option
         * otherwise display connect option.
         */
        //$query = "SELECT * FROM ".TABLE_PREFIX."calendar_google_sync WHERE userid='".$_SESSION['member_id']."'";
       // $result = mysql_query($query,$db);
        $query = "SELECT * FROM %scalendar_google_sync WHERE userid=%d";
        $rows_gcals = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']));
                
        if(count($rows_gcals) > 0){
       // if (mysql_num_rows($result) > 0) {
            echo "<li><a href='mods/_standard/calendar/google_connect_disconnect.php?logout=yes' target='_blank'>".
                 _AT('calendar_disconnect_gcal') . "</a></li></ul></fieldset>";
            echo "<br/><fieldset><legend><h4>". _AT('calendar_gcals') . "</h4></legend>";
            include('google_calendarlist.php');
            echo "</fieldset>";
        } else {
            echo "<li><a href='mods/_standard/calendar/google_connect_disconnect.php' target='_blank'>".
                _AT('calendar_connect_gcal'). "</a></li></ul></fieldset>";
        }
    ?>
    <br/>
    <!-- Display color codes with description. -->
    <fieldset>
        <legend>
            <h4>
                <?php echo _AT('calendar_internal_events'); ?>
            </h4>
        </legend>
        <div class="fc-square fc-inline-block" style="background-color:rgb(51,102,204)"></div>
        <?php echo _AT('calendar_events_persnl'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:yellow"></div>
        <?php echo _AT('calendar_events_assign_due'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:red"></div>
        <?php echo _AT('calendar_events_assign_cut'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:green"></div>
        <?php echo _AT('calendar_events_course_rel'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:maroon"></div>
        <?php echo _AT('calendar_events_course_end'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:lime"></div>
        <?php echo _AT('calendar_events_test_start'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:purple"></div>
        <?php echo _AT('calendar_events_test_end'); ?>
        <br/>
    </fieldset>

    <?php
        /**
         * If user has bookmarked calendars then display them.
         */

        $query = "SELECT * FROM %scalendar_bookmark WHERE memberid = %d";
        $rows_bookmarks   = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']));
        
        if(count($rows_bookmarks) > 0){

    ?>
    <fieldset>
    <legend>
        <h4>
            <?php echo _AT('calendar_bookmarkd'); ?>
        </h4>
    </legend>
    <ul class="social_side_menu">
        <?php
            foreach($rows_bookmarks as $row){
            //while ($row = mysql_fetch_assoc($result)) {
        ?>
        <li>
            <a  href='mods/_standard/calendar/index_mystart.php?mid=<?php echo urlencode(base64_encode($row['ownerid'])); ?>&cid=<?php echo $row['courseid']; ?>&calname=<?php echo $row['calname']; ?>'><?php echo $row['calname'];?>
            </a>
        </li>
        <?php
            }
        ?>
        </ul>
    </fieldset>
    <?php
        }
    ?>
</div>

<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/fullcalendar-theme.js">
</script>

<style type='text/css'>
    #calendar {
        width: 75%;
        margin: 0 auto;
    }
</style>

<div id="dialog" class="event-dialog initial-hide" title="Create Event">
    <div id="dialog-inner">
       <table border="0" cellpadding="5">
        <tr> 
            <td>               
                <label for="name"><?php echo _AT('calendar_form_title'); ?></label>
            </td>
            <td>
                <input type="text" name="name" id="name" 
                onclick="if(this.value == '<?php echo _AT("calendar_form_title_def"); ?>') { this.value = ''; }" 
                onfocus="if(this.value == '<?php echo _AT("calendar_form_title_def"); ?>') { this.value = ''; }"/>
            </td>
        </tr>                
        <tr>
            <td>
                <label for="date-start"><?php echo _AT('calendar_form_start_d'); ?></label>
            </td>
            <td>
                <label id="lbl-start-time" for ="time-start"><?php echo _AT('calendar_form_start_t'); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="date-start" id="date-start" disabled="disabled">
            </td>
            <td>
                <input type="text" name="time" id="time-start" disabled="disabled">
            </td>
        </tr>
        <tr>
            <td>
                <label for="date-end"><?php echo _AT('calendar_form_end_d'); ?></label>
            </td>
            <td>
                <label id="lbl-end-time" for ="time-end"><?php echo _AT('calendar_form_end_t'); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="date" id="date-end">
            </td>
            <td>
                <select name="time" id="time-end">
                </select>
            </td>
        </tr>
        </table> 
        <input type="hidden" id="viewname" />
        <input type="hidden"  id="fc-emode" />
  </div>
</div>
<div id="dialog1" class="event-dialog initial-hide" title="Edit Event">
    <div id="dialog-inner1">
        <table border="0" cellpadding="5">
         <tr> 
            <td>               
                <label for="name1"><?php echo _AT('calendar_form_title'); ?></label>
            </td>
            <td>
                <input type="text" name="name" id="name1">
            </td>
        </tr>                
        <tr>
            <td>
                <label for="date-start1"><?php echo _AT('calendar_form_start_d'); ?></label>
            </td>
            <td>
                <label id="lbl-start-time1" for ="time-start1"><?php echo _AT('calendar_form_start_t'); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="date-start" id="date-start1">
            </td>
            <td id="container-fc-tm">
                <input type="text" name="time" id="time-start1" disabled="disabled">
            </td>
        </tr>
        <tr>
            <td>
                <label for="date-end1"><?php echo _AT('calendar_form_end_d'); ?></label>
            </td>
            <td>
                <label id="lbl-end-time1" for ="time-end1"><?php echo _AT('calendar_form_end_t'); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="date" id="date-end1">
            </td>
            <td>
                <select name="time" id="time-end1">
                </select>
            </td>
        </tr>    
        </table>
        <input type="hidden" id="viewname1" />
        <input type="hidden"  id="fc-emode1" /> 
        <input type="hidden" id="ori-name1" />              
    </div>
</div>

<div style="float:left" id="calendar">
</div>    
<?php
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>