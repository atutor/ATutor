<%
  ' -- Loader.asp --
  ' -- version 1.5
  ' -- last updated 6/13/2002
  '
  ' Faisal Khan
  ' faisal@stardeveloper.com
  ' www.stardeveloper.com
  ' Class for handling binary uploads

'**************************** CLASS BEGIN
Class Loader
    Private dict
    
    Private Sub Class_Initialize
      Set dict = Server.CreateObject("Scripting.Dictionary")
    End Sub
    
    Private Sub Class_Terminate
      If IsObject(intDict) Then
        intDict.RemoveAll
        Set intDict = Nothing
      End If
      If IsObject(dict) Then
        dict.RemoveAll
        Set dict = Nothing
      End If
    End Sub

    Public Sub Initialize
      If Request.TotalBytes > 0 Then
        Dim binData
          binData = Request.BinaryRead(Request.TotalBytes)
          getData binData
      End If
    End Sub
    
    Public Function getValue(name)
      Dim gv
      If dict.Exists(name) Then
        gv = CStr(dict(name).Item("Value"))
        
        gv = Left(gv,Len(gv)-2)
        getValue = gv
      Else
        getValue = ""
      End If
    End Function
    

    Public Function fileExists(name, path)
      If dict.Exists(name) Then
        Dim temp
          temp = dict(name).Item("Value")
        Dim fso
          Set fso = Server.CreateObject("Scripting.FileSystemObject")
        If fso.FileExists(path) Then
		    fileExists = True
        Else
          fileExists= False
        End If
      Else
          fileExists= False
      End If
    End Function


    Public Function saveToFile(name, path)
      If dict.Exists(name) Then
        Dim temp
          temp = dict(name).Item("Value")
        Dim fso
          Set fso = Server.CreateObject("Scripting.FileSystemObject")
        Dim file
          Set file = fso.CreateTextFile(path)
            For tPoint = 1 to LenB(temp)
                file.Write Chr(AscB(MidB(temp,tPoint,1)))
            Next
            file.Close
          saveToFile = True
      Else
          saveToFile = False
      End If
    End Function
    
    Public Function getFileName(name)
      If dict.Exists(name) Then
        Dim temp, tempPos
          temp = dict(name).Item("FileName")
          tempPos = 1 + InStrRev(temp, "\")
          getFileName = Mid(temp, tempPos)
      Else
        getFileName = ""
      End If
    End Function
    

    Public Function getFileSize(name)
      If dict.Exists(name) Then
        getFileSize = LenB(dict(name).Item("Value"))
      Else
        getFileSize = 0
      End If
    End Function

    
    Public Function getContentType(name)
      If dict.Exists(name) Then
        getContentType = dict(name).Item("ContentType")
      Else
        getContentType = ""
      End If
    End Function

  Private Sub getData(rawData)
    Dim separator 
      separator = MidB(rawData, 1, InstrB(1, rawData, ChrB(13)) - 1)

    Dim lenSeparator
      lenSeparator = LenB(separator)

    Dim currentPos
      currentPos = 1
    Dim inStrByte
      inStrByte = 1
    Dim value, mValue
    Dim tempValue
      tempValue = ""

    While inStrByte > 0
      inStrByte = InStrB(currentPos, rawData, separator)
      mValue = inStrByte - currentPos

      If mValue > 1 Then
        value = MidB(rawData, currentPos, mValue)

        Dim begPos, endPos, midValue, nValue
        Dim intDict
          Set intDict = Server.CreateObject("Scripting.Dictionary")
    
          begPos = 1 + InStrB(1, value, ChrB(34))
          endPos = InStrB(begPos + 1, value, ChrB(34))
          nValue = endPos

        Dim nameN
          nameN = MidB(value, begPos, endPos - begPos)

        Dim nameValue, isValid
          isValid = True
          
          If InStrB(1, value, stringToByte("Content-Type")) > 1 Then

            begPos = 1 + InStrB(endPos + 1, value, ChrB(34))
            endPos = InStrB(begPos + 1, value, ChrB(34))
  
            If endPos = 0 Then
              endPos = begPos + 1
              isValid = False
            End If
            
            midValue = MidB(value, begPos, endPos - begPos)
              intDict.Add "FileName", trim(byteToString(midValue))
                
            begPos = 14 + InStrB(endPos + 1, value, stringToByte("Content-Type:"))
            endPos = InStrB(begPos, value, ChrB(13))
            
            midValue = MidB(value, begPos, endPos - begPos)
              intDict.Add "ContentType", trim(byteToString(midValue))
            
            begPos = endPos + 4
            endPos = LenB(value)
            
            nameValue = MidB(value, begPos, endPos - begPos)
          Else
            nameValue = trim(byteToString(MidB(value, nValue + 5)))
          End If

          If isValid = true Then
            intDict.Add "Value", nameValue
            intDict.Add "Name", nameN

            dict.Add byteToString(nameN), intDict
          End If
      End If

      currentPos = lenSeparator + inStrByte
    Wend
  End Sub
  
End Class
'************************** CLASS END


Private Function stringToByte(toConv)
    Dim tempChar
     For i = 1 to Len(toConv)
       tempChar = Mid(toConv, i, 1)
      stringToByte = stringToByte & chrB(AscB(tempChar))
     Next
End Function


Private Function byteToString(toConv)
    For i = 1 to LenB(toConv)
      byteToString = byteToString & chr(AscB(MidB(toConv,i,1))) 
    Next
End Function


Function RegExpTest(patrn, strng)
   Dim regEx, Match, Matches   ' Create variable.
   Set regEx = New RegExp   ' Create a regular expression.
   regEx.Pattern = patrn   ' Set pattern.
   regEx.IgnoreCase = True   ' Set case insensitivity.
   regEx.Global = True   ' Set global applicability.
   Set Matches = regEx.Execute(strng)   ' Execute search.
   Dim RetStr
   RetStr=0
   For Each Match in Matches   ' Iterate Matches collection.
      RetStr = RetStr + 1
'      RetStr = RetStr & Match.FirstIndex & ". Match Value is '"
'      RetStr = RetStr & Match.Value & "'." & vbCRLF
   Next
   RegExpTest = RetStr
End Function


'*********************** BEGIN SCRIPT
Response.Buffer = True

' **************** CONFIGURATION BEGIN ******************
' max Filesize
Dim maxSize
    maxSize = 50000

' always the same dir as upload.asp
Dim poolUrl
    poolUrl = "show.asp"

' always relative to upload.asp
Dim poolDir
    poolDir = "./uploads"
' **************** CONFIGURATION END *********************

Dim Error1
  Error1= 0

' load object
Dim load
Set load = new Loader
      
' calling initialize method
    load.initialize
      
' File name
Dim fileName
    fileName = LCase(load.getFileName("file"))
If fileName="" Then
   Error1= 1
End If


' File size
Dim fileSize
    fileSize = 0 + load.getFileSize("file")

If fileSize > maxSize Then
   Error1= 2
End If 

   

If Error1=0 Then

  ' Content Type
  Dim contentType
      contentType = load.getContentType("file")

  ' Security 
  If (RegExpTest("image/", contentType) = 0) And (RegExpTest(".js$", fileName) = 0) And (RegExpTest(".doc$", fileName) = 0)  And (RegExpTest(".exe$", fileName) = 0) And (RegExpTest(".xls$", fileName) = 0)  And (RegExpTest(".zip$", fileName) = 0)  And (RegExpTest(".tar$", fileName) = 0)  And (RegExpTest(".gz$", fileName) = 0 ) Then
	 fileName= fileName & "~.js"
  End if

  ' Path where file will be uploaded
  Dim pathToFile
      pathToFile = Server.mapPath(poolDir) & "\" & fileName

  ' Uploading file data
  Dim fileUploaded
  If load.fileExists("file", pathToFile) Then
     Error1 = 3
  Else
     fileUploaded = load.saveToFile ("file", pathToFile)
  End If

End If
      
' destroying load object
Set load = Nothing

%>



<%
If Error1=1 Then
    Response.Write "<html><body bgcolor=#c0c0a0 scroll=no><center><h2>No file selected.</h2>"
    Response.Write "<br><a href='javascript:history.back()'>Back</a></center></body></html>"
ElseIf Error1=2 Then
    Response.Write "<html><body bgcolor=#c0c0a0 scroll=no><center><h2>File too big: "& fileSize &">"& maxSize & "</h2>"
    Response.Write "<a href='javascript:history.back()'>Back</a></center></html>"
ElseIf Error1=3 Then
    Response.Write "<html><body bgcolor=#c0c0a0 scroll=no><center><h2>Filename """ & fileName & """ is existing in server.</h2>"
    Response.Write "<a href='javascript:history.back()'>Back</a></center></html>"
Else
%>

<html>
<head><title>Upload and Insert Local File</title></head>
<body bgcolor=#c0c0a0 scroll=no>
<center>
<b>File "<%= fileName %>" was uploaded.<br>You can now access the file with URL:
<a href="<%= poolDir & "/" & fileName %>" target=nw123><%= poolUrl & "/" & fileName %></a>

<%
If RegExpTest("image/", contentType) > 0 Then
  Response.Write "<br><a href=""javascript:window.opener.doFormatF('InsertImage','" & poolDir & "/" & fileName & "')"">"
Else 
  Response.Write "<br><a href=""javascript:window.opener.insertLink('" & poolDir & "/" & fileName & "')"">"
End if
  Response.Write "Insert Into The Document</a></b>"
%>

<br><b><a href="show.asp" target=nw123>Files-Pool</a></b>
<br><b><a href="javascript:history.back()">Back</a></b>
</center></body></html>
</body>
</html>

<%
End If
%>




