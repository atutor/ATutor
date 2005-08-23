<?php
	$fields    = array();
	$fields[0] = array('question',		TEXT);
	$fields[1] = array('created_date',	TEXT);
	$fields[2] = array('choice1',		TEXT);
	$fields[3] = array('choice2',		TEXT);
	$fields[4] = array('choice3',		TEXT);
	$fields[5] = array('choice4',		TEXT);
	$fields[6] = array('choice5',		TEXT);
	$fields[7] = array('choice6',		TEXT);
	$fields[8] = array('choice7',		TEXT);

	$backup_tables['polls']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'polls WHERE course_id='.$course;
	$backup_tables['polls']['fields'] = $fields;
?>