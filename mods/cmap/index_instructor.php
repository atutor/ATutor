<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CMAP);
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php if ($_config['cmap']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('cmap_text', $_config['cmap'] );  ?></p>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('cmap_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>
<div class="input-form">
| <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['cmap']; ?>','cmapwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('cmap_own_window'); ?></a> 
| <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('http://cmap.ihmc.us/Support/Help/','cmapwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('cmap_help_window'); ?></a> 
| <br /><br />
</div>
<iframe name="cmap" id="cmap" title="CMAP" scrolling="yes" src="<?php echo $_config['cmap']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>



<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>