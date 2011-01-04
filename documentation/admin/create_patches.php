<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Creating Patches</h2>

<p>If you happen to come across a bug you can fix, or have a new feature you would like added to the ATutor public distribution, you can use Create Patch to put your changes into a form that can be easily added to the ATutor public code. Or, if you have a feature you are adding to one ATutor installation that you would like to have added to another, Create Patch is ideal for reproducing your feature across installations. If you are creating new features that are not going to become part of the ATutor public source code, you can build them into a patch so they can be reapplied from version to version as you upgrade your ATutor system. Creating patches does require knowledge of PHP, and of SQL if you plan on creating a patch that changes the ATutor database. Please refer to the appropriate documentation for information on PHP and SQL. <p>

<dl>
	<dt>ATutor Patch ID</dt>
	<dd>The patch id you give to your patch must be different from all patches available for the particular version of ATutor it applies to. It is suggested you prefix your patches with a special identifier that represents the author or the authoring organization. If for example the University of Toronto is creating the patch, a patch ID might look like "uoft_0002."</dd>
	<dt>ATutor Version to Apply</dt>
	<dd>This needs to be the exact version number of the ATutor version the patch applies to (e.g 1.6). including any minor version numbers (e.g 1.6.1.2)  The exact version number can be found on the Administrator open screen under "Statistics and Information." In most cases when applying a patch created for an older version of ATutor, the "ATutor version to Apply" will need to be adjusted. Or, this can be adjusted manually in the patch.xml file included with the source code of the patch.
	</dd>
	<dt>Description</dt>
	<dd>This should be a detailed description of what the patch does. Example might include "fixes problem uploading files to filemanager" for a bug fix, or "added a timer function to tests" for an added feature, or "removes registration tab" for a feature adjustment, etc. Include enough detail so those applying the patch understand exactly what it will do..</dd>
	<dt>SQL Statement</dt>
	<dd>This optional field can be used to insert SQL commands which modify the ATutor database. It might be used to write an SQL statement to modify and existing table, such as changing a data type, or a field size, or to add or remove an field. It can also be used to insert SQL that generates a new table for a new feature created by a patch, or it can be used to insert data into a table used by a feature created by the patch. Any SQL can be included in this field. Be careful when running SQL, that that SQL is not going to interfere with upgrade SQL. If you are changing table structures and those same tables are being altered during an upgrade, the upgrade may fail.</dd>
	<dt>Dependant Patches</dt>
	<dd>It is common for later patches to require changes from earlier patches before they can be installed. If this is the case for the patch you are creating, enter the patch IDs into the Dependant Patch ID field. Click on Add Dependent Patch if additional dependencies are required. Be sure to check the patches on the opening screen of the Patcher to see if the file you are modifying with your patch is  being modified by an update.atutor.ca patch. If they are modifying the same files, you may need to include the ID numbers for those patches in the Dependant Patches for the patch your are creating.</dd>
	<dt>Files</dt>	
	<dd>This area is where most ATutor patches are created. Click on <strong>Add File</strong>  to generate a patch block. A patch block can include one of four actions on the file being modified, as described below. As many patch blocks as required can be added to a patch. </dd>
		<dl>
	<dt>Add File</dt>
	<dd>The <strong>Add</strong> action can be used to add a new file to ATutor. This action is often used in conjuction with other patch blocks that alter or delete files, to add a replacement file for one deleted, or to perhaps add a required or include file needed by a modified section in the file being changed. In the <strong>File Name</strong> field enter the file name to be assigned to the file when it is installed. In the <strong>Directory</strong> field enter the <i>relative path</i> from the ATutor root directory in which the modified file is or will exist. Select  from <strong>Upload File</strong> using the Browse button to locate the file in your local computer's file system. Note that the upload file can have any name. It will be renamed to the file name listed in the File Name field when it is installed.</dd>
	<dt>Alter File</dt>
	<dd>This option is used when you wish to make changes to a piece of code within an existing source code file. In the <strong>File Name</strong> field enter the name of the file in the ATutor source code that will be altered. In the <strong>Directory</strong> field enter the relative path to the directory in which the to be alter file exists, relative to the ATutor root directory. In the <strong>Code To Replace From</strong> field copy the code from the original file the will be replaced, or appended to, and in the <strong>Code To Replace To</strong> field enter to code that will replace the code above in the <i>From</i> field. Or, if you are adding code instead of replacing code, include the code from the field above so it gets added back if you are only using that code as a way of identifying a location in the file where new code is being added.</dd>
	<dt>Delete File</dt>
	<dd>This option will remove files from ATutor. In the <strong>File Name</strong>  field enter the name of the file to be deleted. In the <strong>Directory</strong> field enter the path to the directory in which the to be delete file exists, relative to the root directory of the ATutor installation. </dd>
	<dt>Overwrite File</dt>
	<dd>This option is used to replace an existing file in ATutor with a new one. In the <strong>File Name</strong>  field enter the name of the file to be replaced. In the <strong>Directory</strong> field enter the path to the directory in which the to be replaced file exists, relative to the root directory of the ATutor installation. In the <strong>Upload File</strong> field use the Browse button to choose a file from your local computer to replace the specified file. The upload file may be named anything. It will be renamed to the file it is replacing when the patch is installed.</dd>
	</dl>
	<dt>Create Patch</dt>
	<dd>Click on this button to build the patch into a downloadable zip file. This zip file can then be uploaded in the Upload field on the main Patcher screen to apply a patch to a system.</dd>
	<dt>Save Patch</dt>
	<dd>Click on this button to save the developing patch to the ATutor database for future reference. Though it is not required, you should save a copy of the patch in this way, so it can be retrieved and edited if necessary. Or, if a patch takes more than a single sitting to build, you can save it, then retrieve it later to continue.</dd>
	<dt>Cancel</dt>
	<dd>Press this button to ignore the latest changes to the patch, and return to My Own Patches Screen.</dd>

</dl>



<?php require('../common/body_footer.inc.php');?>