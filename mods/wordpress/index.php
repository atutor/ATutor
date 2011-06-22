<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/wordpress/module.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<a href="<?php echo $_config['wp-url']; ?>wp-login.php" target="toolframe">
<?php echo _AT('wordpress_login'); ?></a>
<iframe name="toolframe"  src="<?php echo $_config['wp-url']; ?><?php if($p !=''){ echo '?p='.$p;} ?>" width="95%" height="450" style="border:none;">
<p><?php echo _AT('wordpress_no_iframe',$_config['wp-url']); ?>"><?php echo _AT('wordpress_login'); ?></a></p>
</iframe>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>

