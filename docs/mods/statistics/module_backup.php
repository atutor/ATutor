<?php

$sql = array();
$sql['statistics.csv'] = 'SELECT login_date, guests, members FROM '.TABLE_PREFIX.'course_stats WHERE course_id=?';

?>