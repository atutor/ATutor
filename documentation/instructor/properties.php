<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2008-05-29 16:08:46 -0400 (Thu, 29 May 2008) $'; ?>

<h2>Properties</h2>
	<p>The Properties Manager allows instructors to adjust the visual, functional, and technical details of a course. Properties set during installation can be changed with the Properties Manager. The Properties Manager is also where you <a href="delete_course.php">delete a course</a>. Additional properties are managed by the ATutor system administrator, including upload file size limitations and space limitations for a course. Contact an ATutor administrator if these properties need to be changed.</p>

<dl>
    <dt>Title</dt>
    <dd>The course name.</dd>

    <dt>Primary Language</dt>
    <dd>If a user has not yet chosen a preferred language, ATutor will display in the language selected here.</dd>

    <dt>Description</dt>
    <dd>An short text description of the course, to display in the Browse Course listing for the course.</dd>

    <dt>Course Directory</dt>
    <dd>If the ATutor administrator has enabled the "Pretty URL" feature, instructors will see a field to enter a name for the course directory, which gets added to a url while in a course. The course directory may contain numbers, letters, underscores, or dashes. No spaces are allowed. If no course directory is defined, the course ID is used in its place. The Pretty URL feature is enabled to turn conventional URL variables an there values (e.g course=21&user=13) into something more readable (e.g. course/21/user/13)</dd>


    <dt>Export Content</dt>
    <dd>If enabled, students can export course materials as content packages that can be viewed offline. If set to be <code>available  only for top level pages</code>, exporting a top level page also exports all its sub-pages. </dd>

    <dt>Syndicated Announcements</dt>
    <dd>If enabled, the course's announcements become available as an RSS feed.</dd>

    <dt>Access</dt>
    <dd>Whether students need to login, and/or enroll, to gain access to a course .</dd>

    <dt>Release Date</dt>
    <dd>The date the course can be accessed by students.</dd>

    <dt>Banner</dt>
    <dd>HTML formatted content that appears at the top of the course home page. Create splash screen, or a customized course front page. It is also possible to create a file called banner.txt, and place it in the top directory of a course file manager, that contains HTML to modify the top header area.</dd>

    <dt>Copyright Notice</dt>
    <dd>Appears in addition to the ATutor copyright notice, to signify the copyright of the content being displayed. Use <code>& copy;</code> (without the space) to create a copyright symbol</dd>

    <dt>Icon</dt>
    <dd>An 80 pixel by 80 pixel icon displayed with the course listing in MyCourses.</dd>


</dl>


<?php require('../common/body_footer.inc.php'); ?>
