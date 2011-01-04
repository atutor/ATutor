<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2010-06-25 15:19:46 -0400 (Fri, 25 Jun 2010) $'; ?>

<h2>Question Bank</h2>
	<p>The <em>Question Bank</em> is where course test and survey questions are stored. Questions are created separately so that they may be reused in different tests and surveys.</p>

	<dl>
		<dt>Import Questions</dt>
		<dd>Individual questions, or collections of questions that are not yet part of a test, can be imported from IMS QTI 1.2 packages using the Import Questions feature at the top of the Question Bank. Also see <a href="tests_surveys.php">Tests &amp; Surveys</a> for details on importing complete tests. </dd>
		<dt>Export Questions</dt>
		<dd>Select the checkboxes next to the questions you wish to export, then select the format for the questions (IMS QTI 1.2.1, or IMS QTI 2.1) from the menu below, then click on the Export button to package the questions in an IMS QTI 1.2 question package. These packages can be imported back into ATutor, or into other QTI conformant systems. See <a href="tests_surveys.php">Test &amp; Surveys</a> for information about exporting questions with their associated test definition as complete tests. <em>Note that the IMS QTI 2.1 export is experimental only, since there are no tools yet that support QTI 2.1 import. Unless you are a developer, you probably want to export as a QTI 1.2 package.</em></dd>
	</dl>
<?php require('../common/body_footer.inc.php'); ?>
