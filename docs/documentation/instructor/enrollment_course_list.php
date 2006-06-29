<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Course Lists</h2>
<p>It is possible to enter or import a course enrollment list into your ATutor course. Those on the list can be added to the <code>Enrolled</code> list immediately, or added to the <code>Not Enrolled</code> list and later moved to the Enrolled list when the time comes to give students access to the course. When users are added or moved to the Enrolled list, they are sent an email with instructions on how to access the course.</p>

<h3>Creating a Course List</h3>
<p>You have the option of manually generating the student list by selecting the <em>Create Course List</em> option. This option is useful if there is only a small number of users to be added to the course. With many students, the <em>Import</em> feature may be a more efficient option.</p>

<h3>Creating a Course Enrollment List for Import</h3>
<p>To import a class list from your local system into ATutor, create a plain text file with the format <kbd>"firstname", "lastname", "email"</kbd>, with one student per line. This file can be generated from a spreadsheet application, a database, or created manually in a plain text editor.</p>

<h3>Importing Course Enrollment Lists</h3>
<p>To import a course list (in the file format mentioned above), use the <em>Import Course List</em> link. Choose the course list file on your system by using the <kbd>Browse</kbd> button, and then use the <kbd>Import Course List</kbd> button.</p>

<p>When importing an enrollment list, ATutor will automatically generate login names for each new user based on their first and last names. There is an option to choose a format for this - either separating the username with an underscore or a period. (i.e. J_Smith, or J.Smith).</p>

<h3>Exporting Course Enrollment Lists</h3>
<p>A course enrollment list can easily be exported from ATutor and is useful for creating a backup or for importing the list into other courses. Choose which subsets of users to export (enrolled students, not enrolled students, and alumni) and use <kbd>Export</kbd> to download the list. The exported list is in the same comma-separated format as that described in <em>Creating a Course Enrollment List for Import</em> above.</p>

<?php require('../common/body_footer.inc.php'); ?>
