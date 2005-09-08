<?php

$sql = array();
$sql['news.csv'] = 'SELECT date, formatting, title, body FROM '.TABLE_PREFIX.'news WHERE course_id=? ORDER BY news_id ASC';

?>