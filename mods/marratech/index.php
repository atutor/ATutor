<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/elluminate/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<div>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['marratech']; ?>','marratechwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('marratech_own_window'); ?></a> </li>

<iframe name="marratech" id="marratech" title="Marratech" frameborder="1" scrolling="auto" src="<?php echo $_config['marratech']; ?>/index.jsp" height="500" width="90%" align="center" style="border:thin white solid; align:center;" allowautotransparency="true"></iframe>

</div>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>