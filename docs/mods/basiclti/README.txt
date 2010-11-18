Mon Oct 25 21:40:17 EDT 2010

Hi this is a prototype of an ATutor Basic LTI Integration.

First check this out from 

https://source.sakaiproject.org/contrib//csev/trunk/atutor/basiclti/

And place this into:

/atutor/docs/mods/basiclti

In your ATutor distribution.

There is also a patch file to apply to the rest of the ATutor distro.

-rw-r--r--  1 root  admin  4799 Oct 25 21:39 atutor-patches-01.txt

This is mostly hacking Basic LTI into the content system.   This was 
not particularly elegant, I might add - I welcome a cleaner approach.

Here is a video of it all working:

http://www.vimeo.com/16193060

Here are the steps in ATutor after the software 
(1) Install the Module from the Admin Modules Tab and Enable it
(2) Under the Proxy Tools tab creat a new tool with the standard stuff
    http://www.imsglobal.org/developers/BLTI/tool.php
    lmsng.school.edu
    secet
(3) Make a course.  Go into content and add a new content item.  Give it a title
and then select the "Tools" tab after "Surveys and Tests" - you should be able to 
pick the prxy tool you just built.   Press Save and Close and it should launch.

Lots of features are not working yet, privacy, popup, height, etc.  I 
needed to knock this out to start a conversation with the ATutor team.
They will quickly come up with the right/elegant way to do this.

I wanted to put it in content so it looks as much as possible like what
I expect IMS Common Cartridge 1.1 will look like since AT already has a 
nice imscc 1.0 import.

/Chuck

