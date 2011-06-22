<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_WORDPRESS);
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<a href="<?php echo $_config['wp-url']; ?>wp-login.php" target="tool_frame">
<?php echo _AT('wordpress_login'); ?></a>
<iframe src="<?php echo $_config['wp-url']; ?>" width="95%" height="450" style="border:none;" name="tool_frame">
<p><?php echo _AT('wordpress_no_iframe',$_config['wp-url']); ?>wp-login.php"><?php echo _AT('wordpress_login'); ?></a></p>
</iframe>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>