<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2008-05-08 10:33:25 -0400 (Thu, 08 May 2008) $'; ?>

<h2>Creating/Editing Tests &amp; Surveys</h2>
	<p>To begin creating a test, use the <em>Create Test/Survey</em> link. Filling out the information on the Create Test/Survey page will address all the administrative options for a test. Actual questions are added to the test in a separate step.</p>

	<p>Test properties include:</p>

	<dl>
		<dt>Title (Mandatory field)</dt>
		<dd>Test title </dd>
	
		<dt>Description</dt>
		<dd>Test description </dd>
	
		<dt>Attempts Allowed</dt>
		<dd>Tests used for evaluation could be set to 1 attempt, while self=tests may be set to Unlimited attempts </dd>
	
		<dt>Link from My Courses</dt>
		<dd>Will display a link to the test on the My Courses page, in the course listing. Students will be made aware that the current test is available before they enter the course. This may be useful for creating a pretest to determine students' level of knowledge before taking a course.</dd>
	
		<dt>Anonymous</dt>
		<dd>Set this to No in most cases, or set it to Yes if you are creating a survey or poll.</dd>
		<dd><strong>Note: </strong>Please be aware that the instructor can not modify the anonymous option when submissions have been made on a test.</dd>

		<dt>Allow Guest</dt>
		<dd>Set this if you wish to allow users who are not logged into a course to take the test. In Release Results, set to "Once quiz has been submitted" to allow guest users to see the results of the test after they have completed it. Also see <a href="authenticated_access.php">Authenticated Access</a> for information about guest access to protected and private courses.</dd>
		
		<dt>Display</dt>
		<dd>Controls how test questions are displayed: Either all on one page, or one at a time.</dd>

		<dt>Pass Score</dt>
		<dd>Define the pass score by points or percentage or no pass score. If the pass score/percentage is define, the pass/fail feedback is displayed on student's test result page and instructor can filter by passed/failed students in test submission statistics page. </dd>

		<dt>Pass feedback</dt>
		<dd>Displayed in test result page for passed student.</dd>

		<dt>Fail feedback</dt>
		<dd>Displayed in test result page for failed student.</dd>

		<dt>Release Results</dt>
		<dd>Defines the availability of test results to students, either once the test has been submitted, once submitted and completely marked, or not at all. In the latter case, the Release Results property can later be changed to <em>Once quiz has been submitted</em> to make results available to students once all submissions have been marked.</dd>

		<dt>Randomized Questions</dt>
		<dd>Will display the number of questions specified, chosen randomly from the pool of available questions for that test. It is important that either all questions be assigned the same point value, or that those questions with different point values from the others be included as required questions, otherwise tests' "out scores" will differ from student to student. If including Likert questions in a randomized test, they must be included as required questions.</dd>
	
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