<?php
function links_delete($course) {
    $sqlParams = array(TABLE_PREFIX, $course, LINK_CAT_COURSE);
    $result = queryDB('SELECT cat_id FROM %slinks_categories WHERE owner_id=%d AND owner_type=%d', $sqlParams);
    foreach ($result as $row) {
        queryDB('DELETE FROM %slinks WHERE cat_id=%d', array(TABLE_PREFIX, $row['cat_id']));
    }
    queryDB('DELETE FROM %slinks_categories WHERE owner_id=%d AND owner_type=%d', $sqlParams);
}
?>