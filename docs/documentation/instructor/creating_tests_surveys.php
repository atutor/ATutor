<?php require('../common/body_header.inc.php'); ?>

<h2>Creating Tests &amp; Surveys</h2>
	<p>To begin creating a test, use the <em>Create Test/Survey</em> link. Filling out the information on the Create Test/Survey page will address all the administrative options for a test. Actual questions are added to the test in a separate step.</p>

	<p>Test properties include:</p>

	<dl>
		<dt>Attempts Allowed</dt>
		<dd>Tests used for evaluation could be set to 1 attempt, while self=tests may be set to Unlimited attempts </dd>
	
		<dt>Link from My Courses</dt>
		<dd>Will display a link to the test on the My Courses page, in the course listing. Students will be made aware that the current test is available before they enter the course. This may be useful for creating a pretest to determine students' level of knowledge before taking a course.</dd>
	
		<dt>Anonymous</dt>
		<dd>Set this to No in most cases, or set it to Yes if you are creating a survey or poll.</dd>
	
		<dt>Release Results</dt>
		<dd>Defines the availability of test results to students, either once the test has been submitted, once submitted and completely marked, or not at all. In the latter case, the Release Results property can later be changed to <em>Once quiz has been submitted</em> to make results available to students once all submissions have been marked.</dd>

		<dt>Randomized Questions</dt>
		<dd>Will display the number of questions specified, chosen randomly from the pool of available questions for that test.</dd>
	
		<dt>Start &amp; End Dates</dt>
		<dd>Define the window of time in which the test will be available to students.  It is possible to define the start date to be in the future, meaning the test will not be available until that date is reached.</dd>
	
		<dt>Assign to Groups</dt>
		<dd>Specifies the groups (Created in the <a href="groups.php">Group Manager</a>) permitted to take this test. By default, tests are available to Everyone in the course if no group is selected.. </dd>

		<dt>Instructions</dt>
		<dd>Notes that will appear at the top of the test, which might include instructions for taking the test, or include other information relevant to the test.</dd>

		<dd>Specifies the groups (created using the <a href="groups.php">Group Manager</a>) permitted to take this test. By default, tests are available to Everyone in the course.</dd>

	</dl>
	
	<p><strong>Surveys</strong> are created in the same way as regular tests, with the exception that no marks are assigned to questions and no results are released, and in some cases it might be preferable to treat submissions as <em>Anonymous</em>.  This can be done by choosing Yes from the <em>Anonymous</em> property setting.</p>

	<p>Once the initial properties have been saved, the test or survey will be listed in the Test/Survey Manager.  From here, one can <em>Edit</em> the test properties,  add <em>Questions</em> to a test, <em>Preview</em> the test questions, view the <em>Submissions</em> received so far, view the test <em>Statistics</em>, or <em>Delete</em> the test.</p>

<?php require('../common/body_footer.inc.php'); ?>