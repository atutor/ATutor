<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/wordpress/module.css'; // use a custom stylesheet
admin_authenticate(AT_ADMIN_PRIV_WORDPRESS);

$_POST['wp-url'] = $addslashes($_POST['wp-url']);

if($_POST['submit']){
	foreach($_POST as $key=>$wp_config)
	if($key != "submit"){
	$sql="REPLACE INTO ".TABLE_PREFIX."config SET name='$key', value='".$wp_config."'";
	if($result= mysql_query($sql, $db)){
		$msg->addFeedback("WP_CONFIG_SAVED");
		$_config['wp-url'] = $_POST['wp-url'];
	}else{
		$msg->addError("WP_CONFIG_FAIL");
	}

	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="wp-url"><?php echo _AT('wordpress_base_url'); ?></label>
<input type="text" name="wp-url" id="wp-url" value="<?php if($_config['wp-url']){echo $_config['wp-url'];}else{ echo 'http://';} ?>" size="60" /><br />

<input type="submit" name="submit" value="<?php echo _AT('wordpress_save'); ?>">
</form>

<h3><?php echo _AT('wordpress_admin_login');  ?></h3>
<a href="<?php echo $_config['wp-url']; ?>wp-login.php" target="toolframe"><?php echo _AT('wordpress_login'); ?></a>
<?php
if($_config['wp-url']){?>
	<iframe frameborder="0" border="0" name="toolframe" src="<?php echo $_config['wp-url']; ?>" width="95%" height="450" id="frame_set" onload="if (window.parent &amp;&amp; window.parent.autoIframe) {window.parent.autoIframe('tree');}">
	<p><?php echo _AT('wordpress_no_iframes',$_config['wp-url']); ?> </p>
	</iframe>
<?php }else{ ?>
	<p><?php echo _AT('wordpress_do_setup'); ?></p>

<?php } ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>