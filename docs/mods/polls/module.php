<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

// if this module is to be made available to students on the Home or Main Navigation
$_modules[] = 'polls/index.php';

$_pages['polls/index.php']['title_var'] = 'polls';
$_pages['polls/index.php']['img']       = 'images/home-polls.gif';

$_pages['tools/polls/index.php']['title_var'] = 'polls';
$_pages['tools/polls/index.php']['privilege'] = AT_PRIV_POLLS;
$_pages['tools/polls/index.php']['parent']    = 'tools/index.php';
$_pages['tools/polls/index.php']['children']  = array('tools/polls/add.php');
$_pages['tools/polls/index.php']['guide']     = 'instructor/?p=11.0.polls.php';

	$_pages['tools/polls/add.php']['title_var'] = 'add_poll';
	$_pages['tools/polls/add.php']['parent']    = 'tools/polls/index.php';

	$_pages['tools/polls/edit.php']['title_var'] = 'edit_poll';
	$_pages['tools/polls/edit.php']['parent']    = 'tools/polls/index.php';

	$_pages['tools/polls/delete.php']['title_var'] = 'delete_poll';
	$_pages['tools/polls/delete.php']['parent']    = 'tools/polls/index.php';



/* for the backups */
/* polls.csv */
/*
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
*/

/* in Backup.class.php::restore() */
/*
	if (($material === TRUE) || isset($material['polls'])) {
		$table  = $TableFactory->createTable('polls');
		$table->restore();
	}
*/

/* in TableBackup.class.php::TableFactory*/
/*
	case 'polls':
		return new PollsTable($this->version, $this->db, $this->course_id, $this->import_dir, $garbage);
		break;
*/

/*
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
*/
?>