<?php

$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/wordpress/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<a href="<?php echo $_config['wp-url']; ?>" target="toolwin">
<?php echo _AT('wordpress_login'); ?><?php echo _AT('wordpress_login_newwin'); ?></a>
<iframe src="<?php echo $_config['wp-url']; ?>" width="95%" height="450" style="border:none;" name="blog_frame">
<p><?php echo _AT('wordpress_no_iframe',$_config['wp-url']); ?>"><?php echo _AT('wordpress_login'); ?></a></p>
</iframe>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>