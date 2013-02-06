<?php
function links_delete($course) {
	$queryParams = array(TABLE_PREFIX, $course, LINK_CAT_COURSE);
	$result = queryDB('SELECT cat_id FROM %slinks_categories WHERE owner_id=%d AND owner_type=%s', $queryParams);
	foreach ($result as $i => $value) {
	   $row = $result[$i];
		queryDB('DELETE FROM %slinks WHERE cat_id=%d', array(TABLE_PREFIX, $row['cat_id']));
	}
	queryDB('DELETE FROM %slinks_categories WHERE owner_id=%d AND owner_type=%d', $queryParams);
}
?>