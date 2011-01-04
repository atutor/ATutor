<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2008-08-21 12:10:02 -0400 (Thu, 21 Aug 2008) $'; ?>

<h2>File Manager</h2>
	<p>ATutor has a file system used for storing course content resource files, and it is managed with the <em>File Manager</em>. The File Manager allows instructors to include files associated with course content into content pages. The File Manager also allows you to create, edit, move, and delete files. The File Manager should not be confuse with the <a href="..//general/file_storage.php">File Storage</a> area.</p>

	<p>The File Manager can be found in the <em>Manage</em> area, linked from the Content Editor so it can be opened while authoring content pages, or linked throughout the Test Question authoring screens so files can be managed while assembling tests.</p>


<h3>Creating Folders</h3>
	<p>Using the <code>Create Folder</code> button creates a folder for better organizing uploaded files. It is possible to create folders and move files into folders at any time.</p>

<h3>Uploading Files</h3>
	<p>Uploading files using the <em>File Manager</em> is one way of adding content to your course. After uploading a file, it can be added to a course by using the popup File Manager linked form the <a href="content.php">Content Editor</a> and the <code>Insert</code> button that appears next to each file. This will either create a link to a file, or insert an image into a content page. For various types of mutli media, the insert button will insert the [media] tag.</p>
	
	<p><code>Browse...</code> opens a local file browser window in which to choose the file for upload.</p>

	<p><code>Upload</code> will upload the specified file to the ATutor system. Specify a file by either typing the path and filename in the <kbd>text field</kbd> or by using the <code>Browse...</code> button.</p>

	<p><code>Multi File Upload</code> will upload more than one file at a time using the Fluid Multi-File Uploader utility. Click the checkbox to turn it on, then choose <kbd>Upload Files</kbd>, followed by <kbd>Browse Files</kbd> to select the files to upload. If the browser you are using does not have a Flash plugin, required by the Fluid Uploader, only the single file uploader will be available to you. </p>

<h3>Creating New Files</h3>
	<p>The <em>Create a New File</em> area allows for quick creation of a new text or HTML file. If using Text mode, any blank lines will be saved with the file. If using HTML mode, HTML tags will be permitted. Selecting <code>Save</code> will save a new file with the entered information (filename and content) into the ATutor system and return to the File Manager. <code>Cancel</code> will discard the file and return to the File Manager.</p>

<h3>Editing Files</h3>
	<p>Text or HTML files created using the File Manager, or uploaded from another source, can be edited by selecting the Edit icon next to the file name listed in the File Manager.</p>

<h3>Previewing Files</h3>
	<p>Use the link on the filename in the File Manager to preview that file. Files that can be viewed online, such as images, text, or html files, will open in a preview window. Files that can not be displayed online, or require a plugin, will prompt you with a download confirmation message. </p>

<?php require('../common/body_footer.inc.php'); ?>