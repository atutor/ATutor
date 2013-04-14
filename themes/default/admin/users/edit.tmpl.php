<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="login" value="<?php echo $row['login']; ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo $row['login']; ?></h3>
	</div>

	<div class="row">
		<label for="real_name"><?php echo _AT('real_name'); ?></label><br />
		<input type="text" name="real_name" id="real_name" size="30" value="<?php echo htmlspecialchars($_POST['real_name']); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="30" value="<?php echo htmlspecialchars($_POST['email']); ?>" />
	</div>

	<div class="row">
		<?php echo _AT('privileges'); ?><br />
		<input type="checkbox" name="priv_admin" value="1" id="priv_admin" <?php if ($_POST['priv_admin']) { echo 'checked="checked"'; } ?> /><label for="priv_admin"><?php echo _AT('priv_admin_super'); ?></label><br /><br />

		<?php foreach ($this->keys as $module_name): ?>
			<?php $module =& $this->module_list[$module_name]; ?>
			<?php if (!($module->getAdminPrivilege() > 1)) { continue; } ?>
				<input type="checkbox" name="privs[]" value="<?php echo $module->getAdminPrivilege(); ?>" id="priv_<?php echo $module->getAdminPrivilege(); ?>" <?php if (query_bit($_POST['privs'], $module->getAdminPrivilege())) { echo 'checked="checked"'; }  ?> /><label for="priv_<?php echo $module->getAdminPrivilege(); ?>"><?php echo $module->getName(); ?></label><br />
		<?php endforeach; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" <?php if ($_POST['priv_admin'] != 1) { echo 'onclick="return checkAdmin();"'; } ?> />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>