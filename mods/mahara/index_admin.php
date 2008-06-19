<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_MAHARA);


 /**
 * Adds an ending slash to the given path, makes a difference
 * between windows and unix paths (keeps orginal slashes)
 * @access  public
 * @param   $string The path to some directory.
 * @return  Returns the path in proper format with a trailing slash.
 * @author  Boon-Hau Teh
 */
function add_ending_slash($path){
    $slash_type = (strpos($path, '\\')===0) ? 'win' : 'unix'; 
    $last_char = substr($path, strlen($path)-1, 1);
    if ($last_char != '/' and $last_char != '\\') {
        // no slash:
        echo $slash_type;
        $path .= ($slash_type == 'win') ? '\\' : '/';
    }

    return $path;
}




if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);
	$_POST['uri'] = add_ending_slash($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('MAHARA_MINURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('mahara', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('MAHARA_MINURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

if (isset($_config['mahara'])) {
    @include ($_config['mahara'].'config.php');
    if (!isset($cfg))
        $msg->addError ("MAHARA_ERROR_PATH");

require (AT_INCLUDE_PATH.'header.inc.php');
}

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

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>