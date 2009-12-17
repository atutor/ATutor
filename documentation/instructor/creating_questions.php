<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Creating Test Questions</h2>
	<p>Test questions are created in the <a href="question_database.php">Question Database</a>. Options differ depending on the type of question being created. All questions are saved to the Question Database where they can then be added to Tests or Surveys. The following questions are supported:</p>
	
	<dl>
		<dt>Likert</dt>
		<dd>Likert questions require the respondent to specify their choice based on the scale provided. Keep in mind that Likert questions are not assigned a point value, so if they are included in a randomized test with other questions that do have a point value, they must be included as required question, otherwise test statistics will not be accurate. </dd>

		<dt>Matching (Graphical)</dt>
		<dd>Matching questions require the respondent to match value pairs. The graphical version creates coloured lines when pairs are created and allows for drag-and-drop interaction.</dd>

		<dt>Matching (Simple)</dt>
		<dd>Matching questions require the respondent to match value paris. The simple version does not create coloured lines and does not support drag-and-drop interaction.</dd>

		<dt>Multiple Answer</dt>
		<dd>Multiple answer questions require the respondent to answer a question by selecting two or more correct answers.</dd>

		<dt>Multiple Choice</dt>
		<dd>Multiple choice questions require the respondent to answer a question by selecting only one correct answer.</dd>

		<dt>Open Ended</dt>
		<dd>Open ended questions require the respondent to enter text in the specified text area.</dd>

		<dt>Ordering</dt>
		<dd>Ordering questions require the respondent to correctly assign given items in a particular logical order or rank.</dd>

		<dt>True or False</dt>
		<dd>True or false questions require the respondent to specify whether or not a given statement is true or false.</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>