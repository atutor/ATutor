<?php require('../common/body_header.inc.php'); ?>

<h2>Assignments</h2>
	<p>The assignment manager works alongside the <a href="../general/file_storage.php">File Storage</a> area by letting instructors create virtual assignment drop-boxes within it. A student can submit files to the assignment workspace, and the instructor can view and download the submissions through the assignment manager or the file storage area directly.</p>

<h3>Add &amp; Manage Assignments</h3>
	<p>To add a new assignment submission area, follow the <em>Add Assignment</em> link and specify the assignment title, who to assign it to (everyone or a specific <a href="groups.php">Group</a>), the due date if there is one, and how to handle late submissions. Using the <code>Save</code> button will create a special folder named with the assignment title within the Assignment Submissions area of the File Storage area. Within each assignment folder, additional folders will be created for each student or group (depending on the "Assign to" setting). These folders are read-only and cannot be changed.</p>

	<p>It is possible to <code>Edit</code> an assignment's properties after it has been created, tho not the "Assign to" element.  Also note that to <code>Delete</code> an assignment will also delete all of its submissions. Therefore, it is advised that the instructor first download the submissions to her/his harddrive for safe keeping before deleting an assignment entry.</p>

<h3>Downloading Submissions</h3>
	<p>Only instructors and assistants with Assignment privileges may access assignment folders. Students and groups will not be able to access any submitted files. To download submitted assignments, select an assignment and use the <code>Submissions</code> button. This will redirect to the <a href="../general/file_storage.php">File Storage</a> area where an instructor can download submissions. Alternatively, this area can be accessed directly by going to the File Storage area, and selecting the name of the assignment from the <code>Workspace</code> dropdown.</p>

<?php require('../common/body_footer.inc.php'); ?>
