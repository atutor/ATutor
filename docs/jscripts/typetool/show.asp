<%
' -- show.asp --
' Shows a list of uploaded files
' NOTICE: This file hasn't be tested yet
' Configuration
Response.Buffer = True

Dim poolDir
    poolDir = "./uploads/"
Dim fullDir
    fullDir= "http://vietdev.sf.net/jscript/" & poolDir

' Configuration end 
%>


<html>
<head>
<title>List of uploaded Files</title>

<link REL=stylesheet HREF='./skin/vdev.css' TYPE='text/css'>
<script src="./skin/language.js"></script>

<script>
function insertImageFile(file)
{
 var win= window.opener.window.opener
 var fID= win.fID
 var edi= win.document.getElementById(fID).contentWindow

 var cmd= "<% Response.Write fulldir %>/" + file
 win.insertImageSimple(edi, cmd)

}



function createLink(file)
{
 var win= window.opener.window.opener
 var fID= win.fID
 var edi= win.document.getElementById(fID).contentWindow

 win.insertLink('<% Response.Write fullDdir %>/'+file)
}

</script>

</head>
<body class=vdev>
	<p align="center">
		<b>List of uploaded Files</b><br>
		<a href="javascript:close()">Close</a>
	</p>

<%
	' File System Object
	Dim fso
		Set fso = Server.CreateObject("Scripting.FileSystemObject")
		
	' "Uploads" Folder
	Dim folder
		Set folder = fso.GetFolder(Server.MapPath(poolDir))
		
	If folder.Size > 0 Then
		Response.Write "<ul>"
		For Each file In folder.Files
                  Response.Write "<li>" & file.Name
                  Response.Write " | <a href=""" & poolDir & "/" & file.Name & """><script>document.writeln(FILEVIEW)</script></a>"
                  Response.Write " | <a href=""javascript:insertImageFile('" & file.Name & "')""><script>document.writeln(FILEINSERT)</script></a>"
                  Response.Write " | <a href=""javascript:createLink('" & file.Name & "')""><script>document.writeln(FILELINK)</script></a> | "

                  Response.Write "( Size: " & file.Size & " )</li>"
		Next
		Response.Write "</ul>"
	Else
		Response.Write "<ul><li>No Files Uploaded.</ul>"
	End If
%>
</body>
</html>



