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
     * This file provides calendar interface for shared calendar.
     */
    
    //It is possible that user is not logged in.
    $_user_location = 'public';
    
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $global_js_vars = "
        var calendar_prv_mnth       = '" . _AT('calendar_prv_mnth') . "';
        var calendar_prv_week       = '" . _AT('calendar_prv_week') . "';
        var calendar_prv_day        = '" . _AT('calendar_prv_day') . "';
        var calendar_nxt_mnth       = '" . _AT('calendar_nxt_mnth') . "';
        var calendar_nxt_week       = '" . _AT('calendar_nxt_week') . "';
        var calendar_nxt_day        = '" . _AT('calendar_nxt_day') . "';
        var mid                     = '" . base64_decode(urldecode($_GET['mid'])) . "';
        var cid                     = '" . $_GET['cid'] . "';
    ";
    $_custom_head .= 
    '<script type="text/javascript">' . $global_js_vars . '</script>
    <script type="text/javascript" src="' . AT_BASE_HREF .
     'mods/_standard/calendar/js/index_public.js"></script>';

    //Get member id from request if it is not set then display default message
    if (!isset($_GET['mid'])) {
        require(AT_INCLUDE_PATH.'header.inc.php'); 
        echo _AT('calendar_pub_def_msg');
        require(AT_INCLUDE_PATH.'footer.inc.php'); 
        exit;
    }

   // global $db;
    //User requested to bookmark calendar
    if (isset($_GET['bookm']) && $_GET['bookm'] == 1) {
        if (isset($_SESSION['member_id'])) {
            //Check whether user already bookmarked the calendar

            $sql    = "SELECT * FROM %scalendar_bookmark WHERE memberid=%d AND ownerid=%d AND courseid=%d";
            $row_bookmarks = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], base64_decode(urldecode($_GET['mid'])), $_GET['cid']), TRUE);
            
            //If user has already bookmarked calendar then display error
            
            if(count($row_bookmarks) == 0 ){
                //Not bookmarked so bookmark now

                $sql = "INSERT INTO %scalendar_bookmark VALUES (%d,%d,%d,'%s')";
                queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], base64_decode(urldecode($_GET['mid'])), $_GET['cid'], $_GET['calname']));

            } else if ( count($row_bookmarks) > 0) {
                $msg->addError('ALREADY_BOOKMARKED');
            }

            header('Location: index.php');
            exit;
        } else {
            //add in sql
            $msg->addError('LOG_IN_FIRST');
            header('Location: '.AT_BASE_HREF.'login.php');
            exit;
        }
    } else if (isset($_GET['del']) && $_GET['del'] == 1) {
        //Delete the bookmark
        if (isset($_SESSION['member_id'])) {

            $sql = "DELETE FROM %scalendar_bookmark WHERE memberid=%d AND ownerid=%d";
            queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], base64_decode(urldecode($_GET['mid']))));
            
            header('Location: index.php');
            exit;
        }
    } else if (isset($_GET['editname']) 
                && $_GET['editname'] == 1 
                && trim($_GET['calname']) != "") {
        //Change name of bookmark
        if (isset($_SESSION['member_id'])) {

            $sql = "UPDATE %scalendar_bookmark SET calname='%s' WHERE memberid=&d AND ownerid=%d";
            queryDB($sql, array(TABLE_PREFIX, $_GET['calname'], $_SESSION['member_id'], base64_decode(urldecode($_GET['mid']))));
            
            header('Location: index.php');
            exit;
        }
    }

    require(AT_INCLUDE_PATH.'header.inc.php');
?>
<div style="left:50%; z-index:20000; position:absolute; top:50%" id="loader">
    <img src="mods/_standard/calendar/img/loader.gif" alt="Loading" />
</div>

<?php 
    if (isset($_GET['email']) && $_GET['email'] == 1 && isset($_SESSION['member_id'])) {
?>
<div style="float:right;width:20%" class="box">
    <fieldset>
        <legend><h4><?php echo _AT('calendar_options'); ?></h4></legend>
        <ul class="social_side_menu">
        <li>
            <a  href='mods/_standard/calendar/index_public.php?mid=<?php echo $_GET['mid'];?>&cid=<?php echo $_GET['cid'];?>&bookm=1&calname=<?php echo $_GET['calname']; ?>'>
                <?php echo _AT('calendar_bookmark_this'); ?>
            </a> 
        </li>
        </ul>
    </fieldset>
</div>
<?php
    } else if (isset($_SESSION['member_id'])) {
?>
<div style="float:right;width:20%" class="box">
    <fieldset>
        <legend><h4><?php echo _AT('calendar_options'); ?></h4></legend>
        <ul class="social_side_menu">
        <li>
            <form action="mods/_standard/calendar/index_public.php" method="get" >
                <label for="calname"><?php echo _AT('calendar_edit_title'); ?></label>
                <br/>
                <input type="hidden" value="<?php echo $_GET['mid'];?>" name="mid" />
                <input type="hidden" value="<?php echo $_GET['cid'];?>" name="cid" />
                <input type="hidden" value="1" name="editname" />
                <input type="text" size="12" value="<?php echo $_GET['calname']; ?>" name="calname" id="calname" />
                <br/>
                &nbsp;&nbsp;
                <input type="submit" value="<?php echo _AT('calendar_save'); ?>" />
            </form>
        </li>
        <li>
            <a  href='mods/_standard/calendar/index_public.php?mid=<?php echo $_GET['mid'];?>&cid=<?php echo $_GET['cid'];?>&del=1&calname=<?php echo $_GET['calname']; ?>'>
                <?php echo _AT('calendar_del_bookmark'); ?>
            </a>
        </li>            
        </ul>
    </fieldset>
</div>
<?php        
    } else {
?>
<div style="float:right;width:20%" class="box">
    <fieldset>
        <legend><h4><?php echo _AT('calendar_options'); ?></h4></legend>
        <ul class="social_side_menu">
        <li>
            <?php echo _AT("calendar_public_note1")." <a href= '".AT_BASE_HREF."login.php'>"._AT("calendar_public_note2")."</a> "._AT("calendar_public_note3");
            ?>    
        </li>
        </ul>
    </fieldset>
</div>
<?php
    }
?>
<?php $_custom_css = $_base_path . 'mods/_standard/calendar/lib/fullcalendar/fullcalendar-theme.css'; // use a custom stylesheet ?>

<script type="text/javascript" 
    src="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/fullcalendar-theme.js">
</script>

<link href= "<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/fullcalendar-theme.css" rel="stylesheet" type="text/css"/>

<style type="text/css">
    #calendar {
        width: 75%;
        margin: 0 auto;
    }
</style>

<div style="float:left" id="calendar">
</div>
    
<?php
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>