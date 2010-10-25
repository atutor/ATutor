
Adobe Connect Integration

-------------------------------------------------------------------

Description

This module allows ATutor to connect with an Adobe Connect Server with a Single Sign-On using Adobe Connect API and maintaining user roles. 

When a instructor makes the module avaiable, course enrolled users can access Adobe Connect without login, if they don't have an account on Adobe Connect Server the module creates an account to access, with ATutor login username and ATutor firstname and lastname. 

This module creates an Adobe Connect room foreach course where the module is avaiable. The first time a course user accesses Adobe Connect, the module search a room in Adobe Connect Server named $_SESSION["course_id"] and if it doesn't exists, creates it.

-------------------------------------------------------------------

Requirements

- Atutor version > 1.6
- Adobe Connect Server > 6

-------------------------------------------------------------------

Installation

- Login as an admin user, goto Modules -> Install modules, and select the zip file.
- On the admin menu, go to the Adobe Connect tab
- Config the Adobe Connect Server
    .Host and port
    .An Adobe Connect admin user login data
    .The folder id where the course rooms should be created (adobe connect folder sco-id)
- Activate headers authentication on the Adobe Connect Server installation
    .in INSTALLATIONPATH/appserv/conf/WEB-INF/web.xml uncomment HeaderAuthenticationFilter lines
    .in INSTALLATIONPATH/custom.ini add HTTP_AUTH_HEADER=XXXXXX where XXXXXX is the admin password
    .reboot Adobe Connect application

-------------------------------------------------------------------

I don't have any relation with Adobe, this module is based on a OnCampus University development. For commercial or server configuration support, contact Adobe.

