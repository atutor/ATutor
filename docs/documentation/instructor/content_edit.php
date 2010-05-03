<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Adding/Editing Content</h2>
	<p>Content can be created in either 'plain text' or 'HTML' mode. Plain text mode is useful for quickly writing up text content. HTML mode allows for extra features like text formatting and layout, but is a little more complex to use.</p>

	<dl>
		<dt>Title</dt>
		<dd><p>The main heading that will appear at the top of the page when viewed.</p></dd>		

		<dt>Formatting: Plain Text</dt>
		<dd><p>If using plain text mode, just type the content in the Body window. Note that any extra spaces between characters will be removed (i.e. two or more spaces), but any blank lines will be saved with the text.</p></dd>

		<dt>Formatting: HTML</dt>
		<dd><p>If using HTML mode, you can type HTML tags in the Body window along with your text. If you are unfamiliar with HTML, you can use the visual editor by clicking the <code>Switch to visual editor</code> button.</p></dd>

		<dt>Formatting: Web Link</dt>
		<dd><p>Selecting Web Link replaces the content editor window with a text field into which a URL to an external Web site can be entered. When a student views a content page formatted as a Web Link, the content of the external site becomes the content of the ATutor page.</p></dd>

		 <h3>Content Editor Toolbar</h3>

		<dt>Preview</dt>
		<dd><p>Click on the Preview icon to open the content you are currently editing in a popup window to see how it will appear.</p></dd>

		<dt>Accessibility</dt>
		<dd><p>Clicking on the Accessibility icon will gather the HTML of the page you are editing, send it off to the AChecker accessibility checker, which will return a report outlining any potential barriers that might be present (note that AChecker only works through ATutor using an IP address or qualified domain name, and not when using localhost). Review the details of the potential barriers listed, make adjustments to your content to correct them, then run the accessibility checker again. </p></dd>

		<dt>Scripts/CSS</dt>
		<dd><p>HTML that normally appears in the <kbd>head</kbd> area of a Web page can be entered here. This can include things like links to stylesheets, or the actual stylesheet markup, or you may insert links to scripts, or the scripts themselves. Additional metadata can also be entered here. HTML content created in an external editor will have its head information displayed here when Pasting from a file (see below) after which you can upload the additional files like stylesheets or scripts, and adjust the links to point to the files in the course File Manager. Note that when importing eXe content, the stylesheet supplied with its content is replaced to avoid conflicts between eXe styles and ATutor styles.  </p></dd>

		<dt>Paste</dt>
		<dd><p>Rather than typing out content, it can be uploaded from a text or HTML file on your local file system. Once uploaded, the content of that file will be displayed in the <em>Body</em> window. Keep in mind that uploading in this manner will replace any existing content in the <em>Body</em> window.</p></dd>


		<dt>Files</dt>
		<dd>
			<p>The File Manager can be opened by clicking the Files icons in the content editor tool bar.  It allows you to upload files from your local system to be used in your course. The popup File Manager can be open alongside the Content Editor then clicking the Insert button beside files to insert them into your content.</p>
			
			<p>See the <a href="file_manager.php">File Manager</a> section for details.</p>
		</dd>
		<dt>Forums</dt>
		<dd>
			<p>Click on the Forums button to open a list of the available forums for the current course, then select a forum to associate it with the content you are editing as a learning activity. Forums are exported with Common Cartridges, and are setup automatically when a Common Cartidge is imported into a course.   In future versions of ATutor, any tool available in a course can be used to add activities to content, based on the IMS Learning Tool Interoperability (LTI) standard.</p>
		</dd>

		<h3>Content Body</h3>
		<dt>TinyMCE Editor</dt>
		<dd>
			<p>The Body area of the content editor by default includes a version of the TinyMCE WYSIWYG Javascript HTML editor. It includes a simple mode, and an advanced mode, which can be toggled on or off by clicking the arrow icon at the top left of the editor. The HTML editor in the body area can be replaced with a plain text editor, or with a simple text input field where a link to an external Web site can be added. Click on the Formatting options above to switch editor modes. </p>
		</dd>
		<h3>Formatting Codes</h3>
		<p>A variety of formatting codes are available that can be used for various purposes in your content. These are described below:</p>
		<dt>Terms</dt>
		<dd>
			<p>In either plain text or HTML formatting mode, you can insert <em>terms</em> to tell the ATutor system which words you wish to mark as glossary terms. In advanced mode in TinyMCE, click on the question mark icon to insert a glossary term.</p>
			
			<p>Or, type <kbd>[?][/?]</kbd> into your content, and any text you put after <kbd>[?]</kbd> and before <kbd>[/?]</kbd> will specify the term you want to define. Alternatively, you can manually type <kbd>[?][/?]</kbd> into your text without having to use the <em>Add Term</em> link.</p>

			<p>Once you have specified the terms you would like to define, you can go to the <em>Glossary Terms</em> tab to write the definitions. Once this is done, the terms and their definitions will appear in the glossary and in the content.</p>
		</dd>

		<dt>Code</dt>
		<dd>
			<p>In either plain text or HTML formatting mode, you can insert <em>code</em> which is useful for differentiating blocks of text (like math equations, program code, or quotations) from the rest of the text content.</p>
			
			<p>Using the <em>Add Code</em> link will add <kbd>[code][/code]</kbd> into your content, and any text you put after <kbd>[code]</kbd> and before <kbd>[/code]</kbd> will specify the text you want to differentiate. Alternatively, you can manually type <kbd>[code][/code]</kbd> into your text without having to use the <em>Add Code</em> link.</p>
		</dd>

		<dt>Previous/Next</dt>
		<dd><p>Links can be generated by inserting the <kbd>[pid]</kbd> and the <kbd>[nid]</kbd> codes in your ATutor content.  When the page is displayed these codes get converted to the URL/Link for the previous or next pages in the sequence of content pages. For example <kdb><xmp><a href="[pid]">previous</a> <a href="[nid]">Next</a></xmp></kdb></p>

		<p>Or, pasted the [pid] and [nid] tags into the Link URL field in the visual editor.</p>
		</dd>

		<dt>Colours</dt>
		<dd><p>Like <em>code</em> and <em>terms</em>, colour may be added to text content in the same way. Use the appropriate colour icon to insert colour tags into the content. Valid colour options are blue, red, green, orange, purple, and gray. Also, colour codes can be typed in manually by using the following tags: <kbd>[blue][/blue]</kbd>, <kbd>[red][/red]</kbd>, <kbd>[green][/green]</kbd>, <kbd>[orange][/orange]</kbd>, <kbd>[purple][/purple]</kbd>, and <kbd>[gray][/gray]</kbd>.</p></dd>

		<dt>LaTeX</dt>
		<dd><p>Type in <kbd>[tex][/tex]</kbd> to embed LaTeX equations into your content. In the TinyMCE editor while in advanced mode, click on the TEX icon to insert the LaTeX tags.</p></dd>

		<dt>Multimedia</dt>
		<dd><p>Type the <kbd>[media][/media]</kbd> tags, along with a URL to an external media file, or a relative URL for a media file in the course File Manager (e.g. movies/mymovie.flv), to embed multimedia into your content. Supported formats currently include: mpeg, mov, wmv, swf, mp3, wav, ogg, mid, flv, and YouTube hosted videos. The media tag can take two parameters to define the width and height of the play when it displays <kbd>[media|640|480]http://www.youtube.com/watch?v=bxcZ-dFffHA[/media]</kbd>. If the parameters are not defined, the player size will default to 425x350. While in TinyMCE click on the film slides icon to insert the media tags.</p></dd>

		<dt>Save and Close</dt>
		<dd><p>While editing or creating content, it is wise to frequently <kbd>Save</kbd> your content.  When you are finished, use <kbd>Close</kbd> to close the content editor.  Note that this does not save your content first so any unsaved content will be lost.</p></dd>


	</dl>

<?php require('../common/body_footer.inc.php'); ?>