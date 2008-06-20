<?php

/*
This belongs to the ATutor Mahara module page. It is called within an iframe or 
a new window from index.php and allows a user to access
his/her ePortfolio account on Mahara through their account on ATutor.

Login information for Mahara is passed using cookies (password encrypted in SHA1).
This is to avoid conflicting sessions between ATutor and Mahara from within
the same script.
*/

$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');


/*~~~~~~~~~~~~~few essentials copied from ATutor's vitals.inc.php~~~~~~~~~~~~*/

    /**** 0. start system configuration options block ****/
    error_reporting(0);
    if (!defined(AT_REDIRECT_LOADED)){
        include_once(AT_INCLUDE_PATH.'config.inc.php');
    }
    error_reporting(AT_ERROR_REPORTING);

    if (!defined('AT_INSTALL') || !AT_INSTALL) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
        header('Location: ' . $relative_path . 'install/not_installed.php');
        exit;
    }

    /*** 1. constants ***/
    if (!defined(AT_REDIRECT_LOADED)){
        require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
    }

    $db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
    if (!$db) {
        /* AT_ERROR_NO_DB_CONNECT */
        require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
        $err =& new ErrorHandler();
        trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
        exit;
    }
    if (!@mysql_select_db(DB_NAME, $db)) {
        require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
        $err =& new ErrorHandler();
        trigger_error('VITAL#DB connection established, but database "'.DB_NAME.'" cannot be selected.',
                        E_USER_ERROR);
        exit;
    }

    /* get config variables. if they're not in the db then it uses the installation default value in constants.inc.php */
    $sql    = "SELECT * FROM ".TABLE_PREFIX."config";
    $result = mysql_query($sql, $db);
    while ($row = mysql_fetch_assoc($result)) { 
        $_config[$row['name']] = $row['value'];
    }

    /***** 7. start language block *****/
        // set current language
        require(AT_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');
        $languageManager =& new LanguageManager();

        $myLang =& $languageManager->getMyLanguage();

        if ($myLang === FALSE) {
            echo 'There are no languages installed!';
            exit;
        }
        $myLang->saveToSession();
        if (isset($_GET['lang']) && $_SESSION['valid_user']) {
            if ($_SESSION['course_id'] == -1) {
                $myLang->saveToPreferences($_SESSION['login'], 1);	//1 for admin			
            } else {
                $myLang->saveToPreferences($_SESSION['member_id'], 0);	//0 for non-admin
            }
        }
        $myLang->sendContentTypeHeader();

        /* set right-to-left language */
        $rtl = '';
        if ($myLang->isRTL()) {
            $rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. eg. rtl_tree */
        }
    /***** end language block ****/

/*~~~~~~~~~~~~~~~~~~~~~~~end of vitals.inc.php~~~~~~~~~~~~~~~~~~~~~~*/





// Read Mahara login information from cookies passed by ATutor
$usr = array();
if (isset($_COOKIE['ATutor_Mahara'])) {
    foreach ($_COOKIE['ATutor_Mahara'] as $name => $value) {
        $usr[$name] = $value;

        // expire the cookie
        setcookie ("ATutor_Mahara[".$name."]", "", time() - 3600);
    }
    //expire the cookie array
    setcookie ("ATutor_Mahara", "", time() - 3600);
} else {
    echo 'Unable to detect cookies or the session has timed out.  Please check that cookies are enabled on your browser and try again.';
    exit;
}

// Get password from ATutor's database
$sql    = "SELECT password FROM ".TABLE_PREFIX."mahara WHERE username='".$usr["username"]."' AND SHA1(password)='".$usr["password"]."'";
$result = mysql_query($sql, $db);
if (!($row = @mysql_fetch_array($result))) {
    echo 'Incorrect login information. Please check with course instructor or administrator.';
    exit;
} else {
    $pwd = $row[0];

    if (isset($_config['mahara'])) {

        /****** Taken from index.php of /mahara  *****/
        define('INTERNAL', 1);
        define('PUBLIC', 1);
        define('MENUITEM', '');
        define (MAHARA_PATH, $_config['mahara']);
        require (MAHARA_PATH.'init.php');
        define('TITLE', get_string('home'));

        // Check if user exists in Mahara
        if (!(record_exists('usr', 'username', $usr["username"]))) {
            // Reconnect to ATutor Database and remove the record from the mahara table
            $db_atutor = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
            if (!$db_atutor) {
                /* AT_ERROR_NO_DB_CONNECT */
                require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
                $err =& new ErrorHandler();
                trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
                exit;
            }
            if (!@mysql_select_db(DB_NAME, $db_atutor)) {
                require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
                $err =& new ErrorHandler();
                trigger_error('VITAL#DB connection established, but database "'.DB_NAME.'" cannot be selected.',
                                E_USER_ERROR);
                exit;
            }

            // Delete record from ATutor database since it should not be there
            $sql = "DELETE FROM ".TABLE_PREFIX."mahara WHERE username='".$usr["username"]."'";

            $result = mysql_query($sql, $db_atutor);

            echo "Successfully synchronized user login with Mahara database. Please refresh the page from ATutor.";
            exit;
        }

        session_start();

        /*~~~~~~~~~~~copied from index.php of Mahara~~~~~~~~~~~~~~~*/
        // Check for whether the user is logged in, before processing the page. After
        // this, we can guarantee whether the user is logged in or not for this page.
        if (!$USER->is_logged_in()) {
            $lang = param_alphanumext('lang', null);
            if (!empty($lang)) {
                $SESSION->set('lang', $lang);
            }

            // Read login information
            $values['login_username'] = $usr["username"];
            $values['login_password'] = $pwd;
            $values['submit'] = "Login";
            $values['sesskey'] = "";
            $values['pieform_login'] = "";

            // login
            login_submit(null, $values);

            $adminpage = ($USER->get('admin')) ? 'admin/' : '';
        }

        /* Logged in session should be created.  Now redirect to the Mahara page
           and it should read from this session
         */
        header('Location: '.get_config('wwwroot').$adminpage);
        /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
    } else {
        echo 'You have incorrect config settings for the Mahara module.';
        exit;
    }
}


?>