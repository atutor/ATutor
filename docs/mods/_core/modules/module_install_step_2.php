<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'../mods/_core/modules/classes/ModuleParser.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

// module content folder
$module_content_folder = AT_CONTENT_DIR . "module/";

if (isset($_GET["mod"])) $mod = str_replace(array('.','..'), '', $_GET['mod']);
else if (isset($_POST["mod"])) $mod = $_POST["mod"];

if (isset($_GET["new"])) $new = $_GET["new"];
else if (isset($_POST["new"])) $new = $_POST["new"];

if (isset($_GET["permission_granted"])) $permission_granted = $_GET["permission_granted"];
else if (isset($_POST["permission_granted"])) $permission_granted = $_POST["permission_granted"];

if (isset($_POST['submit_no']))
{
	clr_dir('../../../mods/'.$_POST['mod']);
	
	// if write permission on the mods folder has been granted, re-direct to the page of removing permission,
	// otherwise, back to start page.
	if ($_POST['permission_granted']==1)
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_install_step_3.php?cancelled=1');
	else
	{
		$msg->addFeedback('CANCELLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/install_modules.php');
	}
	
	exit;
} 
else if (isset($_POST['submit_yes'])) 
{
	// install module
	$module = $moduleFactory->getModule($_POST['mod']);
	$module->load();
	$module->install();

	if ($msg->containsErrors()) 
	{
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_install_step_2.php?mod='.$addslashes($mod).SEP.'new=1'.SEP.'permission_granted='.$permission_granted);
	} 
	else 
	{
		if ($_POST['permission_granted']==1)
		{
			header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_install_step_3.php?installed=1');
		}
		else
		{
			$msg->addFeedback('MOD_INSTALLED');
			header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php');
		}
	}
	exit;
} else if (isset($_GET['submit'])) {
	$args = '';

	if (isset($_GET['enabled'])  && $_GET['enabled'])  {  $args .= 'enabled=1';      }
	if (isset($_GET['disabled']) && $_GET['disabled']) {  $args .= SEP.'disabled=1'; }
	if (isset($_GET['missing'])  && $_GET['missing'])  {  $args .= SEP.'missing=1';  }
	if (isset($_GET['core'])     && $_GET['core'])     {  $args .= SEP.'core=1';     }
	if (isset($_GET['standard']) && $_GET['standard']) {  $args .= SEP.'standard=1'; }
	if (isset($_GET['extra'])    && $_GET['extra'])    {  $args .= SEP.'extra=1';    }

	header('Location: index.php?'. $args);
	exit;
}

// copy module from content folder into mods folder
if (isset($mod) && !isset($_GET['mod_in']))
{
	copys($module_content_folder.$mod, '../../../mods/'.$mod);
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$moduleParser = new ModuleParser();

if (!file_exists('../../../mods/'.$mod.'/module.xml')) {
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mod" value="<?php echo $mod; ?>" />
<input type="hidden" name="new" value="<?php echo $new; ?>" />
<input type="hidden" name="permission_granted" value="<?php echo $permission_granted; ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo $mod; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('missing_info'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
		<?php if (isset($new) && $new): ?>
			<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
		<?php endif; ?>
	</div>

</div>
</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$readme = get_readme('../../../mods/'.$mod);

$moduleParser->parse(file_get_contents('../../../mods/'.$mod.'/module.xml'));

$module = $moduleFactory->getModule($mod);

$properties = $module->getProperties(array('maintainers', 'url', 'date', 'license', 'state', 'notes', 'version'));
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mod" value="<?php echo $mod; ?>" />
<input type="hidden" name="new" value="<?php echo $new; ?>" />
<input type="hidden" name="permission_granted" value="<?php echo $permission_granted; ?>" />

<input type="hidden" name="enabled" value="<?php echo (int) isset($_GET['enabled']); ?>" />
<input type="hidden" name="disabled" value="<?php echo (int) isset($_GET['disabled']); ?>" />
<input type="hidden" name="core" value="<?php echo (int) isset($_GET['core']); ?>" />
<input type="hidden" name="standard" value="<?php echo (int) isset($_GET['standard']); ?>" />
<input type="hidden" name="extra" value="<?php echo (int) isset($_GET['extra']); ?>" />
<input type="hidden" name="missing" value="<?php echo (int) isset($_GET['missing']); ?>" />

<div class="input-form">
	<div class="row">
		<h3><?php echo $module->getName(); ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('description'); ?><br />
		<?php echo nl2br($module->getDescription($_SESSION['lang'])); if ($readme <> '') echo '<br /><a href="#" onclick="ATutor.poptastic(\''.AT_BASE_HREF.'mods/'.$mod.'/'.$readme.'\');return false;">'._AT('view_readme').'</a>'; ?>
	</div>

	<div class="row">
		<?php echo _AT('maintainers'); ?><br />
			<ul class="horizontal">
				<?php foreach ($properties['maintainers'] as $maintainer): ?>
					<li><?php echo $maintainer['name'] .' &lt;'.$maintainer['email'].'&gt;'; ?></li>
				<?php endforeach; ?>
			</ul>
	</div>

	<div class="row">
		<?php echo _AT('url'); ?><br />
		<?php echo $properties['url']; ?>
	</div>

	<div class="row">
		<?php echo _AT('version'); ?><br />
		<?php echo $properties['version']; ?>
	</div>

	<div class="row">
		<?php echo _AT('date'); ?><br />
		<?php echo $properties['date']; ?>
	</div>

	<div class="row">
		<?php echo _AT('license'); ?><br />
		<?php echo $properties['license']; ?>
	</div>

	<div class="row">
		<?php echo _AT('state'); ?><br />
		<?php echo $properties['state']; ?>
	</div>

	<div class="row">
		<?php echo _AT('notes'); ?><br />
		<?php echo nl2br($properties['notes']); ?>
	</div>

	<?php if (is_array($module->_pages)): ?>
		<div class="row">
			<?php if (!isset($_GET['files'])): ?>
				<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES).SEP; ?>files#files"><?php echo _AT('files'); ?></a><br />
			<?php else: ?>
				<?php $module_pages = array_keys($module->_pages); ?>
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

<?php if (!isset($new)): ?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
	</div>
<?php endif; ?>
</div>
</form>
<?php if (isset($new)): ?>
	<?php
		$hidden_vars['mod'] = $mod;
		$hidden_vars['new'] = '1';
		$hidden_vars['permission_granted'] = $permission_granted;
		
		$msg->addConfirm(array('ADD_MODULE', $mod), $hidden_vars);
		$msg->printConfirm();
	?>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>