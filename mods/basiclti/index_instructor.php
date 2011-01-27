<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_BASICLTI);

if ( !is_int($_SESSION['course_id']) || $_SESSION['course_id'] < 1 ) {
    $msg->addFeedback('NEED_COURSE_ID');
    exit;
}

if (isset($_GET['view'], $_GET['id'])) {
    header('Location: tool/instructor_view.php?id='.$_GET['id']);
    exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
    header('Location: tool/instructor_edit.php?id='.$_GET['id']);
    exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
    header('Location: tool/instructor_delete.php?id='.$_GET['id']);
    exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT t.id AS id,t.title AS title,t.toolid AS toolid,
               t.description AS description, COUNT(c.id) AS cnt 
        FROM ".TABLE_PREFIX."basiclti_tools AS t 
        LEFT OUTER JOIN ".TABLE_PREFIX."basiclti_content as c
        ON t.toolid = c.toolid
        WHERE t.course_id = ".$_SESSION['course_id']." GROUP BY t.toolid ORDER BY t.title";
$result = mysql_query($sql, $db) or die(mysql_error());
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" summary="" rules="all">
        <thead>
                <th>&nbsp;</th>
                <th><?php echo _AT('bl_title'); ?></th>
                <th><?php echo _AT('bl_toolid'); ?></th>
                <th><?php echo _AT('bl_description'); ?></th>
                <th><?php echo _AT('bl_count'); ?></th>
        </thead>
	<tfoot>
		<tr>
        	<td colspan="5"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" />
                    <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
                    <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
		</tr>
	</tfoot>
        <tbody>
                <?php while($row = mysql_fetch_array($result)) { ?><tr>
 		<td><input type="radio" name="id" value="<?php echo $row['id']; ?>" id="m<?php echo $row['id']; ?>" /></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['toolid']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['cnt']; ?></td>
                </tr> <?php } ?>
        </tbody>
</table>
</form>
<?php
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
