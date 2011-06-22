<?php
/*
Plugin Name: External DB authentication
Plugin URI: http://www.ploofle.com/tag/ext_db_auth/
Description: Used to externally authenticate WP users with an existing user DB.
Version: 3.15
Author: Charlene Barina
Author URI: http://www.ploofle.com

    Copyright 2007  Charlene Barina  (email : cbarina@u.washington.edu)

    This program is free software; you can redistribute it and/or modify
    it  under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//backwords compatability with php < 5 for htmlspecialchars_decode
if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

function ext_db_auth_activate() {
	add_option('ext_db_type',"MySQL","External database type");
	add_option('ext_db_mdb2_path',"","Path to MDB2 (if non-standard)");
	add_option('ext_host',"","External database hostname");
	add_option('ext_db_port',"","Database port (if non-standard)");
	add_option('ext_db',"","External database name");
	add_option('ext_db_user',"","External database username");
	add_option('ext_db_pw',"","External database password");
	add_option('ext_db_table',"","External database table for authentication");
	add_option('ext_db_namefield',"","External database field for username");
	add_option('ext_db_pwfield',"","External database field for password");
	add_option('ext_db_first_name',"");
	add_option('ext_db_last_name',"");
	add_option('ext_db_user_url',"");
	add_option('ext_db_user_email',"");
	add_option('ext_db_description',"");
	add_option('ext_db_aim',"");
	add_option('ext_db_yim',"");
	add_option('ext_db_jabber',"");
	add_option('ext_db_enc',"","Type of encoding for external db (default SHA1? or MD5?)");
	add_option('ext_db_error_msg',"","Custom login message");   
	add_option('ext_db_other_enc','$password2 = $password;');
	add_option('ext_db_role_bool','');
	add_option('ext_db_role','');
	add_option('ext_db_role_value','');
}

function ext_db_auth_init(){
	register_setting('ext_db_auth','ext_db_type');
	register_setting('ext_db_auth','ext_db_mdb2_path');
	register_setting('ext_db_auth','ext_host');
	register_setting('ext_db_auth','ext_db_port');
	register_setting('ext_db_auth','ext_db');
	register_setting('ext_db_auth','ext_db_user');
	register_setting('ext_db_auth','ext_db_pw');
	register_setting('ext_db_auth','ext_db_table');
	register_setting('ext_db_auth','ext_db_namefield');
	register_setting('ext_db_auth','ext_db_pwfield');
	register_setting('ext_db_auth','ext_db_first_name');
	register_setting('ext_db_auth','ext_db_last_name');
	register_setting('ext_db_auth','ext_db_user_url');
	register_setting('ext_db_auth','ext_db_user_email');
	register_setting('ext_db_auth','ext_db_description');
	register_setting('ext_db_auth','ext_db_aim');
	register_setting('ext_db_auth','ext_db_yim');
	register_setting('ext_db_auth','ext_db_jabber');
	register_setting('ext_db_auth','ext_db_enc');
	register_setting('ext_db_auth','ext_db_error_msg');   
	register_setting('ext_db_auth','ext_db_other_enc');
	register_setting('ext_db_auth','ext_db_role');
	register_setting('ext_db_auth','ext_db_role_bool');
	register_setting('ext_db_auth','ext_db_role_value');
}

//page for config menu
function ext_db_auth_add_menu() {
	add_options_page("External DB settings", "External DB settings", 10, __FILE__,"ext_db_auth_display_options");
}

//actual configuration screen
function ext_db_auth_display_options() { 
    $db_types[] = "MySQL";
    $db_types[] = "MSSQL";
    $db_types[] = "PgSQL";
?>
	<div class="wrap">
	<h2>External Database Authentication Settings</h2>        
	<form method="post" action="options.php">
	<?php settings_fields('ext_db_auth'); ?>
        <h3>External Database Settings</h3>
          <strong>Make sure your WP admin account exists in the external db prior to saving these settings.</strong>
        <table class="form-table">
        <tr valign="top">
            <th scope="row">Database type</th>
                <td><select name="ext_db_type" >
                <?php 
                    foreach ($db_types as $key=>$value) { //print out radio buttons
                        if ($value == get_option('ext_db_type'))
                            echo '<option value="'.$value.'" selected="selected">'.$value.'<br/>';
                        else echo '<option value="'.$value.'">'.$value.'<br/>';;
                    }                
                ?>
                </select> 
				</td>
				<td>
					<span class="description"><strong style="color:red;">required</strong>; If not MySQL, requires <a href="http://pear.php.net/package/MDB2/" target="_blank">PEAR MDB2 package</a> and relevant database driver package installation.</span>
				</td>
        </tr>        
        <tr valign="top">
            <th scope="row"><label>Path to MDB2.php</label></th>
				<td><input type="text" name="ext_db_mdb2_path" value="<?php echo get_option('ext_db_mdb2_path'); ?>" /> </td>
				<td><span class="description">Only when using non-MySQL database and in case this isn't in some sort of include path in your PHP configuration.  No trailing slash! e.g., /home/username/php </span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Host</label></th>
				<td><input type="text" name="ext_host" value="<?php echo get_option('ext_host'); ?>" /> </td>
				<td><span class="description"><strong style="color:red;">required</strong>; (often localhost)</span> </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Port</label></th>
				<td><input type="text" name="ext_db_port" value="<?php echo get_option('ext_db_port'); ?>" /> </td>
				<td><span class="description">Only set this if you have a non-standard port for connecting.</span></td>
        </tr>        
        <tr valign="top">
            <th scope="row"><label>Name</label></th>
				<td><input type="text" name="ext_db" value="<?php echo get_option('ext_db'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong></span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Username</label></th>
				<td><input type="text" name="ext_db_user" value="<?php echo get_option('ext_db_user'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong>; (recommend select privileges only)</span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Password</label></th>
				<td><input type="password" name="ext_db_pw" value="<?php echo get_option('ext_db_pw'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong></span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>User table</label></th>
				<td><input type="text" name="ext_db_table" value="<?php echo get_option('ext_db_table'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong></span></td>
        </tr>
        </table>
        
        <h3>External Database Source Fields</h3>
        <table class="form-table">
        <tr valign="top">
            <th scope="row"><label>Username</label></th>
				<td><input type="text" name="ext_db_namefield" value="<?php echo get_option('ext_db_namefield'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong></span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Password</label></th>
				<td><input type="text" name="ext_db_pwfield" value="<?php echo get_option('ext_db_pwfield'); ?>" /></td>
				<td><span class="description"><strong style="color:red;">required</strong></span><td>
        </tr>
        <tr valign="top">
            <th scope="row">Password encryption method</th>
                <td><select name="ext_db_enc">
                <?php 
                    switch(get_option('ext_db_enc')) {
                    case "SHA1" :
                        echo '<option selected="selected">SHA1</option><option>MD5</option><option>Other</option>';
                        break;
                    case "MD5" :
                        echo '<option>SHA1</option><option selected="selected">MD5</option><option>Other</option>';
                        break;                
                    case "Other" :
                        echo '<option>SHA1</option><option  selected="selected">MD5</option><option selected="selected">Other</option>';
                        break;                                        
                    default :
                        echo '<option selected="selected">SHA1</option><option>MD5</option><option>Other</option>';
                        break;
                    }
                ?>
				</select></td>
			<td><span class="description"><strong style="color:red;">required</strong>; (using "Other" requires you to enter PHP code below!)</td>            
        </tr>
        <tr valign="top">
            <th scope="row"><label>Hash code</label></th>
				<td><input type="text" name="ext_db_other_enc" size="50" value="<?php echo get_option('ext_db_other_enc'); ?>" /></td>
				<td><span class="description">Only will run if "Other" is selected and needs to be PHP code. Variable you need to set is $password2, and you have access to (original) $username and $password.</td>
        </tr>
		<tr valign="top">
            <th scope="row"><label>Role check</label></th>
			<td><input type="text" name="ext_db_role" value="<?php echo get_option('ext_db_role'); ?>" />
				<br />
				<select name="ext_db_role_bool">
                <?php 
                    switch(get_option('ext_db_role_bool')) {
                    case "is" :
                        echo '<option selected="selected">is</option><option>greater than</option><option>less than</option>';
                        break;
                    case "greater than" :
                        echo '<option>is</option><option selected="selected">greater than</option><option>less than</option>';
                        break;                
                    case "less than" :
                        echo '<option>is</option><option>greater than</option><option selected="selected">less than</option>';
                        break;                                        
                    default :
                        echo '<option selected="selected">is</option><option>greater than</option><option>less than</option>';
                        break;
                    }
                ?>
				</select><br />
				<input type="text" name="ext_db_role_value" value="<?php echo get_option('ext_db_role_value'); ?>" /></td>
				<td><span class="description">Use this if you have certain user role ids in your external database to further restrict allowed logins.  If unused, leave fields blank.</span></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>First name</label></th>
			<td><input type="text" name="ext_db_first_name" value="<?php echo get_option('ext_db_first_name'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Last name</label></th>
			<td><input type="text" name="ext_db_last_name" value="<?php echo get_option('ext_db_last_name'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Homepage</label></th>
			<td><input type="text" name="ext_db_user_url" value="<?php echo get_option('ext_db_user_url'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Email</label></th>
			<td><input type="text" name="ext_db_user_email" value="<?php echo get_option('ext_db_user_email'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Bio/description</label></th>
			<td><input type="text" name="ext_db_description" value="<?php echo get_option('ext_db_description'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>AIM screen name</label></th>
			<td><input type="text" name="ext_db_aim" value="<?php echo get_option('ext_db_aim'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>YIM screen name</label></th>
			<td><input type="text" name="ext_db_yim" value="<?php echo get_option('ext_db_yim'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>JABBER screen name</label></th>
			<td><input type="text" name="ext_db_jabber" value="<?php echo get_option('ext_db_jabber'); ?>" /></td>
        </tr>
        </table>
        <h3>Other</h3>
        <table class="form-table">
        <tr valign="top">
                <th scope="row">Custom login message</th>
                <td><textarea name="ext_db_error_msg" cols=40 rows=4><?php echo htmlspecialchars(get_option('ext_db_error_msg'));?></textarea></td>
                <td><span class="description">Shows up in login box, e.g., to tell them where to get an account. You can use HTML in this text.</td>
        </tr>        
    </table>
	
	<p class="submit">
	<input type="submit" name="Submit" value="Save changes" />
	</p>
	</form>
	</div>
<?php
}

//sort-of wrapper for all DB interactions
function db_functions($driver,$process,$resource,$query) {
    if ($driver == "MySQL") {	//use built-in PHP mysql connection
        switch($process) {
            case "connect" :
                $port = get_option('ext_db_port');                
                if (!empty($port))   $port = ":".get_option('ext_db_port');
                $resource = mysql_connect(get_option('ext_host').$port, get_option('ext_db_user'), get_option('ext_db_pw'),true) or die(mysql_error());                
                mysql_select_db(get_option('ext_db'),$resource) or die(mysql_error());
                return $resource;
                break;
            case "query":
                $result = mysql_query($query,$resource) or die(mysql_error());
                return $result;
                break;            
            case "numrows":
                return mysql_num_rows($resource);
                break;
            case "fetch":
                return mysql_fetch_assoc($resource);            
                break;
            case "close":
                mysql_close($resource);            
                break;
        }
    }
    else {  //Use MDB2   
        $mdbpath = get_option('ext_db_mdb2_path')."/MDB2.php";        
        require_once($mdbpath);
        switch($process) {
            case "connect" :                
                $port = get_option('ext_db_port');                
                if (!empty($port))   $port = ":".get_option('ext_db_port');                
                $url = strtolower($driver)."://".get_option('ext_db_user').":".get_option('ext_db_pw')."@".get_option('ext_host').$port."/".get_option('ext_db');                
                $resource =& MDB2::connect($url);
                if(PEAR::isError($resource)) die("Error while connecting : " . $resource->getMessage());
                return $resource;        
                break;
            case "query":    
                $result = $resource->query($query);
                if(PEAR::isError($result)) die('Failed to issue query, error message : ' . $result->getMessage());                            
                return $result;
                break;            
            case "numrows":
                return $resource->numRows();
                break;
            case "fetch":
                return $resource->fetchRow(MDB2_FETCHMODE_ASSOC);                
                break;
            case "close":
                $resource->disconnect();                
                break;
        }
    }
}

//actual meat of plugin - essentially, you're setting $username and $password to pass on to the system.
//You check from your external system and insert/update users into the WP system just before WP actually
//authenticates with its own database.
function ext_db_auth_check_login($username,$password) {
	require_once('./wp-includes/registration.php');
     
    //first figure out the DB type and connect...
    $driver = get_option('ext_db_type');
	//if on same host have to use resource id to make sure you don't lose the wp db connection        
    	 
    $mdbpath = get_option('ext_db_mdb2_path')."/MDB2.php";        
    if ($mdbpath != "/MDB2.php") @require_once($mdbpath);
    
    $resource = db_functions($driver,"connect","","");
	//prepare the db for unicode queries
	//to pick up umlauts, non-latin text, etc., without choking
	$utfquery = "SET NAMES 'utf8'";
	$resultutf = db_functions($driver,"query",$resource,$utfquery);  

	//do the password hash for comparing
	switch(get_option('ext_db_enc')) {
		case "SHA1" :
			$password2 = sha1($password);
			break;
		case "MD5" :
			$password2 = md5($password);
			break;			
        case "Other" :             //right now defaulting to plaintext.  People can change code here for their own special hash
            eval(get_option('ext_db_other_enc'));
            break;
	}
        
   
   //first check to see if login exists in external db
   $query = "SELECT count(*) AS numrows FROM " . get_option('ext_db_table') . " WHERE ".get_option('ext_db_namefield')." = '$username'";
   $result = db_functions($driver,"query",$resource,$query);    
   $numrows = db_functions($driver,"fetch",$result,"");
   $numrows = $numrows["numrows"];
   	
    if ($numrows) {
	     //then check to see if pw matches and get other fields...
        $sqlfields['first_name'] = get_option('ext_db_first_name');
        $sqlfields['last_name'] = get_option('ext_db_last_name');
        $sqlfields['user_url'] = get_option('ext_db_user_url');
        $sqlfields['user_email'] = get_option('ext_db_user_email');
        $sqlfields['description'] = get_option('ext_db_description');
        $sqlfields['aim'] = get_option('ext_db_aim');
        $sqlfields['yim'] = get_option('ext_db_yim');
        $sqlfields['jabber'] = get_option('ext_db_jabber');	
		$sqlfields['ext_db_role'] = get_option('ext_db_role');
		  
        foreach($sqlfields as $key=>$value) {				
            if ($value == "") unset($sqlfields[$key]);
        }
        $sqlfields2 = implode(", ",$sqlfields);
    
        //just so queries won't error out if there are no relevant fields for extended data.
        if (empty($sqlfields2)) $sqlfields2 = get_option('ext_db_namefield');
		  
	    $query = "SELECT $sqlfields2 FROM " . get_option('ext_db_table') . " WHERE ".get_option('ext_db_namefield')." = '$username' AND ".get_option('ext_db_pwfield')." = '$password2'";                            			
	    $result = db_functions($driver,"query",$resource,$query);    
        $numrows = db_functions($driver,"numrows",$result,"");         
		
		if ($numrows) {    //create/update wp account from external database if login/pw exact match exists in that db		
            $extfields = db_functions($driver,"fetch",$result,""); 
			$process = TRUE;
				
			//check role, if present.
			$role = get_option('ext_db_role');
			if (!empty($role)) {	//build the role checker too					
				$rolevalue = $extfields[$sqlfields['ext_db_role']];			
				$rolethresh = get_option('ext_db_role_value');
				$rolebool = get_option('ext_db_role_bool');					
				global $ext_error;
				if ($rolebool == 'is') {
					if ($rolevalue == $rolethresh) {}
					else {
						$username = NULL;
						$ext_error = "wrongrole";													
						$process = FALSE;
					}
				}
				if ($rolebool == 'greater than') {
					if ($rolevalue > $rolethresh) {}
					else {					
						$username = NULL;
						$ext_error = "wrongrole";														
						$process = FALSE;
					}
				}
				if ($rolebool == 'less than') {
					if ($rolevalue < $rolethresh) {}
					else {
						$username = NULL;
						$ext_error = "wrongrole";
						$process = FALSE;
					}
				}			
			}								
			//only continue with user update/creation if login/pw is valid AND, if used, proper role perms
			if ($process) {
				$userarray['user_login'] = $username;
				$userarray['user_pass'] = $password;                    
				$userarray['first_name'] = $extfields[$sqlfields['first_name']];
				$userarray['last_name'] = $extfields[$sqlfields['last_name']];        
				$userarray['user_url'] = $extfields[$sqlfields['user_url']];
				$userarray['user_email'] = $extfields[$sqlfields['user_email']];
				$userarray['description'] = $extfields[$sqlfields['description']];
				$userarray['aim'] = $extfields[$sqlfields['aim']];
				$userarray['yim'] = $extfields[$sqlfields['yim']];
				$userarray['jabber'] = $extfields[$sqlfields['jabber']];
				$userarray['display_name'] = $extfields[$sqlfields['first_name']]." ".$extfields[$sqlfields['last_name']];            
				
				//also if no extended data fields
				if ($userarray['display_name'] == " ") $userarray['display_name'] = $username;
				
				db_functions($driver,"close",$resource,"");
				
				//looks like wp functions clean up data before entry, so I'm not going to try to clean out fields beforehand.
				if ($id = username_exists($username)) {   //just do an update
					 $userarray['ID'] = $id;
					 wp_update_user($userarray);
				}
				else wp_insert_user($userarray);          //otherwise create
			}
        }        		  
		else {	//username exists but wrong password...			
			global $ext_error;
			$ext_error = "wrongpw";				
			$username = NULL;
		}
	}
	else {  //don't let login even if it's in the WP db - it needs to come only from the external db.
		global $ext_error;
		$ext_error = "notindb";
		$username = NULL;
	}	     
}


//gives warning for login - where to get "source" login
function ext_db_auth_warning() {
   echo "<p class=\"message\">".get_option('ext_db_error_msg')."</p>";
}

function ext_db_errors() {
	global $error;
	global $ext_error;
	if ($ext_error == "notindb")
		return "<strong>ERROR:</strong> Username not found.";
	else if ($ext_error == "wrongrole")
		return "<strong>ERROR:</strong> You don't have permissions to log in.";
	else if ($ext_error == "wrongpw")
		return "<strong>ERROR:</strong> Invalid password.";
	else
		return $error;
}

//hopefully grays stuff out.
function ext_db_warning() {
	echo '<strong style="color:red;">Any changes made below WILL NOT be preserved when you login again. You have to change your personal information per instructions found in the <a href="../wp-login.php">login box</a>.</strong>'; 
}

//disables the (useless) password reset option in WP when this plugin is enabled.
function ext_db_show_password_fields() {
	return 0;
}


/*
 * Disable functions.  Idea taken from http auth plugin.
 */
