<?php

/*
    This belongs to the ATutor+Mahara module. It is called from index.php when
    the user does not have a Mahara account associated with ATutor.  This script
    automatically creates a new Mahara account for the user and saves the login
    information with ATutor.  If the user already has a Mahara account, the script
    simply adds the login information to ATutor and reassigns an automatically
    generated password for Mahara.

    Most of the necessary code is copied and modified from init.php and
    register.php of Mahara.

    by: Boon-Hau Teh
*/

if (!defined('new_account')) { exit; }
if (!defined('AT_INCLUDE_PATH')) { exit; }

$_user_location	= 'public';

$sql = 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id="'.$_SESSION['member_id'].'"';
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

$registration->username     = $row['login'];
$registration->firstname    = $row['first_name'];
$registration->lastname     = $row['last_name'];
$registration->password     = $row['password'];
$registration->email        = $row['email'];

define (MAHARA_PATH, $_config['mahara']);

/******************from init.php*************************/
define('INTERNAL', 1);
define('PUBLIC', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'site');
define('SECTION_PAGE', 'register');

$CFG = new StdClass;
$CFG->docroot = MAHARA_PATH;

// Figure out our include path
if (!empty($_SERVER['MAHARA_LIBDIR'])) {
    $CFG->libroot = $_SERVER['MAHARA_LIBDIR'];
} else {
    $CFG->libroot = MAHARA_PATH. 'lib/';
}
set_include_path($CFG->libroot . PATH_SEPARATOR . $CFG->libroot . 'pear/' . PATH_SEPARATOR . get_include_path());

// Set up error handling
require(MAHARA_PATH.'lib/errors.php');

if (!is_readable($CFG->docroot . 'config.php')) {
    // @todo Later, this will redirect to the installer script. For now, we
    // just log and exit.
    log_environ(_AT('MAHARA_ERROR_INSTALL'));
    header('Location: '.AT_BASE_HREF);
}

require(MAHARA_PATH.'config.php');
$CFG = (object)array_merge((array)$cfg, (array)$CFG);

// Fix up paths in $CFG
foreach (array('docroot', 'dataroot') as $path) {
    $CFG->{$path} = (substr($CFG->{$path}, -1) != DIRECTORY_SEPARATOR) ? $CFG->{$path} . DIRECTORY_SEPARATOR : $CFG->{$path};
}

// xmldb stuff
$CFG->xmldbdisablenextprevchecking = true;
$CFG->xmldbdisablecommentchecking = true;

// ensure directorypermissions is set
if (empty($CFG->directorypermissions)) {
    $CFG->directorypermissions = 0700;
}

// core libraries
require(MAHARA_PATH.'lib/mahara.php');
ensure_sanity();
require(MAHARA_PATH.'auth/internal/lib.php');
require(MAHARA_PATH.'lib/dml.php');
require(MAHARA_PATH.'lib/ddl.php');
require(MAHARA_PATH.'lib/activity.php');
require(MAHARA_PATH.'lib/user.php');
require(MAHARA_PATH.'lib/web.php');

// Database access functions
require(MAHARA_PATH.'lib/adodb/adodb-exceptions.inc.php');
require(MAHARA_PATH.'lib/adodb/adodb.inc.php');

try {
    // ADODB does not provide the raw driver error message if the connection
    // fails for some reason, so we use output buffering to catch whatever
    // the error is instead.
    ob_start();
    
    $db = &ADONewConnection($CFG->dbtype);
    $dbgenerator = null;
    if (empty($CFG->dbhost)) {
        $CFG->dbhost = '';
    }
    else if (!empty($CFG->dbport)) {
        $CFG->dbhost .= ':'.$CFG->dbport;
    }
    if (!empty($CFG->dbpersist)) {    // Use persistent connection (default)
        $dbconnected = $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    } 
    else {                                                     // Use single connection
        $dbconnected = $db->Connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    }

    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    configure_dbconnection();
    ensure_internal_plugins_exist();

    ob_end_clean();
}
catch (Exception $e) {
    $errormessage = ob_get_contents();
    if (!$errormessage) {
        $errormessage = $e->getMessage();
    }
    ob_end_clean();
    $errormessage = get_string('dbconnfailed', 'error') . $errormessage;
    throw new ConfigSanityException($errormessage);
}
try {
    db_ignore_sql_exceptions(true);
    load_config();
    db_ignore_sql_exceptions(false);
} 
catch (SQLException $e) {
    db_ignore_sql_exceptions(false);
}


// Only do authentication once we know the page theme, so that the login form
// can have the correct theming.
require_once(MAHARA_PATH.'auth/lib.php');
$USER    = new LiveUser();
/***************end from init.php*************************/


/*~~~~~~~~~modified from register.php~~~~~~~~~~*/
$random_password = substr(md5($registration->password.rand(100000, 999999)), 2, 8);

/*-- from register_submit function --*/
$registration->salt         = substr(md5(rand(1000000, 9999999)), 2, 8);
$registration->password     = AuthInternal::encrypt_password($random_password, $registration->salt);
$registration->expiry       = NULL;
/*-----------------------------------*/


// Check if user already exists in Mahara
if ($data_record = get_record('usr', 'username', $registration->username)) {
    $registration -> id = $data_record -> id;
    update_record('usr', $registration, 'username');
} else {
    create_registered_user();   // Send register info to create a new account
}

// Reconnect to ATutor Database
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

// Store data into ATutor Databse
$sql = "INSERT INTO ".TABLE_PREFIX."mahara SET at_login='".$_SESSION['login']."', username='".$registration->username."', password='".$random_password."'";
if (!mysql_query($sql, $db_atutor))
    exit;              // in case there's some external error; prevent being caught in an infinite loop


/**
 * This function is copied and modified from register.php of Mahara
 *
 * @param array profilefields    Array of values from registration form. In this module, we're not using a form so we don't pass anything
 * @return boolean               Returns true if function exits without any problems
 */
function create_registered_user($profilefields=array()) {
    global $registration, $USER;

    db_begin();

    // Move the user record to the usr table from the registration table
    $registrationid = $registration->id;
    unset($registration->id);
    unset($registration->expiry);
    if ($expirytime = get_config('defaultaccountlifetime')) {
        $registration->expiry = db_format_timestamp(time() + $expirytime);
    }
    $registration->lastlogin = db_format_timestamp(time());

    $user = new User();
    $user->username         = $registration->username;
    $user->password         = $registration->password;
    $user->salt             = $registration->salt;
    $user->passwordchange   = 0;
    $user->active           = 1;
    $user->authinstance     = $authinstance->id;
    $user->firstname        = $registration->firstname;
    $user->lastname         = $registration->lastname;
    $user->email            = $registration->email;
    $user->commit();

    $registration->id = $user->id;

    // Insert standard stuff as artefacts
    set_profile_field($user->id, 'email', $registration->email);
    set_profile_field($user->id, 'firstname', $registration->firstname);
    set_profile_field($user->id, 'lastname', $registration->lastname);
    if (!empty($registration->lang) && $registration->lang != 'default') {
        set_account_preference($user->id, 'lang', $registration->lang);
    }

    // Set mandatory profile fields 
    foreach(ArtefactTypeProfile::get_mandatory_fields() as $field => $type) {
        // @todo here and above, use the method for getting "always mandatory" fields
        if (in_array($field, array('firstname', 'lastname', 'email'))) {
            continue;
        }
        set_profile_field($user->id, $field, $profilefields[$field]);
    }

    db_commit();
    handle_event('createuser', $registration);

    return true;
}

?>