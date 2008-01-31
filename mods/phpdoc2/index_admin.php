<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PHPDOC);
include ('include/classes/phpdoc2_images.class.php');
$_custom_css = $_base_path . 'mods/phpdoc2/module.css'; // use a custom stylesheet
$image_dir = 'PhpDocumentor/media/images/';

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<div id="phpdoc2_installer">
<p class="message">This utility will generate API documentation for ATutor. First click on Generate API Documentation below, then click on View API to open a framed display of the API documentation.</p>
<p>
| <a href="mods/phpdoc2/api_install.php">Generate API Documentation</a> |
<a href="mods/phpdoc2/apidoc" target="atutor_api">View API</a> |
</p>
</div>

<div id="phpdoc2_installer">
<h3><?php echo _AT('Legend'); ?></h3><br/>
<?php
$imageTableObj = new Phpdoc_images();
echo $imageTableObj->printImageTable($_base_path . 'mods/phpdoc2/PhpDocumentor/media/images/earthli/');
?>
<div class="license"><p>This feature is provided by <a href="http://www.phpdoc.org/">phpDocumentor 1.4.1</a></p></div>
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>