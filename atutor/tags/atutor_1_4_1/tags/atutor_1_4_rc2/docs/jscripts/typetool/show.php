<?
/* Configuration */
$poolDir= "uploads";
$fulldir= "http://vietdev.sf.net/jscript/$poolDir";
//$fulldir= "http://127.0.0.1/jscript/$poolDir";
/* End configuration */
?>


<html>
<head>
<title>Files-Pool</title>

<link REL=stylesheet HREF='./skin/vdev.css' TYPE='text/css'>
<script src="./skin/language.js"></script>

<script>
function insertImageFile(file)
{
 var win= window.opener.window.opener
 var fID= win.fID
 var edi= win.document.getElementById(fID).contentWindow

 var cmd= "<? echo $fulldir ?>/" + file
 win.insertImageSimple(edi, cmd)

}



function createLink(file)
{
 var win= window.opener.window.opener
 var fID= win.fID
 var edi= win.document.getElementById(fID).contentWindow

 win.insertLink('<? echo $fulldir ?>/'+file)
}

</script>
</head>

<body class=vdev onload="self.focus()">
<center>
<h2><script>document.writeln(FILESLIST)</script></h2>
<a href="javascript:self.close()"><script>document.writeln(CLOSE)</script></a> |
<a href="show.php?sort=name"><script>document.writeln(SORTFILENAME)</script></a> |
<a href="show.php?sort=time"><script>document.writeln(SORTFILETIME)</script></a>
</center>
<ol>

<?php 

$handle=opendir($poolDir); 
$fileArr= array ();

while ($file = readdir ($handle)) 
{ 
  if ($file != "." && $file != ".." && strtolower($file) != "index.html" && substr ($file,0,1) !="." ) 
   { 
     $statArr= stat($poolDir."/".$file);
     $key= $statArr[9];	   
     $fileArr[$key]= $file;
   } 
}

closedir($handle); 

if($_GET['sort']=='time') krsort($fileArr);
else asort($fileArr);

reset($fileArr);


while (list ($key, $file) = each ($fileArr) ) 
{ 
  $statArr= stat($poolDir."/".$file);
  $mdate= getdate($key);
  echo "<li>$file (".$mdate['mday'].".".$mdate['month'].".".$mdate['year']." / $statArr[7] Bytes) | <a href=\"$fulldir/$file\"><script>document.writeln(FILEVIEW)</script></a> | <a href=\"javascript:insertImageFile('$file')\"><script>document.writeln(FILEINSERT)</script></a> | <a href=\"javascript:createLink('$file')\"><script>document.writeln(FILELINK)</script></a> |</li>"; 
}


?>
</ol>

</body>

</html>
