<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Tests and Surveys</h2>
	<p>The instructor, and assistants with test privileges, can create tests and surveys to be administered to enrolled students. There are a variety of options for defining tests like setting the release date, and using randomized questions or group-specific tests. Once a test or survey has been created, add questions to the Question Bank, and then add these questions to the new test. </p>

	<dl>
		<dt>Import Tests &amp; Surveys</dt>
		<dd>Complete tests including the test definition, as well as their questions, or just the questions without their test definition, can be imported from IMS QTI 1.2 test packages using the Import Test feature at the top of the Tests &amp; Surveys Manager. Note that if the test is included as part of a content package, then it should be imported using the <a href="content_packages.php">Content Import/Export</a> utility. </dd>
		<dt>Export Tests &amp; Surveys</dt>
		<dd>Choose a test from the Tests &amp; Survey Manager, then click on the Export button to package that test in an IMS QTI 1.2 test package. These packages can be imported back into ATutor, or into other QTI conformant systems. See the <a href="question_database.php">Question Banke</a> for information about exporting questions without the associated test definition.</dd>
	</dl>


<?php require('../common/body_footer.inc.php'); ?>
