<?php if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

	<?php if ($_SESSION['course_id'] > 0): ?>
		<br /><div align="right" id="gototop" style="vertical-align:bottom;padding-right:3px;font-size:smaller;"><br style="clear:both;" /><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" style="border: 0px;" title="<?php echo _AT('goto_content'); ?>" ><?php echo _AT('goto_top'); ?></a></div>  
	<?php endif; ?>

	</td>
	<?php if (($_SESSION['course_id'] > 0) && $this->side_menu): ?>
		<td valign="top">
		    <a name="menu"></a>
            <div id="side-menu">
            <?php foreach ($this->side_menu as $dropdown_file): ?>
                <?php if (file_exists($dropdown_file)) { require($dropdown_file); } ?>
            <?php endforeach; ?>
            </div>
		</td>
	<?php endif; ?>
</tr>
<tr>
	<td colspan="2">
		<br /><br />
		<?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
		<?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
		<br />
	</td>
</tr>
</table>
<script type="text/javascript">
//<!--
    <?php require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); ?>
//-->
</script>
</body>
</html>