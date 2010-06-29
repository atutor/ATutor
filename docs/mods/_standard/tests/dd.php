<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: dd.php 7788 2008-08-20 18:20:25Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}
// Verify that we may access this question
if (!isset($_SESSION['dd_question_ids']) || !is_array($_SESSION['dd_question_ids']) || !isset($_SESSION['dd_question_ids'][$_GET['qid']])) {
	// Just exit as we're in an IFRAME
	exit;
}
// Clean up tidily
unset($_SESSION['dd_question_ids'][$_GET['qid']]);
if (count($_SESSION['dd_question_ids']) == 0) {
	unset($_SESSION['dd_question_ids']);
}
session_write_close();
$_GET['qid'] = intval($_GET['qid']);
$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$_GET[qid]";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));
$_colours = array('#FF9900', '#00FF00', '#0000FF', '#F23AA3', '#9999CC', '#990026', '#0099CC', '#22C921', '#007D48', '#00248F');

$num_options = 0;
for ($i=0; $i < 10; $i++) {
	if ($row['option_'. $i] != '') {
		$num_options++;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo AT_print($row['question'], 'tests_questions.question'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $myLang->getCharacterSet(); ?>" />
	<meta name="Generator" content="ATutor - Copyright 2007 by http://atutor.ca" />
	<base href="<?php echo AT_BASE_HREF . $content_base_href; ?>" />
	<script type="text/javascript" src="<?php echo AT_BASE_HREF; ?>jscripts/jquery.js"></script>
	<script type="text/javascript" src="<?php echo AT_BASE_HREF; ?>jscripts/interface.js"></script>
	<script type="text/javascript" src="<?php echo AT_BASE_HREF; ?>jscripts/wz_jsgraphics.js"></script>
	<link rel="stylesheet" href="<?php echo AT_BASE_HREF; ?>themes/default/styles.css" type="text/css" />
<style type="text/css">
* {
	margin: 0px;
	padding: 0px;
}
body {
	background-color: #fdfdfd;
}
option {
	padding-right: 5px;
}
li {
	padding: 5px;
	border: 1px solid #ccc;
	margin: 8px;
}
li.question {
	width: 180px;
	overflow: auto;
}
li.question:hover {
	cursor: move;
}
li.answer {
	width: 180px;
	overflow: auto;
	padding: 8px;
	margin: 8px;
}
.dropactive {
	background-color: #fc9;
}
.drophover {
	background-color: #ffc;
}
</style>
</head>
<body>

<?php $response = explode('|', $_GET['response']); ?>

<?php for ($i=0; $i < 10; $i++): ?>
	<?php if ($row['choice_'. $i] != ''): ?>
		<div id="container<?php echo $i; ?>" style="position: absolute; top: 0px; left: 0px; width: 100%"></div>
	<?php endif; ?>
<?php endfor; ?>

<form method="get">
	<ul style="position: absolute; top: 10px; left: 5px" id="q">
		<?php for ($i=0; $i < 10; $i++): ?>
			<?php if ($row['choice_'. $i] != ''): ?>
				<li class="question" id="q<?php echo $i; ?>" value="<?php echo $i; ?>">
					<select name="s<?php echo $i; ?>" onchange="selectLine(this.value, '<?php echo $i; ?>');" id="s<?php echo $i; ?>">
						<option value="-1">-</option>
						<?php for ($j=0; $j < $num_options; $j++): ?>
							<option value="<?php echo $j; ?>" <?php if($response[$i] == $j): ?>selected="selected"<?php endif; ?>><?php echo $_letters[$j]; ?></option>
						<?php endfor; ?>
					</select>
				
				<?php echo AT_print($row['choice_'.$i], 'tests_questions.question'); ?></li>
			<?php endif; ?>
		<?php endfor; ?>
	</ul>

	<ol style="position: absolute; list-style-type: upper-alpha; top: 10px; left: 310px" id="a">
		<?php for ($i=0; $i < 10; $i++): ?>
			<?php if ($row['option_'. $i] != ''): ?>
				<li class="answer" id="a<?php echo $i; ?>" value="<?php echo $i; ?>"><?php echo $_letters[$i]; ?>. <?php echo AT_print($row['option_'.$i], 'tests_questions.question'); ?></li>
			<?php endif; ?>
		<?php endfor; ?>
	</ol>
</form>
<script type="text/javascript">
// <!--
if($.browser.msie) {
	var padding = 8;
} else {
	var padding = 15;
}
var jg = Array(10);
<?php for ($i=0; $i < 10; $i++): ?>
	<?php if ($row['choice_'. $i] != ''): ?>
		jg[<?php echo $i; ?>] = new jsGraphics("container<?php echo $i; ?>");
		jg[<?php echo $i; ?>].setStroke(3);
		jg[<?php echo $i; ?>].setColor("<?php echo $_colours[$i]; ?>");
	<?php endif; ?>
<?php endfor; ?>

var container_html = $("#container0").html();

$(document).ready(
	function() {
	
		$('#q>li').Draggable(
			{
				containment: "document",
				zIndex: 	1000,
				ghosting:	true,
				opacity: 	1,
				revert:     true,
				fx: 0 // doesn't update select menu in FF if > 0
			}
		); // end draggable

		$('#a>li').Droppable(
			{
				accept : 'question', 
				activeclass: 'dropactive', 
				hoverclass:	'drophover',
				tolerance: "pointer",
				ondrop:	function (drag)  {
					var lx = drag.offsetLeft + $("#" + drag.id).width() + padding;
					var ly = drag.offsetTop  + $("#" + drag.id).height()/2 + 10;
					var rx = this.offsetLeft + 310;
					var ry = this.offsetTop  + $("#" + this.id).height()/2 + 10;

					document.getElementById('s' + drag.value).selectedIndex =  this.value + 1;

					window.top.document.getElementById("<?php echo $_GET['qid']; ?>q" + drag.value).value = this.value;

					$("#container" + drag.value).html(container_html);

					jg[drag.value].drawLine(lx, ly , rx, ry );
					jg[drag.value].paint();

					return true;
				}
			}
		); // end droppable

        parent.iframeSetHeight(<?php echo $_GET['qid']; ?>, Math.max($("#q").height(), $("#a").height()));
		<?php foreach ($response as $id => $value): ?>
		selectLine(<?php echo $value; ?>, <?php echo $id; ?>);
		<?php endforeach; ?>
	}
)

function selectLine(value, id) {
	if (value == -1) {
		window.top.document.getElementById("<?php echo $_GET['qid']; ?>q" + id).value = "-1";
		$("#container" + id).html(container_html);

		return true;
	}

	var lx = document.getElementById("q" + id).offsetLeft + $("#q" + id).width() + padding;
	var ly = document.getElementById("q" + id).offsetTop  + $("#q" + id).height()/2 + 10;
	var rx = document.getElementById("a" + value).offsetLeft + 310;
	var ry = document.getElementById("a" + value).offsetTop + $("#a" + value).height()/2 + 10;

	window.top.document.getElementById("<?php echo $_GET['qid']; ?>q" + id).value = value;

	$("#container" + id).html(container_html);
	jg[id].drawLine(lx, ly , rx, ry );
	jg[id].paint();

	return true;
}
// -->
</script>

</body>