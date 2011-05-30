<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Creating and Adding External Tools</h2>
	<p>The External Tools utility allows ATutor administrators and instructors to link external tools into ATutor, and to
associate those tools with content as learning activities. External tools that support the <strong>BasicLTI</strong> tool provider standard may be added to content here.</p>


<h3>Setup A New External Tool </h3>
<ol>
<li> Title:  Enter a name for the tool being created in the title field</li>
<li> ToolID: Create an ID for the tool that will be unique across all tools on the system (e.g. demo_tool.ocadu.ca) any unique string will do</li>
<li> Description: Describe the tool, its function, and how it might be used.</li>
<li> Tool Launch URL: Copy the URL of the tool's BasicLTI launch location. Ensure there is no space at the end of the URL (see the Sample LTI Tool below for demo purposes)</li>
<li> Enter the Tool Key and secret</li>
<li> Set various options and Save</li>
</ol>

<h3>External Tool Options</h3>
<p>These values may be set by the administrator or the administrator may allow the instructor to set these values in content items.</p>

<h4>Required Settings</h4>
<ul>
<li>ToolId (must be unique across system)<br />
This is a unique identifier that you much choose.  This identifier must be unique across the system.  This identified is used to connect tool content items across exports and imports of the content area.</li>

<li>Tool Launch URL<br />
This is the launch URL for the external tool.  It is provided by the eternal tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.</li>

<li>Tool Key (oauth_consumer_key)<br />
This is the launch key for the external tool.  It is provided by the external tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.</li>

<li>Tool Secret<br />
This is the launch key for the external tool.  It is provided by the eternal tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.</li>
</ul>

<h4>Optional External Tool Settings</h4>

<ul>
<li>Frame Height<br />
This allows you to control the height of the frame that will contain the external tool.</li>

<li>Allow Frame Height to be Changed<br />
The frame height may be specified for a tool by the administrator, or the administrator may allow the instructor to change the frame height.</li>

<li>Launch Tool in Pop Up Window<br />
Normally tools are launched in an iframe at the bottom of an ATutor content page.  This option can be used so that the tool opens in a new browser window by clicking a link at the bottom of a content page, which replaces the default iframe. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Launch Tool in Debug Mode<br />
This option should normally be off except when you are having problems with tool launching.   When this option is turned on, The launch is 
paused part-way through to allow you to examine the data to be sent to the external tool.  You are then given an option to continue the launch
by pressing a button. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Send User Names to External Tool<br />
This option determines whether you want to send user names to the external tool. You should only send user names to trusted tools and you should make sure that if you share user names with the external tools that you are following all appropriate regulations regarding student privacy. Sending user names is optional data in the Basic LTI specification although some tools may require user names to function properly.  
This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Send User Mail Addresses to External Tool<br />
This option determines whether you want to send user mail addresses to the external tool.You should only send user mail addresses to trusted tools and you should make sure that
if you share user addresses with the external tools that you are following all appropriate regulations regarding student privacy.
Sending user mail addresses is optional data in the Basic LTI specification although some tools may require user names to function properly.  
This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Accept Grades From External Tool<br />
Some tools can send grades back to ATutor through a Basic LTI extension REST web service.   If the external tool has the capability of
using these services and you would like to allow the tool to send grades back to ATutor, you can enable this option.   When you author
an external tool content item and enable this option, the tool will only be able to read and write grades in a single grade book item that 
you associate with the content item. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Allow External Tool To Retrieve Roster<br />
Some tools can retrieve the entire course roster through a Basic LTI extension REST web service.  If the external tool has the capability of 
using these services and you would like to provide the entire course roster to the tool then you can enable this option.  If this option is enabled, it respects the privacy option setting as to whether or not to release user names and email addresses.   If these are configured to be provided on launches and the tool can retrieve the entire roster, then user names and mail addresses are included in the roster when it is retrieved.  This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Allow External Tool to use the Setting Service<br />
This option allows the external tool to store up to 8K of data in the content item.  Typically the external tool uses this area for a resource 
setting or perhaps a playlist as selected by the user. In particular it does not allow a separate 8K setting for each user for a content item. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.</li>

<li>Custom Parameters
Sometimes the external tool requires that you send additional custom parameters along with the launch. The typical use of this would be to specify an ISBN number for a book associated with the launch or to select a particular content item within a content repository.   Different tools will use this capability differently.  Typically these will be specified as a keyword and a value such as<br />

<strong>isbn=929293939</strong>
</li>
<?php require('../common/body_footer.inc.php'); ?>