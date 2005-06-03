<?php require('../common/body_header.inc.php'); ?>

<h2>6.4 Course Lists</h2>
<p>When importing or creating a course enrollment list, students can be added to the <code>Un-enrolled</code> list as the list is being assembled, then moved over to the Enrolled list when you are ready to give students access to the course. Or, they can be added directly to the <code>Enrolled</code> list. When a student is added or moved to the Enrolled list, they are sent an email with instructions outlining how to access the course</p>

<h3>6.4.1 Creating a Course List</h3>
<p>You have the option of manually generating the student list by selecting the <em>Create Course List</em> option. This option is useful if you have a small number of students to add to a course. If you have many students to enlist then the <em>Import</em> feature may be a more efficient option.</p>

<h3>6.4.2 Creating a Course Enrollment List for Import</h3>
<p>To import a list as a file from your local system to ATutor, the format for the list must be <kbd>"firstname", "lastname", "email"</kbd>, with one student per line, saved as a plain text file.</p>

<p>This file can be generated from a spreadsheet application, a database, or created manually in a plain text editor. Once you have the list saved as a text file, you are ready to import it into ATutor.</p>

<h3>6.4.3 Importing Course Enrollment Lists</h3>
<p>To import a list of students into your course, you must first have a file on your local computer containing all the necessary information. To import a course list, select the <em>Import Course List</em> link and then select the file on your system by using the <kbd>Browse</kbd> button. Finally select the <kbd>Import Course List</kbd> button to upload the list into ATutor.</p>

<p>When importing an enrollment list, ATutor will automatically generate login names based on the firstnames and lastnames. You have the option of separating the username using an underscore or a period. (i.e. J_Smith, or J.Smith).</p>

<h3>6.4.4 Exporting Course Enrollment Lists</h3>
<p>A course enrollment list can be exported from ATutor, which is useful for creating a backup or for importing the list into other courses. You can select subsets of students to export (enrolled students, un-enrolled students, and alumni). Selecting <kbd>Export</kbd> will begin a download in your browser. The exported list is in the same comma-seperated format as that described in <em>Creating a Course Enrollment List for Import</em>.</p>

<?php require('../common/body_footer.inc.php'); ?>