function disable_function_register() {	
	$errors = new WP_Error();
	$errors->add('registerdisabled', __('User registration is not available from this site, so you can\'t create an account or retrieve your password from here. See the message above.'));
	?></form><br /><div id="login_error">User registration is not available from this site, so you can't create an account or retrieve your password from here. See the message above.</div>
		<p id="backtoblog"><a href="<?php bloginfo('url'); ?>/" title="<?php _e('Are you lost?') ?>"><?php printf(__('&larr; Back to %s'), get_bloginfo('title', 'display' )); ?></a></p>
	<?php
	exit();
}

function disable_function() {	
	$errors = new WP_Error();
	$errors->add('registerdisabled', __('User registration is not available from this site, so you can\'t create an account or retrieve your password from here. See the message above.'));
	login_header(__('Log In'), '', $errors);
	?>
	<p id="backtoblog"><a href="<?php bloginfo('url'); ?>/" title="<?php _e('Are you lost?') ?>"><?php printf(__('&larr; Back to %s'), get_bloginfo('title', 'display' )); ?></a></p>
	<?php
	exit();
}


add_action('admin_init', 'ext_db_auth_init' );
add_action('admin_menu', 'ext_db_auth_add_menu');
add_action('wp_authenticate', 'ext_db_auth_check_login', 1, 2 );
add_action('lost_password', 'disable_function');
add_action('user_register', 'disable_function');
add_action('register_form', 'disable_function_register');
add_action('retrieve_password', 'disable_function');
add_action('password_reset', 'disable_function');
add_action('profile_personal_options','ext_db_warning');
add_filter('login_errors','ext_db_errors');
add_filter('show_password_fields','ext_db_show_password_fields');
add_filter('login_message','ext_db_auth_warning');

register_activation_hook( __FILE__, 'ext_db_auth_activate' );
