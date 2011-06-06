<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mod" value="<?php echo $_GET['mod']; ?>" />
<input type="hidden" name="new" value="<?php echo $_GET['new']; ?>" />

<input type="hidden" name="enabled" value="<?php echo (int) isset($_GET['enabled']); ?>" />
<input type="hidden" name="disabled" value="<?php echo (int) isset($_GET['disabled']); ?>" />
<input type="hidden" name="core" value="<?php echo (int) isset($_GET['core']); ?>" />
<input type="hidden" name="standard" value="<?php echo (int) isset($_GET['standard']); ?>" />
<input type="hidden" name="extra" value="<?php echo (int) isset($_GET['extra']); ?>" />
<input type="hidden" name="missing" value="<?php echo (int) isset($_GET['missing']); ?>" />

<div class="input-form">
	<div class="row">
		<h3><?php echo $this->module->getName(); ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('description'); ?><br />
		<?php echo nl2br($this->module->getDescription($_SESSION['lang'])); ?>
	</div>

	<div class="row">
		<?php echo _AT('maintainers'); ?><br />
			<ul class="horizontal">
				<?php foreach ($this->properties['maintainers'] as $maintainer): ?>
					<li><?php echo $maintainer['name'] .' &lt;'.$maintainer['email'].'&gt;'; ?></li>
				<?php endforeach; ?>
			</ul>
	</div>

	<div class="row">
		<?php echo _AT('url'); ?><br />
		<?php echo $this->properties['url']; ?>
	</div>

	<div class="row">
		<?php echo _AT('version'); ?><br />
		<?php echo $this->properties['version']; ?>
	</div>

	<div class="row">
		<?php echo _AT('date'); ?><br />
		<?php echo $this->properties['date']; ?>
	</div>

	<div class="row">
		<?php echo _AT('license'); ?><br />
		<?php echo $this->properties['license']; ?>
	</div>

	<div class="row">
		<?php echo _AT('state'); ?><br />
		<?php echo $this->properties['state']; ?>
	</div>

	<div class="row">
		<?php echo _AT('notes'); ?><br />
		<?php echo nl2br($this->properties['notes']); ?>
	</div>

	<?php if (is_array($this->module->_pages)): ?>
		<div class="row">
			<?php if (!isset($_GET['files'])): ?>
				<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES).SEP; ?>files#files"><?php echo _AT('files'); ?></a><br />
			<?php else: ?>
				<?php $module_pages = array_keys($this->module->_pages); ?>
				<?php natsort($module_pages); ?>
				<a name="files"></a><?php echo _AT('files'); ?><br />
				<ul style="margin-top: 0px;">
					<?php foreach ($module_pages as $key): ?>
						<?php if (defined($key)) : continue; endif; ?>
						<li><kbd><?php echo $key; ?></kbd></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php if (!isset($_REQUEST['new'])): ?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
	</div>
<?php endif; ?>
</div>
</form>