<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BASICLTI);

if (isset($_GET['view'], $_GET['id'])) {
    header('Location: admin/view_tool.php?id='.$_GET['id']);
    exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
    header('Location: admin/edit_tool.php?id='.$_GET['id']);
    exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
    header('Location: admin/delete_tool.php?id='.$_GET['id']);
    exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT id,title,toolid,course_id,description FROM ".TABLE_PREFIX."basiclti_tools WHERE course_id = 0 ORDER BY TITLE";
$result = mysql_query($sql, $db) or die(mysql_error());
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" summary="" rules="all">
        <thead>
                <th>&nbsp;</th>
                <th><?php echo _AT('bl_title'); ?></th>
                <th><?php echo _AT('bl_toolid'); ?></th>
                <th><?php echo _AT('bl_description'); ?></th>
        </thead>
	<tfoot>
		<tr>
        	<td colspan="4"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" />
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
                </tr> <?php } ?>
        </tbody>
</table>
</form>
<?php
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
