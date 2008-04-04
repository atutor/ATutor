<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Modules</h2>
	<p>Since version 1.5.2, ATutor provides the facility to install, enable, or disable student, instructor, and administrator tools as modules.</p>

	<p>To install a module it must first be extracted into a unique subdirectory within the <kbd>./mods</kbd> directory of your ATutor installation. It will then be listed on the <em>Install Modules</em> page where more details can be retrieved and the module installed.</p>

	<p>After extracting a module, be sure to see the readme file in the module's top directory for any additional installation instructions or requirements. See the ATutor <a href="../developer/modules.html">Module Development Documentation</a> for information about creating ATutor modules, and review the module files in the Hello World demo module (and other modules) as a model that can be duplicated and modified to quickly add new addon features to an ATutor installation. </p>

<p>Visit the <a href="http://www.atutor.ca/atutor/modules.php" target="_new">ATutor Modules Site</a> for a list of add-on modules for ATutor.</p>

<?php require('../common/body_footer.inc.php');?>