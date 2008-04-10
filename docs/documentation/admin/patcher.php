<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2008-04-04 11:25:07 -0400 (Fri, 04 April 2008) $'; ?>

<h2>Patcher</h2>
	<p>The Patcher was introduce in ATutor 1.6 to allow administrators to update their systems with feature adjustments, security fixes, and other ATutor code changes in between ATutor releases. The Patcher is included as a standard module with ATutor 1.6.1+, and installs as an extra module for 1.6.</p>
<dl>
	<dt>The Patch List</dt>
	<dd>On the opening screen of the Patcher will appear a list of patches available for the version of ATutor you are using, along with a description of each patch.  This list is retrieved from update.atutor.ca, as are the patches themselves, so you must be connected to the Internet. Patches are retrieved from update.atutor.ca in a zip form, unzipped by ATutor and applied as necessary. </dd>
	<dt>File Permissions</dt>
	<dd>In most cases you will be asked to temporarily grant write permission to the files that need to be updated or replaced, then once the patch has been applied, you will be asked to change the permissions back to read only. It is important that you follow the instructions after patches have been applied, otherwise you run the risk of opening a security hole. 
	</dd>
	<dt>Types of Patches</dt>
	<dd>Patches come in various forms. Some patches replace code in a file with new code. Others replace a file with a new file.  Others may do both on multiple files and multiple code changes. Other patches delete files that are no longer required.</dd>
	<dt>Required and Non-Required Patches</dt>
	<dd>In most cases you will want to install patches in the order they appear in the patch list, but not all patches are required patches.  Some feature patches can be ignored if you do not need the features they would add or modify on your system. Other patches will have dependencies, requiring the administrator to install earlier patches before installing a later one. You will be prompted to install previous patches if there are dependencies.</dd>
	<dt>Checks and File Backups</dt>
	<dd>If you have made changes to a file the Patcher wishes to change,  you will be prompted to continue or not. The patcher compares your local file with the same file in the ATutor code repository, and if they differ the prompt will display. In many cases  the Patcher can apply patches without changing the code you have modified, but if the code to be replaced was modified, the patch will fail, or if the patch replaces a file, your changes will be lost. In all cases the patcher will create a backup of the files that were modified, identified by the filename plus the patch number added as a suffix. Rename the file to its original name to restore that file back to its original state. You can list these files by clicking the view messages button next to the patch listing after the patch is installed. After you have confirmed that the patches were applied and are working properly, it is safe to delete the backup files, though it does not hurt to keep them around. </dd>
	<dt>Private Patches</dt>
	<dd>In some cases private patches can be applied by uploading a patch file through the upload form below the patch list.  Private patches are often those used to apply changes that are not being applied to the ATutor default source code, or to apply custom features, or to share patches between users, etc. When uploading a patch, be sure the patch id, defined in the patch.xml file, is unique . </dd>
</dl>


<?php require('../common/body_footer.inc.php');?>