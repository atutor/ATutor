<?php
/*
Admin LDAP statistics page

Maintainer smal (Serhiy Voyt)
smalgroup@gmail.com

Version 0.2
10.11.2008

Distributed under GPL (c)Sehiy Voyt 2005-2009
*/
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    /* Get POST variables from jqGrid request */
    $page  = intval($_POST['page']);
    $limit = intval($_POST['rows']);
    $sidx  = addslashes($_POST['sidx']);
    $sord  = addslashes($_POST['sord']);
    if(!$sidx) $sidx = 1;
    if ($sord == 'desc'){
        $sord = 'DESC';
    }else{
        $sord = 'ASC';
    }
    $sql = "SELECT COUNT(*) as count FROM ".TABLE_PREFIX."ldap_log";
    $result = mysql_query($sql, $db);
    $row = mysql_fetch_assoc($result);
    $count = $row['count'];
    if ($count > 0 ) {
        $total_pages = ceil($count/$limit);
    } else {
        $total_pages = 0;
    }
    if ($page > $total_pages) $page = $total_pages;
    $start = $limit*$page - $limit;
    if ($start < 0) $start = 0;
    $sql = "SELECT L.member_id, M.login, CONCAT(M.last_name, ' ', M.first_name, ' ', M.second_name) AS full_name, 
    M.email, L.create_date, L.ldap_source FROM AT_ldap_log L LEFT JOIN AT_members M USING (member_id) ORDER BY ".$sidx." ".$sord. " 
    LIMIT ".$start." , ".$limit;
    $result = mysql_query($sql, $db);
    // Construct the json data
    $response->page = $page; // current page
    $response->total = $total_pages; // total pages
    $response->records = $count; // total records
    $i=0;
    while($row = mysql_fetch_array($result)) {
        $response->rows[$i]['id']=$row[member_id]; //id 
        $response->rows[$i]['cell']=array($row[member_id],
                                          $row[login],
                                          addslashes($row[full_name]),
                                          $row[email],
                                          $row[create_date],
                                          $row[ldap_source]);
        $i++;
    } 
    echo json_encode($response);
    exit();
}
require (AT_INCLUDE_PATH.'header.inc.php');  
?>
<link rel="stylesheet" type="text/css" media="screen" href="/jscripts/jqgrid/themes/basic/grid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/jscripts/jqgrid/themes/jqModal.css" />
<script src="/jscripts/jqgrid/jquery.js" type="text/javascript"></script>
<script src="/jscripts/jqgrid/jquery.jqGrid.js" type="text/javascript"></script>
<script src="/jscripts/jqgrid/js/jqModal.js" type="text/javascript"></script>
<script src="/jscripts/jqgrid/js/jqDnR.js" type="text/javascript"></script>
<script type="text/javascript"> 
 jQuery(document).ready(function(){ 
     jQuery("#ldapuserlist").jqGrid({ 
             url:'/admin/ldap_auth_log.php', 
             datatype: 'json', 
             mtype: 'POST', 
             colNames:[
                        '<?php echo(addslashes(_AT('ID')));?>',
                        '<?php echo(addslashes(_AT('login_name')));?>', 
                        '<?php echo(addslashes(_AT('full_name')));?>',
                        '<?php echo(addslashes(_AT('email')));?>', 
                        '<?php echo(addslashes(_AT('created_date')));?>',
                        '<?php echo(addslashes(_AT('ldap_source')));?>'], 
             colModel :[ 
               {name:'member_id', index:'member_id', width:30}, 
               {name:'login', index:'login', width:85},
               {name:'last_name', index:'last_name', width:200},
               {name:'email', index:'email', width:130},
               {name:'create_date', index:'create_date', width:110},
               {name:'ldap_source', index:'ldap_source', width:90}], 
             pager: jQuery('#pager'), 
             rowNum:50, 
             rowList:[50,100,150],
             sortname: 'create_date', 
             sortorder: "desc", 
             viewrecords: true, 
             imgpath: '/jscripts/jqgrid/themes/basic/images', 
             caption: 'LDAP Auth Log',
             width: 850,
             height: 'auto'
             }).navGrid('#pager',{
                refresh:true,
                edit: false,
                add: false,
                del: false,
                search: false,
                position: 'right'
                } 
             ); 
 }); 
</script> 
<div>
<table id="ldapuserlist" class="scroll"></table> 
<div id="pager" class="scroll" style="text-align:center;"></div>
</div>
<?php
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
