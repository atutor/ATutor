<?php

$sql = array();

$sql['content.csv'] = 'SELECT content_id, content_parent_id, ordering, last_modified, revision, formatting, release_date, keywords, content_path, title, text FROM '.TABLE_PREFIX.'content WHERE course_id=? ORDER BY content_parent_id, ordering';

$sql['related_content.csv'] = 'SELECT R.content_id, R.related_content_id FROM '.TABLE_PREFIX.'related_content R, '.TABLE_PREFIX.'content C WHERE C.course_id=? AND R.content_id=C.content_id ORDER BY R.content_id ASC';


?>