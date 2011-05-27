##########################################
# External Tools Module Readme file: #####
##########################################
This is the first version of an ATutor Basic LTI Integration module. 

It allows ATutor administrators and instructors to link external tools into ATutor, and to
associate those tools with content as learning activities.

The current development source code is located at:
http://svn.atutor.ca/repos/atutor/trunk/docs/mods/_standard/basiclti/

More about the BasicLTI Standard
http://www.imsglobal.org/lti/

Here are a couple videos with more information:
http://www.vimeo.com/18074396
http://vimeo.com/14100773

######################
# External Tools Setup
######################

Setup A New External Tool (Administrator)
1. Title:  Enter a name for the tool being created in the title field
2. ToolID: Create an ID for the tool that will be unique across all tools on the system (e.g. demo_tool.ocadu.ca) any unique string will do
3. Description: Describe the tool, its function, and how it might be used.
4. Tool Launch URL: Copy the URL of the tool's BasicLTI launch location. Ensure there is no space at the end of the URL (see the Sample LTI Tool below for demo purposes)
5. Enter the Tool Key and secret
6. Set various options and Save

Add a Tool to Course Content (Instructor)
1. Create a new content page and save it or edit an existing page.
2. Click on the External Tool icon in the content editor toolbar while editing that page.
3. Select from the available tools to add it as a Learning Activity.
4. Save the content page, and close the content editor.
5. The tool appears at the bottom of the page as a link that opens a popup window.

Or,

Setup a New Tool (Instructor)
1. Under the Manage Tab, click on Create External Tool in the External Tools section
2. Fill in the required fields, as described above for Administrators.
3. Set the optional settings below and Save
4. Follow the steps above to Add a Tool to Course Content, to use your new tool.

##################
# Useful Resources
##################

BasicLTI Certification
http://www.imsglobal.org/developers/alliance/LTI/blti-cert/lmscert.cfm

Sample LTI Tool for Testing/Demo Purposes
http://www.imsglobal.org/developers/BLTI/tool.php
key: lmsng.school.edu
secret: secret

Tools Currently with BasicLTI Provider Capability
QuestionMark
http://www.questionmark.com/
Noteflight
http://www.noteflight.com/
Wimba
http://www.wimba.com/
Elluminate
http://www.elluminate.com/

A list BasicLTI conformant system can be found at:
http://www.imsglobal.org/cc/statuschart.html

###############################
# Known issues in this release
###############################
- should the view button in the admin's tool listing table display the actual tool instead of the tool settings. Would be more useful I think, given clicking the edit button displays the same information. Or maybe display settings above, and open the tool below in a frame.

- course backups need to be rethought. There is currently no backup support for external; tools. 
	Issues:
	If/How to export Admin created tools in a course backup, so they will render in other systems without disclosing authentication/tool account info to instructors, who may not have license to access a tool outside it originally licenced environment?
	How to import course level tools in backups, and reset course_id and cid to the new content ids created
	How to reset the toolid to make it unique when importing BasicLTI tools in a backup into a course.

#############################
# Tool options documentation
#############################
###################
#Required Settings#
###################
#ToolId (must be unique across system)
This is a unique identifier that you much choose.  This identifier must be unique across the system.  This identified is used to connect tool content items across exports and imports of the content area.

#Tool Launch URL
This is the launch URL for the external tool.  It is provided by the eternal tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.

#Tool Key (oauth_consumer_key)
This is the launch key for the external tool.  It is provided by the external tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.

#Tool Secret
This is the launch key for the external tool.  It is provided by the eternal tool provider and should be entered here.  Generally an external tool provider will give you a URL, key, and secret so that you can access their tool.
###################
#Optional Settings#
###################

#Frame Height
This allows you to control the height of the frame that will contain the external tool.

#Allow Frame Height to be Changed
The frame height may be specified for a tool by the administrator, or the administrator may allow the instructor to change the frame height.

#Launch Tool in Pop Up Window
Normally tools are launched in an iframe at the bottom of an ATutor content page.  This option can be used so that the tool opens in a new browser window by clicking a link at the bottom of a content page, which replaces the default iframe. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Launch Tool in Debug Mode
This option should normally be off except when you are having problems with tool launching.   When this option is turned on, The launch is 
paused part-way through to allow you to examine the data to be sent to the external tool.  You are then given an option to continue the launch
by pressing a button. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Send User Names to External Tool
This option determines whether you want to send user names to the external tool. You should only send user names to trusted tools and you should make sure that if you share user names with the external tools that you are following all appropriate regulations regarding student privacy. Sending user names is optional data in the Basic LTI specification although some tools may require user names to function properly.  
This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Send User Mail Addresses to External Tool
This option determines whether you want to send user mail addresses to the external tool.You should only send user mail addresses to trusted tools and you should make sure that
if you share user addresses with the external tools that you are following all appropriate regulations regarding student privacy.
Sending user mail addresses is optional data in the Basic LTI specification although some tools may require user names to function properly.  
This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Accept Grades From External Tool
Some tools can send grades back to ATutor through a Basic LTI extension REST web service.   If the external tool has the capability of
using these services and you would like to allow the tool to send grades back to ATutor, you can enable this option.   When you author
an external tool content item and enable this option, the tool will only be able to read and write grades in a single grade book item that 
you associate with the content item. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Allow External Tool To Retrieve Roster
Some tools can retrieve the entire course roster through a Basic LTI extension REST web service.  If the external tool has the capability of 
using these services and you would like to provide the entire course roster to the tool then you can enable this option.  If this option is enabled, it respects the privacy option setting as to whether or not to release user names and email addresses.   If these are configured to be provided on launches and the tool can retrieve the entire roster, then user names and mail addresses are included in the roster when it is retrieved.  This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

#Allow External Tool to use the Setting Service
This option allows the external tool to store up to 8K of data in the content item.  Typically the external tool uses this area for a resource 
setting or perhaps a playlist as selected by the user. In particular it does not allow a separate 8K setting for each user for a content item. This option may be specified for a tool by the administrator, or the administrator may allow the instructor to specify this option.

Custom Parameters
Sometimes the external tool requires that you send additional custom parameters along with the launch. The typical use of this would be to specify an ISBN number for a book associated with the launch or to select a particular content item within a content repository.   Different tools will use this capability differently.  Typically these will be specified as a keyword and a value such as

isbn=929293939

These values may be set by the administrator or the administrator may allow the instructor to set these values in content items.