<?php
/* each table to be backed up. includes the sql entry and fields */
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

/* the tables to be restored, the order matters! */
/* the key must be the module directory name.    */
/* a {table_name}Table class must exist that extends AbstractTable */
	$restore_tables['polls'] = array('polls');

//---------------------------------------------------------------------
class PollsTable extends AbstractTable {
	var $tableName = 'polls';
	var $primaryIDField = 'poll_id';

	function getOldID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'polls VALUES ';
		$sql .= '('.$row['new_id'].',';
		$sql .= $this->course_id.',';
		$sql .= "'$row[0]',"; // question
		$sql .= "'$row[1]',"; // created date
		$sql .= "0,";         // total

		for ($i=2; $i<=8; $i++) {
			$sql .= "'".$row[$i]."',0,";
		}

		$sql  = substr($sql, 0, -1);
		$sql .= ')';

		return $sql;
	}
}
?>