Wed Dec 22 08:50:48 EST 2010

Hi this is a alpha version of an ATutor Basic LTI Integration.

First check this out from 

https://source.sakaiproject.org/contrib//csev/trunk/atutor/basiclti/

And place this into:

/atutor/docs/mods/basiclti

In your ATutor distribution.

Here is a video of it all working:

http://www.vimeo.com/18074396

Here are the steps in ATutor after the software 
(1) Install the Module from the Admin Modules Tab and Enable it
(2) Under the Proxy Tools tab creat a new tool with the standard stuff
    http://www.imsglobal.org/developers/BLTI/tool.php
    lmsng.school.edu
    secet
(3) Make a course.  Go into content and add a new content item.  Give it a title
and then select the "Tools" tab after "Surveys and Tests" - you should be able to 
pick the prxy tool you just built.   Press Save and Close and it should launch.

I wanted to put it in content so it looks as much as possible like what
I expect IMS Common Cartridge 1.1 will look like since AT already has a 
nice imscc 1.0 import.

/Chuck

