<?php

$sql = array();
$sql['groups.csv'] = 'SELECT title FROM '.TABLE_PREFIX.'groups WHERE course_id=? ORDER BY title';

?>