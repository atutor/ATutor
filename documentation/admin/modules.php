<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Modules</h2>
	<h3>Import Modules</h3>
	<p>As of version 1.6.2 the preferred method of installing modules is by <strong> importing modules from atutor.ca</strong> using the Module Manager.  <strong>Read the descriptions</strong> for modules you want to install to be sure all prerequisites are met, and note the version numbers the module has been tested with. Though in most cases older modules will function without any trouble in newer versions of ATutor, occassionally they need to be updated to work with a current version. Please report any problems you experience with modules to the <a href="http://www.atutor.ca/forum/17/1.html">Modules Forum on atutor.ca</a></p>

	<h4>Uninstalling Imported Modules</h4>
	<p>Modules that have been installed using the Import method above, can be uninstalled using the Module Manager's <strong>Uninstall</strong> button. After pressing the Uninstall button you will be given the option to remove the data and the directories associated with the module. Select from the checkboxes, and press continue.  If you are removing the module completely, you will probably want to delete the data and directories. If you are upgrading or reinstalling a module, you will want to save the data and directories so they can be reused with the upgraded or reinstalled version of the module.</p>

	<h4>Upload Module</h4>
	<p>Modules can be downloaded uploaded through the Module Upload form at the top of the Install Modules screen. Uploaded modules must be in ZIP format, so if you have older modules, they may need to be axtracted from the tar.gz format, then re-archived in ZIP format before uploading.</p>

	<h4>Missing Modules</h4>	
	<p>When an ATutor system is upgraded modules need to be reinstalled. Those modules will be listed as <strong>missing</strong> in the Module Manager.

	<h4>Partially Installed Modules</h4>
	<p>When a module is uninstalled, but its data and/or directories are saved, it will be listed as <strong>Partially Uninstalled</strong>. The same status will appear for modules for which the uninstall process was not completed for one reason or another. To remove these modules completely, they must either be reinstalled then uninstalled once more, or they must be removed by deleting the database tables and/or directories manually.</p>

	<h3>Manual FTP Module Install</h3>
	<p> In most cases you should use the Import method described above. Manually installing a module through an FTP client is useful if you are installing a third party module not available from atutor.ca. Or, if you are a module developer and need control over the module files for editing etc. you will want to use the manual FTP method.  Manually installed modules must be uninstalled manually if they need to be removed.</p>

	<p>To install a module manually it must first be extracted into a unique subdirectory within the <kbd>./mods</kbd> directory of your ATutor installation. It will then be listed on the <em>Install Modules</em> page where more details can be viewed, and the module installed.</p>

	<p>After extracting a module, see the readme file in the module's top directory for any additional installation instructions or requirements.</p>

	<h3>Creating Modules</h3>
	See the ATutor <a href="../developer/modules.html">Module Development Documentation</a> for information about creating ATutor modules, and review the module files in the Hello World demo module (and other modules) as a model that can be duplicated and modified to quickly add new addon features to an ATutor installation. </p>

	<h3>Export Module </h3>
	<p>If you want to take a copy of a module from your ATutor system to import into another ATutor installation, or to bundle a copy of a module you have created to submit to atutor.ca to be included in the module repository, select the module from the Module Manager screen, then press <strong>Export</strong>. This will create a standard module ZIP file that you can download.</p>
	
	<p>Visit the <a href="http://www.atutor.ca/atutor/modules/index.php" target="_new">ATutor Modules Site</a> for a list of add-on modules for ATutor.</p>

<?php require('../common/body_footer.inc.php');?>