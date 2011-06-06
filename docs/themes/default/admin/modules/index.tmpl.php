
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', count($this->keys));?>
			</h3>
		</div>

		<div class="row">
			<?php echo _AT('type'); ?><br />
			<input type="checkbox" name="core" value="1" id="t0" <?php if ($_GET['core']) { echo 'checked="checked"'; } ?> /><label for="t0"><?php echo _AT('core'); ?></label>

			<input type="checkbox" name="standard" value="1" id="t1" <?php if ($_GET['standard']) { echo 'checked="checked"'; } ?> /><label for="t1"><?php echo _AT('standard'); ?></label> 

			<input type="checkbox" name="extra" value="1" id="t2" <?php if ($_GET['extra']) { echo 'checked="checked"'; } ?> /><label for="t2"><?php echo _AT('extra'); ?></label> 
		</div>


		<div class="row">
			<?php echo _AT('status'); ?><br />
			<input type="checkbox" name="enabled" value="1" id="s0" <?php if ($_GET['enabled']) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('enabled'); ?></label> 

			<input type="checkbox" name="disabled" value="1" id="s1" <?php if ($_GET['disabled']) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('disabled'); ?></label> 

			<input type="checkbox" name="missing" value="1" id="s2" <?php if ($_GET['missing']) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('missing'); ?></label> 

			<input type="checkbox" name="partially_uninstalled" value="1" id="s3" <?php if ($_GET['partially_uninstalled']) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('partially_uninstalled'); ?></label> 
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">

<input type="hidden" name="enabled" value="<?php echo (int) $_GET['enabled']; ?>" />
<input type="hidden" name="disabled" value="<?php echo (int) $_GET['disabled']; ?>" />
<input type="hidden" name="core" value="<?php echo (int) $_GET['core']; ?>" />
<input type="hidden" name="standard" value="<?php echo (int) $_GET['standard']; ?>" />
<input type="hidden" name="extra" value="<?php echo (int) $_GET['extra']; ?>" />
<input type="hidden" name="missing" value="<?php echo (int) $_GET['missing']; ?>" />
<input type="hidden" name="partially_uninstalled" value="<?php echo (int) $_GET['partially_uninstalled']; ?>" />

<table class="data" summary="" rules="cols">
<colgroup>
		<col />
		<col class="sort" />
		<col span="4" />
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('module_name'); ?></th>
	<th scope="col"><?php echo _AT('type'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('cron'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="details" value="<?php echo _AT('details'); ?>" />
		<input type="submit" name="enable"  value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
		<input type="submit" name="uninstall" value="<?php echo _AT('uninstall'); ?>" />
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>



<?php foreach($this->keys as $dir_name) : $module =& $this->module_list[$dir_name]; $i++; $readme = get_readme(AT_INCLUDE_PATH.'../mods/'.$dir_name);?>

	<tr onmousedown="document.form['t_<?php echo $i; ?>'].checked = true; rowselect(this);" id="r_<?php echo $i; ?>">
		<td valign="top"><input type="radio" id="t_<?php echo $i; ?>" name="mod_dir" value="<?php echo $dir_name; ?>" /></td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $i; ?>"><?php echo $module->getName(); if ($readme <> '') echo '&nbsp;<a href="#" onclick="ATutor.poptastic(\''.AT_BASE_HREF.'mods/'.$dir_name.'/'.$readme.'\');return false;">'._AT('view_readme').'</a>'; ?></label></td>
		<td valign="top"><?php
			if ($module->isCore()) {
				echo '<strong>'._AT('core').'</strong>';
			} else if ($module->isStandard()) {
				echo _AT('standard');
			} else {
				echo _AT('extra');
			}
			?></td>
		<td valign="top"><?php
			if ($module->isEnabled()) {
				echo _AT('enabled');
			} else if ($module->isMissing()) {
				echo '<strong>'._AT('missing').'</strong>';
			} else if ($module->isPartiallyUninstalled()) {
				echo _AT('partially_uninstalled');
			} else {
				echo '<strong>'._AT('disabled').'</strong>';
			}
			?></td>
		<td valign="top" align="center">
			<?php if ($module->getCronInterval()): ?>
				<?php echo _AT('minutes', $module->getCronInterval()); ?>
			<?php else: ?>
				-
			<?php endif; ?>
		</td>
		<td valign="top"><code><?php echo $dir_name; ?>/</code></td>
	</tr>
<?php endforeach; ?>
<?php if (!$this->keys): ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
