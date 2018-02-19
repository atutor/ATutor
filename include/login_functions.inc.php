<?php
/***********************************************************************/
/* ATutor                                                              */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute                                          */
/* http://atutor.ca                                                    */
/*                                                                     */
/* This program is free software. You can redistribute it and/or       */
/* modify it under the terms of the GNU General Public License         */
/* as published by the Free Software Foundation.                       */
/***********************************************************************/
// $Id$



// For security reasons the token has to be generated anew before each login attempt.
// The entropy of SHA-1 input should be comparable to that of its output; in other words, the more randomness you feed it the better.

/***
* Remove comments below to enable a remote login form.
*/
if (isset($_POST['token']))
{
    $_SESSION['token'] = $_POST['token'];
}
else
{
    if (!isset($_SESSION['token']))
        $_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));
}

/***
* Add comments 2 lines below to enable a remote login form.
*/
//if (!isset($_SESSION['token']))
//    $_SESSION['token'] = sha1(mt_rand() . microtime(TRUE));

if (isset($_GET['course'])) {
    $_GET['course'] = intval($_GET['course']);
} else {
    $_GET['course'] = 0;
}

// check if we have a cookie
if (!$msg->containsFeedbacks()) {
    if (isset($_COOKIE['ATLogin'])) {
        $cookie_login = $_COOKIE['ATLogin'];
    }
    if (isset($_COOKIE['ATPass'])) {
        $cookie_pass  = $_COOKIE['ATPass'];
    }
}

//garbage collect for maximum login attempts table
if (rand(1, 100) == 1){
    queryDB("DELETE FROM %smember_login_attempt WHERE expiry < '%s'", array(TABLE_PREFIX, time()));
}

if (isset($cookie_login, $cookie_pass) && !isset($_POST['submit'])) {
    /* auto login */
    $this_login        = $cookie_login;
    $this_password    = $cookie_pass;
    $auto_login        = 1;
    $used_cookie    = true;
} else if (isset($_POST['submit'])) {
    /* form post login */
    $this_password = $_POST['form_password_hidden'];
    $this_login        = $_POST['form_login'];
    $auto_login        = isset($_POST['auto']) ? intval($_POST['auto']) : 0;
    $used_cookie    = false;
} else if (isset($_POST['submit1'])) {
    /* form post login on autoenroll registration*/
    $this_password = $_POST['form1_password_hidden'];
    $this_login        = $_POST['form1_login'];
    $auto_login        = isset($_POST['auto']) ? intval($_POST['auto']) : 0;
    $used_cookie    = false;
}

