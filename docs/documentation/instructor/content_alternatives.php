<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Alternate Content</h2>
	<p>Based on the IMS AccessForAll standards, the Alternate Content utility allows authors to enhance their content with different forms presenting the same information.  Alternate forms can be used to replace or enhance content for people with disabilities who may not be able to access the primary version, or it can be used to supplement the primary content by including the same content in multi-modal forms, allowing learners to experience the content through multiple senses.</p>
		
<h3>Define Alternatives to Non-Text Content</h3>
<p>Selecting this option at the top of the Alternate Content screen will display a list of media formats found in the primary content, scanning through the content and identifying any audio files, videos, images etc. to which alternative forms can be defined.</p>

<dl>
    <dt>Primary Resources</dt>
    <dd>Along the left side of the screen will appear a list of media files found in the primary content. For each, define the type of resource it is by selecting the appropriate check box (usually Visual or Auditory). Also select the natural language of the primary content. To add an alternate form for a particular primary resource, click the radio button next to the filename of the resource, then select from the available files in the File Manager to the right, then click Add to insert the file as a secondary resource.</dd>

    <dt>Secondary Resources</dt>
    <dd>Once an alternative has been Added, it will be listed below the primary resource it is associated with as a secondary resource. Define the <strong>Secondary Resource Type</strong> for the alternative, either Auditory, Sign Language, Textual, or Visual, and select the natural language of the content if applicable. Depending on users' <a href="../general/preferences.php">preference settings</a>, different versions of the content may be displayed for different users. For instance, if a text transcript is provided as an alternative for an audio file, learners who are deaf, or do not have an audio player for instance, may receive the transcript instead of (or in addition to) the primary audio content. You may add as many alternatives to a primary resource as you like. </dd>

    <dd>To remove a secondary resource, click the <strong>Delete</strong> link next to the filename of the resource. This removes the association between the primary and secondary resources, but does not delete the actual files. They can still be found in the <a href="file_manager.php">File Manager</a>. </dd>

</dl>

<h3>Define Alternatives to Text Content</h3>
<p>Selecting this option at the top of the Alternate Content screen will open an editor much like the primary content editor. It can be used to create a complete alternate content page that replaces the original Primary Content when viewed by users who have set their preferences to display alternatives to text content. This editor might be used to create a media rich alternative for a plain text primary content page. Define the the resource types, or the alternative forms found in the page. </p>


<p>See the <a href="content_edit.php">Entering Content</a> for information about using the content editor. </p>

<p>See  <a href="content_packages.php">Import/Export Content</a> for information about including alternate content in exported content packages. </p>
<?php require('../common/body_footer.inc.php'); ?>
