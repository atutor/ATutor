<?php

$sql = array();

$sql['glossary.csv'] = 'SELECT word_id, word, definition, related_word_id FROM '.TABLE_PREFIX.'glossary WHERE course_id=? ORDER BY word_id ASC';

?>