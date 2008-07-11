<?php
/*
    This is the main page for the ATutor+Mahara module page.
    It checks if the current user is registered with Mahara by checking
    ATutor's 'mahara' table.  If registered, an iframe is created to display
    the Mahara page ('mahara_login.php', a login script to the page also gets called).
    If not registered, 'new_account.php' is called which automatically sets up an
    account with Mahara and saves the user information in ATutor's mahara table.
    The page then automatically gets refreshed.

    by: Boon-Hau Teh
*/


$_user_location	= 'public';

ob_start();  // we need this to be able to set cookies

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if ($_SESSION['is_guest'])
    $msg->addFeedback ("MAHARA_LOGIN");

require (AT_INCLUDE_PATH.'header.inc.php');


// Check if user is logged in as a guest
if (!($_SESSION['is_guest'])) {
    // Read login info for Mahara
    $sql    = "SELECT username, SHA1(password) FROM ".TABLE_PREFIX."mahara WHERE at_login='".$_SESSION['login']."'";
    $result = mysql_query($sql, $db);


    if (!($row = @mysql_fetch_array($result))) {
        define('new_account', 1);

        // if not configured with ATutor, automatically register for account now
        require('new_account.php');

        // refresh the page
        header('Location: index.php');

    } else {
        $username = $row[0];
        $password = $row[1];

        // Login
        ?>

        <?php
        setcookie("ATutor_Mahara[at_login]", $_SESSION['login'], time()+1200); 
        setcookie("ATutor_Mahara[username]", $username, time()+1200); 
        setcookie("ATutor_Mahara[password]", $password, time()+1200); 


        if (function_exists('url_rewrite')) {   // if "pretty url" feature supported (from ATutor 1.6.1)
            $url = url_rewrite('mods/mahara/mahara_login.php', AT_PRETTY_URL_IS_HEADER);  // to be directly called in an iframe
            $url_cookie_forward = url_rewrite('mods/mahara/cookie.php', AT_PRETTY_URL_IS_HEADER);  // to be used in a new window and redirect to mahara_login.php
        } else {
            $url = AT_BASE_HREF.'mods/mahara/mahara_login.php';
            $url_cookie_forward = AT_BASE_HREF.'mods/mahara/cookie.php';
        }

        ?>
            <script language='javascript' type='text/javascript'>
            function iFrameHeight() {
              var h = 0;
                if ( !document.all ) {
                    h = document.getElementById('ATutorMahara').contentDocument.height;
                    document.getElementById('ATutorMahara').style.height = h + 60 + 'px';
                } else if( document.all ) {
                    h = document.frames('ATutorMahara').document.body.scrollHeight;
                    document.all.ATutorMahara.style.height = h + 20 + 'px';
                }
            }
            function stopFrame(url, name, button) {
                // close iframe
                var ifr = document.getElementById(name);
                if (ifr)
                    ifr.parentNode.removeChild(ifr);

                // disable button
                var btn = document.getElementById(button);
                if (btn) {
                    btn.value = '<?=_AT('mahara_opened')?>';
                    btn.disabled = true;
                }

                // open in new window
                window.open(url);
            }
            </script>
            <div align="center">
                <input type="button" name="new_win" id="new_win" value= "<?=_AT('mahara_new_win')?>" onClick="stopFrame('<?php echo $url_cookie_forward;?>', 'ATutorMahara', 'new_win')" class="button" /><br /><br />
            </div>
            <iframe onload='iFrameHeight()' id='ATutorMahara' name='ATutorMahara'
              src='<?=$url?>' width='100%' height='500' scrolling='auto' align='top' frameborder='0'>
            </iframe>
    <?php
    }
}

ob_end_flush();

?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
