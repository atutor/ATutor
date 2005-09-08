<?php

$sql = array();

$sql['resource_categories.csv'] = 'SELECT CatID, CatName, CatParent FROM '.TABLE_PREFIX.'resource_categories WHERE course_id=? ORDER BY CatID ASC';

$sql['resource_links.csv'] = 'SELECT L.CatID, Url, LinkName, Description, Approved, SubmitName, SubmitEmail, SubmitDate, hits FROM '.TABLE_PREFIX.'resource_links L, '.TABLE_PREFIX.'resource_categories C  WHERE C.course_id=? AND L.CatID=C.CatID ORDER BY LinkID ASC';

?>