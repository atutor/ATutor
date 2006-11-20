<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Creating Test Questions</h2>
	<p>Test questions are created in the <a href="question_database.php">Question Database</a>. Options differ depending on the type of question being created. All questions are saved to the Question Database where they can then be added to Tests or Surveys. The following questions are supported:</p>
	
	<dl>
		<dt>Multiple Choice</dt>
		<dd>Multiple choice questions require the test or survey taker to answer a question by selecting one or more correct answers.</dd>
		
		<dt>True or False</dt>
		<dd>True/False questions require the test or survey taker to specify whether or not the given statement is true or false.</dd>
		
		<dt>Open Ended</dt>
		<dd>Open ended questions require the test or survey taker to enter text in the specified area.</dd>
		
		<dt>Likert</dt>
		<dd>Likert questions require the test or survey taker to specify their choice based on the scale provided.</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>