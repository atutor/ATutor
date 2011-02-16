##########################################
# External Tools Module Readme file: #####
##########################################
This is an alpha version of an ATutor Basic LTI Integration module. 

It allows ATutor administrators and instructors to link external tools into ATutor, and to
associate those tools with content as learning activities.

The current development source code is located at:
http://svn.atutor.ca/repos/atutor/trunk/mods/basiclti/

More about the BasicLTI Standard
http://www.imsglobal.org/lti/

Here are a couple videos with more information:
http://www.vimeo.com/18074396
http://vimeo.com/14100773

Intallation
1. Download the External Tools (BasicLTI TBD) Module from atutor.ca (or import it directly from atutor.ca via the Admin Module Manager if it's listed there)
2. Follow the instructions presented by the module installer to install the module.
3. Once installed, enable the module where it is listed in the Module Manager. This will create an External Tools Tab from where BasicLTI compatible external tools can be managed.

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

Useful Resources
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

###############
Known issues in this release
-missing error message when a button is clicked in the admin table listing tools, when a tool radio  has not been selected
- should the view button in the admin's tool listing table display the actual tool instead of the tool settings. Would be more useful I think, given clicking the edit button displays the same information. Or maybe display settings above, and open the tool below in a frame.
- when "allow frame height to change" setting is enabled, the frame hieght gets set to 0, effectively hiding the tool when added to a page