if (isset($this_login, $this_password)) {
    if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
        session_regenerate_id(TRUE);
    }


    if ($_GET['course']) {
        $_POST['form_course_id'] = intval($_GET['course']);
    } else {
        $_POST['form_course_id'] = intval($_POST['form_course_id']);
    }
    $this_login    = $addslashes($this_login);
    $this_password = $addslashes($this_password);

    //Check if this account has exceeded maximum attempts
    $rows = queryDB("SELECT login, attempt, expiry FROM %smember_login_attempt WHERE login='%s'", array(TABLE_PREFIX, $this_login), TRUE);

    if ($rows && count($rows) > 0){
        list($attempt_login_name, $attempt_login, $attempt_expiry) = $rows;
    } else {
        $attempt_login_name = '';
        $attempt_login = 0;
        $attempt_expiry = 0;
    }
    if($attempt_expiry > 0 && $attempt_expiry < time()){
        //clear entry if it has expired
        queryDB("DELETE FROM %smember_login_attempt WHERE login='%s'", array(TABLE_PREFIX, $this_login));
        $attempt_login = 0;
        $attempt_expiry = 0;
    }

    if ($used_cookie) {
        #4775: password now store with salt
        $rows = queryDB("SELECT password, last_login FROM %smembers WHERE login='%s'", array(TABLE_PREFIX, $this_login), TRUE);
        $cookieRow = $rows;
        $saltedPassword = hash('sha512', $cookieRow['password'] . hash('sha512', $cookieRow['last_login']));
        $row = queryDB("SELECT member_id, login, first_name, second_name, last_name, preferences,password AS pass, language, status, last_login FROM %smembers WHERE login='%s' AND '%s'='%s'", array(TABLE_PREFIX, $this_login, $saltedPassword, $this_password), TRUE);
    } else {
        $row = queryDB("SELECT member_id, login, first_name, second_name, last_name, preferences, language, status, password AS pass, last_login FROM %smembers WHERE (login='%s' OR email='%s') AND SHA1(CONCAT(password, '%s'))='%s'", array(TABLE_PREFIX, $this_login, $this_login, $_SESSION['token'], $this_password), TRUE);
    }
    //$row = $rows;

    if($_config['max_login'] > 0 && $attempt_login >= $_config['max_login']){
        $msg->addError('MAX_LOGIN_ATTEMPT');
    } else if ($row['status'] == AT_STATUS_UNCONFIRMED) {
        $msg->addError('NOT_CONFIRMED');
    } else if ($row && $row['status'] == AT_STATUS_DISABLED) {
        $msg->addError('ACCOUNT_DISABLED');
    } else if (count($row) > 0) {
        $_SESSION['valid_user'] = true;
        $_SESSION['member_id']    = intval($row['member_id']);
        $_SESSION['login']        = $row['login'];
        if ($row['preferences'] == "")
            assign_session_prefs(unserialize(stripslashes($_config["pref_defaults"])), 1);
        else
            assign_session_prefs(unserialize(stripslashes($row['preferences'])), 1);
        $_SESSION['is_guest']    = 0;
        $_SESSION['lang']        = $row['language'];
        $_SESSION['course_id']  = 0;
        $now = date('Y-m-d H:i:s');

        if ($auto_login == 1) {
            $parts = parse_url($_base_href);
            // update the cookie.. increment to another 2 days
            $cookie_expire = time()+172800;
            // #4775, also look at pref_tab_functions.inc.php setAutoLoginCookie(). Same technique.
            $saltedPassword = hash('sha512', $row['pass'] . hash('sha512', $now));
            ATutor.setcookie('ATLogin', $this_login, $cookie_expire, $parts['path']);
            ATutor.setcookie('ATPass',  $saltedPassword,  $cookie_expire, $parts['path']);
        }

        $_SESSION['first_login'] = false;
        if ($row['last_login'] == null || $row['last_login'] == '' || is_null($row['last_login'])
            || $_SESSION['prefs']['PREF_MODIFIED']!==1) {
            $_SESSION['first_login'] = true;
        }

        queryDB("UPDATE %smembers SET creation_date=creation_date, last_login='%s' WHERE member_id=%d", array(TABLE_PREFIX, $now, $_SESSION['member_id']));

        //clear login attempt on successful login
        queryDB("DELETE FROM %smember_login_attempt WHERE login='%s'", array(TABLE_PREFIX, $this_login));

        //if page variable is set, bring them there.
        if (isset($_POST['p']) && $_POST['p']!=''){
            header ('Location: '.urldecode($_POST['p']));
            exit;
        }

        $msg->addFeedback('LOGIN_SUCCESS');
        if(!isset($_REQUEST['en_id'])) {
            header('Location: bounce.php?course='.$_POST['form_course_id']);
            exit;
        }
    } else {
        // check if it's an admin login.
        $rows = queryDB("SELECT login, `privileges`, language FROM %sadmins WHERE login='%s' AND SHA1(CONCAT(password, '%s'))='%s' AND `privileges`>0", array(TABLE_PREFIX, $this_login, $_SESSION['token'], $this_password));

        if ($row = $rows[0]) {
            $sql = "UPDATE %sadmins SET last_login=NOW() WHERE login='%s'";
            $num_login = queryDB($sql, array(TABLE_PREFIX, $this_login));

            $_SESSION['login']        = $row['login'];
            $_SESSION['valid_user'] = true;
            $_SESSION['course_id']  = -1;
            $_SESSION['privileges'] = intval($row['privileges']);
            $_SESSION['lang'] = $row['language'];

            $sql = "UPDATE ".TABLE_PREFIX."admins SET last_login=NOW() WHERE login='$this_login'";
            write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', $num_login, $sql);

            //clear login attempt on successful login
            queryDB("DELETE FROM %smember_login_attempt WHERE login='%s'", array(TABLE_PREFIX, $this_login));

            $msg->addFeedback('LOGIN_SUCCESS');

            header('Location: admin/index.php');
            exit;

        } else {
            $expiry_stmt = '';
            $attempt_login++;
            if ($attempt_expiry==0){
                $expiry = (time() + LOGIN_ATTEMPT_LOCKED_TIME * 60);    //an hour from now
            } else {
                $expiry = $attempt_expiry;
            }
            queryDB("REPLACE INTO %smember_login_attempt SET attempt='%s', expiry='%s', login='%s'", array(TABLE_PREFIX, $attempt_login, $expiry, $this_login));
        }
        //Different error messages depend on the number of login failure.
        if ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==2){
            $msg->addError('MAX_LOGIN_ATTEMPT_2');
        } elseif ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==1){
            $msg->addError('MAX_LOGIN_ATTEMPT_1');
        } elseif ($_config['max_login'] > 0 && ($_config['max_login']-$attempt_login)==0){
            $msg->addError('MAX_LOGIN_ATTEMPT');
        } else {
            $msg->addError('INVALID_LOGIN');
        }
    }
}

if (isset($_SESSION['member_id'])) {
    queryDB("DELETE FROM %susers_online WHERE member_id=%d", array(TABLE_PREFIX, $_SESSION['member_id']));
}

$_SESSION['prefs']['PREF_FORM_FOCUS'] = 1;
?>
