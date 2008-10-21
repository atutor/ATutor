<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Adapted Content</h2>
<p>Based on the IMS AccessForAll and ISO FDIS 24751 standards, the Adapted Content utility allows authors to enhance their content with different forms presenting the same information.  Adapted forms can be used to replace or supplement content for people with disabilities who may not be able to access the original version, or it can be used to supplement the original content by including the same content in multi-modal forms, allowing learners to experience the content through multiple senses.</p>
		
<h3>Define Adaptations for Files in Original Content</h3>
<p>Down the left of the Adapted Content screen is displayed a list of files found in the original content, such as audio files, videos, images, documents etc. for which adapted forms can be defined. Down the right is a trimmed down version of the ATutor File Manager, from which files there can be defined as adaptations. </p>

<dl>
    <dt>Original Resources</dt>
    <dd>Along the left side of the screen will appear a list of files linked into the original content, referred to as original resources. For each, define the type of resource it is by selecting the appropriate check box (Auditory, Textual, or Visual). Also select the natural language of each original resource and run <strong>Update Resource Properties</strong> to apply the settings. To add an adapted form for a particular original resource, click the radio button next to the filename of the original resource on the left, then select from the available files in the File Manager to the right, then click Add to insert the file as an adapted resource.</dd>

    <dt>Adapted Resources</dt>
    <dd>Once an adaptation has been Added, it will be listed below the original resource it is associated with as an Adapted Resource. Define the <strong>Adapted Resource Type</strong> for the alternative, either Auditory, Sign Language, Textual, or Visual, and select the natural language of the content if applicable. Once again run Update Resource Properties to save the settings. Depending on users' <a href="../general/preferences.php">preference settings</a>, different versions of the content may be displayed for different users. For instance, if a text transcript is provided as an alternative for an audio file, learners who are deaf, or do not have an audio player for instance, may receive the transcript instead of (or in addition to) the original audio content. You may add as many adaptations of an original resource as you like. </dd>

    <dd>To remove an adapted resource, click the <strong>Delete</strong> link next to the filename of the adapted resource. This removes the association between the original and adapted resources, but does not delete the actual files. They can still be found in the <a href="file_manager.php">File Manager</a>. </dd>

</dl>

<!--h3>Define Alternatives to Text Content</h3>
<p>Selecting this option at the top of the Alternate Content screen will open an editor much like the primary content editor. It can be used to create a complete alternate content page that replaces the original Primary Content when viewed by users who have set their preferences to display alternatives to text content. This editor might be used to create a media rich alternative for a plain text primary content page. Define the the resource types, or the alternative forms found in the page. </p -->


<p>See the <a href="content_edit.php">Entering Content</a> for information about using the content editor. </p>

<p>See  <a href="content_packages.php">Import/Export Content</a> for information about including adapted content in exported content packages. </p>

<p>See <a href="../general/preferences.php">Preferences</a> for information about user content preference settings.</p>

<?php require('../common/body_footer.inc.php'); ?>
