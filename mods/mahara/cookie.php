<?php
/*
    Called by index.php when user opens a new window.
    To avoid conflicting sessions between ATutor and Mahara,
    we are passing information through temporary cookies.
    This page simply acts as a bridge between ATutor and Mahara
    by using the ATutor session to read the user login from the 
    database, then setting the cookies and forwarding to
    the login script (mahara_login.php).

    by: Boon-Hau Teh
*/

$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// Read login info for Mahara
$sql    = "SELECT username, SHA1(password) FROM ".TABLE_PREFIX."mahara WHERE username='".$_SESSION['login']."'";
$result = mysql_query($sql, $db);

if ($row = @mysql_fetch_array($result)) {
    setcookie("ATutor_Mahara[username]", $row[0], time()+1200); 
    setcookie("ATutor_Mahara[password]", $row[1], time()+1200); 

    if (function_exists('url_rewrite')) {   // if "pretty url" feature supported (from ATutor 1.6.1)
        $url = url_rewrite('mods/mahara/mahara_login.php', AT_PRETTY_URL_IS_HEADER);
    } else {
        $url = AT_BASE_HREF.'mods/mahara/mahara_login.php';
    }

    // proceed to login script
    header('Location: '.$url);
}

?>