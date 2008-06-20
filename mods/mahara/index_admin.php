<?php
/*
    This is the main page for the ATutor+Mahara module accessed by ATutor admins.
    It checks if the current admin is registered with Mahara by checking
    ATutor's 'mahara' table.  If registered, an iframe is created to display
    the Mahara page ('mahara_login.php', a login script to the page also gets called).
    If not registered, 'new_account_admin.php' is called which automatically sets up an
    admin account with Mahara and saves the user information in ATutor's mahara table.
    The page then automatically gets refreshed.

    by: Boon-Hau Teh
*/


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

ob_start();  // we need this to be able to set cookies

admin_authenticate(AT_ADMIN_PRIV_MAHARA);


 /**
 * Adds an ending slash to the given path, and differentiates
 * between windows and unix paths (keeps orginal slashes)
 * @access  public
 * @param   $string The path to some directory.
 * @return  Returns the path in proper format with a trailing slash.
 * @author  Boon-Hau Teh
 */
function add_ending_slash($path){
    // Check if last character is a slash
    //$last_char = substr($path, strlen($path)-1, 1);
    $last_char = $path[strlen($path)-1];
    if ($last_char != '/' && $last_char != '\\') {
        // determine if windows or unix
        $path .= (substr_count($path, '\\') > 0) ? '\\\\' : '/'; 
    }
    return $path;
}




if (isset($_POST['uri'])) {
	$mahara_path = add_ending_slash(trim($_POST['uri']));
	if (!$mahara_path){
		$msg->addError('MAHARA_MINURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
        // If Magic Quotes are not on, then add necessary slashes
        if (!get_magic_quotes_gpc())
            $mahara_path = addslashes($mahara_path);

        $sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('mahara', '$mahara_path')";
        mysql_query($sql, $db);
		$msg->addFeedback('MAHARA_MINURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

if (isset($_config['mahara'])) {
    if (!file_exists($_config['mahara'].'config.php'))
        $msg->addError ("MAHARA_ERROR_PATH");
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('mahara_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['mahara']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>


<?php

// Show iframe to Mahara if path is correct and user has access to admin site in Mahara

// First check if path is correct
if (isset($_config['mahara'])) {
    @include ($_config['mahara'].'config.php');
    if (isset($cfg)) {

        // Now check for existing account on Mahara and if it's admin

        // Read login info for Mahara
        $sql    = "SELECT username, SHA1(password) FROM ".TABLE_PREFIX."mahara WHERE at_login='".$_SESSION['login']."'";
        $result = mysql_query($sql, $db);

        if (!($row = @mysql_fetch_array($result))) {
            define('new_admin_account', 1);

            // if not configured with ATutor, automatically register for account now
            require('new_account_admin.php');

            // refresh the page
            header('Location: index_admin.php');

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
                </script>
                <iframe onload='iFrameHeight()' id='ATutorMahara' name='ATutorMahara'
                  src='<?=$url?>' width='100%' height='500' scrolling='auto' align='top' frameborder='0'>
                </iframe>
        <?php
        }


    }
}


require (AT_INCLUDE_PATH.'footer.inc.php');

ob_end_flush();

?>
