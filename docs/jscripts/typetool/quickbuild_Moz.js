/*** Freeware Open Source writen by ngoCanh 2002-05                  */
/*** Original by Vietdev  http://vietdev.sourceforge.net             */
/*** Release 2004-03-15  R9.0                                        */
/*** GPL - Copyright protected                                       */
/*********************************************************************/
function iEditor(idF)
{
  var o=document.getElementById(idF).contentWindow.document;
  o.designMode="On"
  o.addEventListener("mousedown", function(){ TXT=null; fID=idF; }, false); 
  o.addEventListener("mouseup", FMUp, false); 
  o.addEventListener("keypress", FKPress, true); 

  var arr=idF.split("VDevID");
//  var val=document.forms[arr[0]][arr[1]].value;
var val=document.form[26].value;
   val=val.replace(/\r/g,"");
   val=val.replace(/\n</g,"<");
   
   var reg=/<pre>/i ;
   if( reg.test(val) )
    { val=val.replace(/\n/g, "&#13;"); val=val.replace(/\t/g, "     "); }

   val=val.replace(/\n/g, "<br>");
   val=val.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");

   val=val.replace(/\\/g, "&#92;");
   val=val.replace(/\'/g, "&#39;");

   if(val && val.indexOf('ViEtDeVdIvId')>=0) val=initDefaultOptions1(val,idF)
   else initDefaultOptions0(idF)

   setTimeout("document.getElementById('"+idF+"').contentWindow.document.body.innerHTML='"+val+"'",200)

   format[idF]='HTML'
   viewm[idF]=1;
}




function changetoIframeEditor(el)
{
   var wi='', hi='';
   if(el.style.height) hi=el.style.height
   else if(el.rows) hi=(14*el.getAttribute('rows')+28)
   if(el.style.width) wi=el.style.width
   else if(el.cols) wi=(6*el.getAttribute('cols') +25)
   
   var parent=el.parentNode   
   
   while(parent.nodeName !='FORM') parent=parent.parentNode
   var oform=parent
   var fidx=0; while(document.forms[fidx] !=oform) fidx++ ; // form index

   var val='';
   if(el.nodeName=='TEXTAREA' || el.nodeName=='INPUT')
    { fID=fidx+'VDevID'+el.getAttribute('name'); val=el.value }
   else fID=fidx+'VDevID'+el.getAttribute('id')

   createEditor(el,fID,wi,hi);

   setTimeout("iEditor('"+fID+"')",200); 
   return true;
  
}




// init all found TEXTAREA in document
function changeAllTextareaToEditors()
{
  var i=0;
  while(document.getElementsByTagName('textarea')[i])
   { 
    if(!changetoIframeEditor(document.getElementsByTagName('textarea')[i])) break;
	if(++i>0 && !document.getElementsByTagName('textarea')[i] ) i=0;
   }
}



// init all found IFRAME in document to Editable
function changeAllIframeToEditors()
{
  var i=0;
  while(document.getElementsByTagName('iframe')[i])
  { 
   if(!changetoIframeEditor(document.getElementsByTagName('iframe')[i])) break;
   i++
  }

}



// changeIframeToEditor('id1','id2',...); // id1=id of frame
function changeIframeToEditor()
{
  for(var j=0;j<arguments.length;j++)
   {
     var i=0;
     while(document.getElementsByTagName('iframe')[i])
      { 
       if(document.getElementsByTagName('iframe')[i].id==arguments[j])
        { changetoIframeEditor(document.getElementsByTagName('iframe')[i]); break; }
       i++
      }
   }
}




/////////////////////////////////////////////////////////////////
function controlRows(fid)
{
  var str="<TR class=vdev align=center valign=middle EVENT>\
<TD nowrap style='cursor:pointer'>\
<img src='IURL/bold.gif' title='Bold' class=vdev onclick='doFormatF(\"Bold\")'>\
<img src='IURL/left.gif' title='Left' class=vdev onclick='doFormatF(\"JustifyLeft\")'>\
<img src='IURL/center.gif' title='Center' class=vdev onclick='doFormatF(\"JustifyCenter\")'>\
<img src='IURL/right.gif' title='Right' class=vdev onclick='doFormatF(\"JustifyRight\")'>\
<img src='IURL/outdent.gif' title='Outdent' class=vdev onclick='doFormatF(\"Outdent\")'>\
<img src='IURL/indent.gif' title='Indent' class=vdev onclick='doFormatF(\"Indent\")'>\
<img src='IURL/italic.gif' title='Italic' class=vdev onclick='doFormatF(\"Italic\")'>\
<img src='IURL/under.gif' title='Underline' class=vdev onclick='doFormatF(\"Underline\")'>\
<img src='IURL/strike.gif' title='StrikeThrough' class=vdev onclick='doFormatF(\"StrikeThrough\")'>\
<img src='IURL/superscript.gif' title='SuperScript' class=vdev onclick='doFormatF(\"SuperScript\")'>\
<img src='IURL/subscript.gif' title='SubScript' class=vdev onclick='doFormatF(\"SubScript\")'>\
<img src='IURL/bgcolor.gif' title='Background' class=vdev onclick='selectBgColor()'>\
<img src='IURL/fgcolor.gif' title='Foreground' class=vdev onclick='selectFgColor()'>\
<img src='IURL/image.gif' title='Insert Image' class=vdev onclick='doFormatF(\"InsertImage\")'>\
<img src='IURL/link.gif' title='Create Link' class=vdev onclick='createLink()'>\
<img src='IURL/numlist.gif' title='OrderedList' class=vdev onclick='doFormatF(\"InsertOrderedList\")'>\
<img src='IURL/bullist.gif' title='UnorderedList' class=vdev onclick='doFormatF(\"InsertUnorderedList\")'>\
<img src='IURL/hr.gif' title='HR' class=vdev onclick='doFormatF(\"InsertHorizontalRule\")'>\
<img src='IURL/delformat.gif' title='Delete Format' class=vdev onclick='doFormatF(\"RemoveFormat\")'>\
<img src='IURL/undo.gif' title='Undo' class=vdev onclick='doFormatF(\"Undo\")'>\
<img src='IURL/redo.gif' title='Redo' class=vdev onclick='doFormatF(\"Redo\")'>\
<!-- img src='IURL/cool.gif' title='Emotions' class=vdev onclick='selectEmoticon()'>\
<img src='IURL/wow.gif' title='Characters' class=vdev onclick='characters()' -->\
</TD></TR>"

if(FULLCTRL)
{
str +="\
<TR class=vdev valign=middle align=center EVENT>\
<TD nowrap style='cursor:pointer'>\
<img src='IURL/instable.gif' title='InsertTable' class=vdev onclick='insertTable()'>\
<img src='IURL/tabprop.gif' title='TableProperties' class=vdev onclick='tableProp()'>\
<img src='IURL/cellprop.gif' title='CellProperties' class=vdev onclick='cellProp()'>\
<img src='IURL/inscell.gif' title='InsertCell' class=vdev onclick='insertTableCell()'>\
<img src='IURL/delcell.gif' title='DeleteCell' class=vdev onclick='deleteTableCell()'>\
<img src='IURL/insrow.gif' title='InsertRow' class=vdev onclick='insertTableRow()'>\
<img src='IURL/delrow.gif' title='DeleteRow' class=vdev onclick='deleteTableRow()'>\
<img src='IURL/inscol.gif' title='InsertCol' class=vdev onclick='insertTableCol()'>\
<img src='IURL/delcol.gif' title='DeleteCol' class=vdev onclick='deleteTableCol()'>\
<img src='IURL/mrgcell.gif' title='IncreaseColSpan' class=vdev onclick='morecolSpan()'>\
<img src='IURL/spltcell.gif' title='DecreaseColSpan' class=vdev onclick='lesscolSpan()'>\
<img src='IURL/mrgrow.gif' title='IncreaseRowSpan' class=vdev onclick='morerowSpan()'>\
<img src='IURL/spltrow.gif' title='DecreaseRowSpan' class=vdev onclick='lessrowSpan()'>\
<img src='IURL/div.gif' title='CreateDiv' class=vdev onclick='insertDivLayer()'>\
<img src='IURL/divstyle.gif' title='DivStyle/Delete' class=vdev onclick='editDivStyle()'>\
<img src='IURL/divborder.gif' title='DivBorder' class=vdev onclick='editDivBorder()'>\
<img src='IURL/divfilter.gif' title='DivFilter' class=vdev onclick='editDivFilter()'>\
<!-- img src='IURL/marquee.gif' title='Marquee' class=vdev onclick='doFormatF(\"InsertMarquee\")' -->\
<img src='IURL/all.gif' title='SelectAll' class=vdev onclick='selectAll()'>\
<img src='IURL/cut.gif' title='Cut-IE' class=vdev onclick='doFormatF(\"Cut\")'>\
<img src='IURL/copy.gif' title='Copy-IE' class=vdev onclick='doFormatF(\"Copy\")'>\
<img src='IURL/paste.gif' title='Paste-IE' class=vdev onclick='doFormatF(\"Paste\")'>\
<!-- img src='IURL/chipcard.gif' title='Content Recover/Insert-Smartcard-Data IE' class=vdev onclick='SmartcardData()' -->\
<img src='IURL/search.gif' title='Search/Replace-IE' class=vdev onclick='findText()'>\
<img src='IURL/file.gif' title='Open/Save File-IE' class=vdev onclick='FileDialog()'>\
</TD></TR>\
";
}

str +="<TR class=vdev valign=middle align=center EVENT>\
<TD nowrap style='cursor:pointer'>\
<SELECT name='QBCNTRL0' title='TextStyle' class=vdev onchange='setTextStyle(this.value)' style='width:75px'>\
<OPTION value=''>"+M_DEFTSTYLE+
"<OPTION value='"+M_TSTYLE1+"'>"+M_TSTYLE1T + 
"<OPTION value='"+M_TSTYLE2+"'>"+M_TSTYLE2T + 
"<OPTION value='"+M_TSTYLE3+"'>"+M_TSTYLE3T +
"<OPTION value='"+M_TSTYLE4+"'>"+M_TSTYLE4T +
"<OPTION value='"+M_TSTYLE5+"'>"+M_TSTYLE5T +
"</SELECT>\
<SELECT name='QBCNTRL1' title='FontName' class=vdev onchange='doFormatF(\"FontName,\"+this.value)' style='width:75px'>\
<OPTION value=''>"+M_DEFFONT+
"<OPTION value='"+M_FONT1+"' style='font-family:"+M_FONT1+"'>"+M_FONT1T+ 
"<OPTION value='"+M_FONT2+"' style='font-family:"+M_FONT2+"'>"+M_FONT2T+ 
"<OPTION value='"+M_FONT3+"' style='font-family:"+M_FONT3+"'>"+M_FONT3T+
"<OPTION value='"+M_FONT4+"' style='font-family:"+M_FONT4+"'>"+M_FONT4T+
"<OPTION value='"+M_FONT5+"' style='font-family:"+M_FONT5+"'>"+M_FONT5T+
"</SELECT>\
<SELECT name='QBCNTRL2' title='Headline' class=vdev onchange='doFormatF(\"formatBlock,\"+this.value)' style='width:50px'>\
<OPTION value=''>"+M_HEAD+
"<OPTION value='H1'>H1\
<OPTION value='H2'>H2\
<OPTION value='H3'>H3\
<OPTION value='H4'>H4\
<OPTION value='H5'>H5\
<OPTION value='H6'>H6\
<OPTION value='P'>"+M_REMOVE+"</OPTION>\
</SELECT>\
<SELECT name='QBCNTRL3' title='FontSize' class=vdev onchange='doFormatF(\"FontSize,\"+this.value)' style='width:40px'>\
<OPTION value=3>"+M_FSIZE+
"<OPTION value=7>7\
<OPTION value=6>6\
<OPTION value=5>5\
<OPTION value=4>4\
<OPTION value=3>3\
<OPTION value=2>2\
<OPTION value=1>1\
</OPTION>\
</SELECT>"


if(USEFORM==1)
{
str +="\
<SELECT name='QBCNTRL4' title='FormElements' class=vdev onchange=doFormatF(this.value) style='width:80px'>\
<OPTION value=''>"+M_FORM+
"<OPTION value=InsertFieldset>Fieldset\
<OPTION value=InsertInputButton>Button\
<OPTION value=InsertInputReset>Reset\
<OPTION value=InsertInputSubmit>Submit\
<OPTION value=InsertInputCheckbox>Checkbox\
<OPTION value=InsertInputRadio>Radio\
<OPTION value=InsertInputText>Text\
<OPTION value=InsertSelectDropdown>Dropdown\
<OPTION value=InsertSelectListbox>Listbox\
<OPTION value=InsertTextArea>TextArea\
<OPTION value=InsertButton>IEButton\
<OPTION value=InsertIFrame>IFrame\
</SELECT>";
}

str +="\
<!-- INPUT name='QBCNTRL6' title='QuickSave-IE' value='"+M_QSAVE+"' class=vdev onclick='saveBefore()' type=button style='width:45px' -->\
<INPUT name='QBCNTRL5' title='View/Source' value='"+M_SWAPMODE+"' class=vdev onclick='swapMode()' type=button style='width:70px'>\
<!-- INPUT name='QBCNTRL8' title='Upload files' value='"+M_UPLOAD+"' class=vdev onclick='doUploadFile()' type=button style='width:50px' -->\
"

if(UNICODE) str +="\
<INPUT name='QBCNTRL4' title='Unicode/Iso' value='"+M_SWAPUNI+"' class=vdev onclick='swapUnicode()' type=button style='width:70px'>\
"

if(FULLCTRL)
{
str +="\
<INPUT name='QBCNTRL7' title='View/Iso' value='"+M_SWAPCODE+"' class=vdev onclick='swapCharCode()' type=button style='width:70px'>\
<INPUT name='QBCNTRL9' title='General options' value='"+M_OPTIONS+"' class=vdev onclick='doEditorOptions()' type=button style='width:50px'>\
<INPUT name='QBCNTRL10' title='Help' value='"+M_HELP+"' class=vdev onclick='displayHelp()' type=button style='width:35px'>\
";
}
else
{
str +="<INPUT name='QBCNTRL11' title='Extra functions' value='"+M_EXTRAS+"' class=vdevx onclick='doExtras()' type=button style='width:65px'>"
}

//str +="<INPUT name='QBCNTRL12' title='Change back to textmode' value='"+M_DESTROY+"' class=vdev onclick='destroyEditor()' type=button style='width:25px;'>"

str +="</TD></TR>"

 var iurl=QBPATH + '/imgedit'
 var event="onmousedown='fID=\"" + fid +"\"'"
 str=str.replace(/IURL/g, iurl);
 str=str.replace(/EVENT/g, event);
 return str ;
}



function createEditor(el,id,wi,hi)
{
  if(wi=='' || parseInt(wi)<600) wi=600;
  if(hi=='' || parseInt(hi)<100) hi=100;
  
  var hval='';
  if(el.value) hval=el.value
  hval=hval.replace(/\'/g,"&#39;")
  hval=hval.replace(/&/g,"&amp;")

  var arr=id.split("VDevID")

  var strx="<iframe id="+id+" height="+hi+" width="+wi+" style='background-color:#ffffff;'></iframe>"
  //style='height:"+hi+"; width:"+wi+"'
  strx +="<input name="+arr[1]+" type=hidden value='"+hval+"'></input>"
  var str="<TABLE border=1 cellspacing=0 cellpadding=1 width="+wi+"><tr><td align=center>"
  str +=strx+"</td></tr>"
  str +=controlRows(id);
  str +="</TABLE>" ;

  var parent=el.parentNode;
  var oDiv=document.createElement('div');
  parent.insertBefore(oDiv, el);
  parent.removeChild(el);	 

  oDiv.innerHTML=str
}




function destroyEditor()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var urlx=QBPATH + '/deeditor.html'

  var twidth=300, theight=140;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
//  var newWin1=window.open(urlx,"destroy","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
//  newWin1.moveTo(tposx,tposy);
//  newWin1.focus()
var strx= el.parent.editorContents(fID);
el= el.parent.document.getElementById(fID);
var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode;
var arr = fID.split("VDevID");
var row= (el.height- 28) / 14; row++; row--;
var col= el.width / 6 - 25; col++; col--;
parent.innerHTML= "<textarea name="+arr[1]+" cols="+col+" rows="+row+">" + strx + "</textarea>";
}





function selectEmoticon()
{ 
  var el=document.getElementById(fID).contentWindow;
  if(!el){alert(EDISELECT);return}
  el.focus();
  doFormatDialog('emoticon.html','InsertImage',QBPATH)
}

function selectBgColor()
{ 
  doFormatDialog('selcolor.html',"HiliteColor",'')
}


function selectFgColor()
{ 
  doFormatDialog('selcolor.html','ForeColor','')
}




function doUploadFile()
{
  var el=document.getElementById(fID).contentWindow;
  if(!el){alert(EDISELECT);return}
  el.focus()

  var urlx=QBPATH + '/upload.html'

  var twidth=0.8*screen.width, theight=140;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"upload","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}


function doEditorOptions()
{
  var el=document.getElementById(fID).contentWindow;
  if(!el){alert(EDISELECT);return}
  el.focus()

  var urlx=QBPATH + '/options.html'

  var twidth=0.8*screen.width, theight=190;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"options","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}


function displayHelp()
{
  var urlx=QBPATH + '/edithelp.html'

  var newWin=window.open(urlx,"help","toolbar=no, width=600px,height=400px,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no;scroll=no")
  newWin.focus()
}


function doExtras()
{
  var el=document.getElementById(fID).contentWindow;
  if(!el){alert(EDISELECT);return}
  el.focus()

  var urlx=QBPATH + '/extras.html'
  var twidth=400, theight=20;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 155
  	    	  
  var newWin1=window.open(urlx,"extras","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}




function insertLink(linkurl)
{
  if(!fID && !TXT){alert(ELESELECT);return}
  var strx="<A href='"+linkurl+"' target=nwin>" + linkurl + "</A>"

  if(fID)
  {
    var el=document.getElementById(fID).contentWindow;
    el.focus();
    var sel=el.getSelection();
    var range=sel.getRangeAt(0);
    var container=range.startContainer;
    if(container.nodeType!=3) return; // text or empty
    insertHTML(el,strx);
  }
  else 
  {
    TXT.focus();
    var conts=TXT.value;

    var start=TXT.selectionStart
    var end=TXT.selectionEnd
    var conts1=conts.substring(0,start)
    var conts2=conts.substr(end)

    TXT.value=conts1 + strx + conts2

    var cursor=conts1.length+ strx.length
    TXT.setSelectionRange(cursor,cursor)
  }

}



var curDIV;
function addEventToDiv()
{
  var el=document.getElementById(fID).contentWindow; 
  // add event listen Div
  var objA=el.document.getElementsByTagName('div')
  for(var i=0; i<objA.length;i++)
   { objA[i].addEventListener("click", clickDIV, true) }
}


function clickDIV(e)
{
  curDIV=e.target
}



function insertDivLayer()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()
  
  var sel=el.getSelection();
  var range=sel.getRangeAt(0);
  var container=range.startContainer;
  
  if(container.nodeType!=3) return; // text or empty

  var wrd=sel;
  if(wrd=='') wrd="I'm a DIV-Layer."
  var div="<DIV style='position:relative; font-family:Arial; font-size:12px; background-color:#f0fdd0; border:2px outset; width:150px;'>"+ wrd + "</DIV>" ;
  insertHTML(el,div);
  addEventToDiv()
  return

}



function editDivStyle()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()
  
  if(!curDIV){alert(DIVSELECT); addEventToDiv(); return}

  var urlx=QBPATH + '/divstyle.html'

  var twidth=0.8*screen.width, theight=190;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"divstyle","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}


function editDivBorder()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()

  if(!curDIV){alert(DIVSELECT); addEventToDiv(); return} 

  var urlx=QBPATH + '/divborder.html'

  var twidth=0.8*screen.width, theight=215;
  var tposx=(screen.width-twidth)/2
  var tposy=screen.height-theight - 55
  	    	  
  var newWin1=window.open(urlx,"divborder","toolbar=no,width="+twidth+",height="+theight+",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}




function editDivFilter()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()

  if(!curDIV){alert(DIVSELECT); addEventToDiv(); return} 

  var urlx=QBPATH + '/divfilter.html'

  var twidth=0.8*screen.width,theight=210;
  var tposx=(screen.width-twidth)/2
  var tposy=screen.height-theight-55
  	    	  
  var newWin1=window.open(urlx,"divfilter","toolbar=no,width="+twidth+",height="+theight+",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}




function FileDialog()
{
  var urlx=QBPATH + '/filedialog.html'

  var twidth=0.5*screen.width,theight=100
  var tposx=(screen.width-twidth)/2
  var tposy=screen.height-theight-55
  	    	  
  var newWin1=window.open(urlx,"fdialog","toolbar=no,width="+twidth+",height="+theight+",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy)
  newWin1.focus()
}




function initDefaultOptions0(fID)
{
   setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.fontFamily='"+DFFACE+"'",200)
   setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.fontSize='"+DFSIZE+"'",200)
   setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.color='"+DCOLOR+"'",200)
   setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.backgroundColor='"+DBGCOL+"'",200)
   setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.backgroundImage='url("+DBGIMG+")'",200)
   // setTimeout("CSS['"+fID+"']=document.getElementById('"+fID+"').contentWindow.document.createStyleSheet('"+DCSS+"')",200)
   FACE[fID]=DFFACE;
   SIZE[fID]=DFSIZE;
   COLOR[fID]=DCOLOR;
   BCOLOR[fID]=DBGCOL;
   BIMAGE[fID]=DBGIMG;
}






function DefaultOptions(linex)
{
  var retArr=new Array('','','','','','','');
  var tempx,strx,objx,idx;


  // DEFAULT DIV
  var idx=linex.indexOf('ViEtDeVdIvId')
  if(idx>=0) 
   {
     strx=linex.substring(linex.indexOf('style="')+7,linex.indexOf('">'))

     var atrA=strx.split(";")
     for(var i=0;i<atrA.length;i++)
      {
       tempx=atrA[i].split(':')
       switch(tempx[0].toUpperCase())
        {
         case "FONT-FAMILY":retArr[0]=tempx[1]; break;
	 case "FONT-SIZE":retArr[1]=tempx[1]; break;
	 case "BACKGROUND-COLOR":retArr[2]=tempx[1]; break;
	 case "COLOR":retArr[3]=tempx[1]; break;
	 case "BACKGROUND-IMAGE":if(tempx[2]) tempx[1] +=':'+tempx[2];
			 retArr[4]=tempx[1].substring(tempx[1].indexOf('url(')+4,tempx[1].indexOf(')') ); 
									 break;
	}
      }

    linex=""+ />.*<\/div>/i.exec(linex)
    linex=linex.substring(1,linex.length-6)	
   }


   // EXT STYLE
   idx=linex.indexOf('<style>@import url("')
   if( idx>=0 )
    {
     var strx=linex.substring(idx+20, linex.indexOf('")'))
     retArr[5]=strx
     linex=linex.substring(0,idx)
    }

   retArr[6]=linex

   return retArr

}





function initDefaultOptions1(linex,fID)
{
  var retArr=new Array();

  retArr=DefaultOptions(linex);

  setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.fontFamily='"+retArr[0]+"'",200)
  setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.fontSize='"+retArr[1]+"'",200)
  setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.backgroundColor='"+retArr[2]+"'",200)
  setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.color='"+retArr[3]+"'",200)
  setTimeout("document.getElementById('"+fID+"').contentWindow.document.body.style.backgroundImage='url("+retArr[4]+")'",200)
  setTimeout("CSS['"+fID+"']=document.getElementById('"+fID+"').contentWindow.document.createStyleSheet('"+retArr[5]+"')",200)
  FACE[fID]=retArr[0];
  SIZE[fID]=retArr[1];
  COLOR[fID]=retArr[3];
  BCOLOR[fID]=retArr[2];
  BIMAGE[fID]=retArr[4];

  return retArr[6]

}




function actualize()
{
  var i=0;
  while(document.getElementsByTagName('iframe')[i])
   setHiddenValue(document.getElementsByTagName('iframe')[i++].id) 
}



function setHiddenValue(fid)
{ 
 if(!fid) return

 var strx=editorContents(fid)

 var idA=fid.split('VDevID')
 if(!idA[0]) return;

 var fobj=document.forms[idA[0]]
 if(!fobj) return;

 var loc=location.href
 loc=loc.substring(0,loc.lastIndexOf('/'))
 if(! /http:\/\//.test(loc) || /http\:\/\/127\.0\.0\.1/.test(loc) || /http\:\/\/localhost/.test(loc))
  {
   loc=loc.replace(/\//g,"\\/")
   loc=loc.replace(/\./g,"\\.")
   var reg=eval("/"+loc+"/g");
   strx=strx.replace(reg,".")
  }

 strx=exchangeTags(strx,"<div>","</div>","",""); //delete trick div
 fobj[idA[1]].value=strx

}	





function doCleanCode(strx,fid) 
{    
  strx=strx.replace(/\r/g,""); 
  strx=strx.replace(/\n>/g,">"); 
  strx=strx.replace(/>\n/g,">"); 
  strx=strx.replace(/\\/g,"&#92;");
  strx=strx.replace(/\'/g,"&#39;")

  // Security
  if(SECURE==1)
   {
     strx=strx.replace(/<meta/ig, "< meta"); 
     strx=strx.replace(/&lt;meta/ig, "&lt; meta"); 
     strx=strx.replace(/<script/ig, "< script"); 
     strx=strx.replace(/&lt;script/ig, "&lt; script"); 
     strx=strx.replace(/<\/script/ig, "< /script"); 
     strx=strx.replace(/&lt;\/script/ig, "&lt; /script"); 
     strx=strx.replace(/<iframe/ig, "< iframe"); 
     strx=strx.replace(/&lt;iframe/ig, "&lt; iframe"); 
     strx=strx.replace(/<\/iframe/ig, "< /iframe"); 
     strx=strx.replace(/&lt;\/iframe/ig, "&lt; /iframe"); 
     strx=strx.replace(/<object/ig, "< object"); 
     strx=strx.replace(/&lt;object/ig, "&lt; object"); 
     strx=strx.replace(/<\/object/ig, "< /object"); 
     strx=strx.replace(/&lt;\/object/ig, "&lt; /object"); 
     strx=strx.replace(/<applet/ig, "< applet"); 
     strx=strx.replace(/&lt;applet/ig, "&lt; applet"); 
     strx=strx.replace(/<\/applet/ig, "< /applet"); 
     strx=strx.replace(/&lt;\/applet/ig, "&lt; /applet"); 
     strx=strx.replace(/ on/ig, " o&shy;n"); 
     strx=strx.replace(/script:/ig, "script&shy;:"); 
   }

  var idx=strx.indexOf('ViEtDeVdIvId')
  if(idx>=0) strx=strx.substring(strx.indexOf('>')+1,strx.lastIndexOf('</DIV>'))

  idx=strx.indexOf('<style>@import url(')
  if(idx>=0) strx=strx.substring(0,idx)
  if(CSS[fid] && CSS[fid].href) strx +='<style>@import url("'+CSS[fid].href+'");</style>';


  var defdiv="" ;
  if(FACE[fid]) defdiv +="; FONT-FAMILY:"+ FACE[fid] 
  if(SIZE[fid]) defdiv +="; FONT-SIZE:"+ SIZE[fid]
  if(COLOR[fid]) defdiv +="; COLOR:"+ COLOR[fid]
  if(BCOLOR[fid])defdiv +="; BACKGROUND-COLOR:"+ BCOLOR[fid]
  if(BIMAGE[fid] && BIMAGE[fid]!='about:blank')
   {
     BIMAGE[fid]=BIMAGE[fid].replace(/\\/g,"/"); 
     defdiv +="; BACKGROUND-IMAGE:url("+ BIMAGE[fid]+")"
   }
  if(defdiv)
   {
    defdiv='<DIV id=ViEtDeVdIvId style="POSITION:Relative' + defdiv + '">'
    strx=defdiv + strx + "</DIV>"
   }


  // From Valerio Santinelli, PostNuke Developer,(http://www.onemancrew.org)
  // removes all Class attributes on a tag eg. '<p class=asdasd>xxx</p>' returns '<p>xxx</p>'    
     //code=code.replace(/<([\w]+) class=([^ |>]*)([^>]*)/gi, "<$1$3")
  // removes all style attributes eg. '<tag style="asd asdfa aasdfasdf" something else>' returns '<tag something else>'
     //code=code.replace(/<([\w]+) style=\"([^\"]*)\"([^>]*)/gi, "<$1$3")
  // gets rid of all xml stuff... <xml>,<\xml>,<?xml> or <\?xml>
     //code=code.replace(/<]>/gi">\\?\??xml[^>]>/gi, "")
  // get rid of ugly colon tags <a:b> or </a:b>
     //code=code.replace(/<\/?\w+:[^>]*>/gi, "")
  // removes all empty <p> tags
     strx=strx.replace(/<p([^>])*>(&nbsp;)*\s*<\/p>/gi,"")
  // removes all empty span tags
     strx=strx.replace(/<span([^>])*>(&nbsp;)*\s*<\/span>/gi,"")
  return strx
}




/*
function addEventToObj()
{
  // Textarea
  var oArr=document.getElementsByTagName("textarea")
  var i=-1
  while(oArr[++i])
   {
     oArr[i].addEventListener("mousedown",doMDown,false);
     oArr[i].addEventListener("mouseup",doMUp,false);
   }

  // Input
  oArr=document.getElementsByTagName("input")
  i=-1
  while(oArr[++i])
   {
     oArr[i].addEventListener("mousedown",doMDown,false);
     oArr[i].addEventListener("mouseup",doMUp,false);
   }
}


addEventToObj();
*/

function addEventToObj2()
{
  // input
  var oArr= document.getElementsByTagName("input")
  var i=-1
  while(oArr[++i])
   {
     oArr[i].addEventListener("mousedown", doMDown, false);
     oArr[i].addEventListener("mouseup", doMUp, false);
   }

  // Input
  oArr= document.getElementsByTagName("input")
  i=-1
  while(oArr[++i])
   {
     oArr[i].addEventListener("mousedown", doMDown, false);
     oArr[i].addEventListener("mouseup", doMUp, false);
     if(oArr[i].type!="text") continue
   }
}



function editorContents(fid)
{
  fID=fid
  var el=document.getElementById(fid).contentWindow;
  if(!el)return

  var strx, strx1;
  if(format[fid]=="HTML")
  {
   if(curTD)
    { 
     curTD.setAttribute("bgcolor",oldCOLOR);curTD=null 
     curTB.setAttribute("bgcolor",oldCOLOR1);curTB=null 
    }
   swapMode()
   strx=objInnerHTML(el);
   swapMode()
  }
  else
   strx=objInnerHTML(el);

  strx=doCleanCode(strx,fid);

  return strx
}




function setTextStyle(tstyle)
{
  if(!tstyle) return

  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()
  var edit=el.document; 

  var sArr=tstyle.split(",") // FontName,ForeColor,HiliteColor,FontSize,Italic

  edit.execCommand("RemoveFormat",false,false) 

  if(sArr[0]) edit.execCommand("FontName",false,sArr[0]) 
  if(sArr[1]) edit.execCommand("ForeColor",false,sArr[1]) 
  if(sArr[2]) edit.execCommand("HiliteColor",false,sArr[2])
  if(sArr[3]) edit.execCommand("FontSize",false,sArr[3])
  if(sArr[4]) edit.execCommand("Italic",false,sArr[4])

}

function doFormatF(arr)
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()

  var cmd=new Array();
  cmd=arr.split(',')

  var edit=el.document; 
  if(cmd[0]=='formatBlock')
   {
    edit.execCommand(cmd[0],false,"<"+cmd[1]+">");
    if(cmd[1]=='PRE' && format[fID]=="HTML") swapMode();
   }
  else if(cmd[0]=='InsertImage'&&!cmd[1]) insertImage();
  else if(cmd[0]=='InsertImage') insertImageSimple(el,cmd[1]);
  else if(cmd[0]=='FontName' && (cmd[1]=='Webdings'||cmd[1]=='Windings') )
   {
    el.document.execCommand("useCSS",false,true); 
    edit.execCommand(cmd[0],false,cmd[1]) 
    el.document.execCommand("useCSS",false,false); 
   }
  else if(cmd[1]!=null) edit.execCommand(cmd[0],false,cmd[1]) 
  else edit.execCommand(cmd[0],false,null)
}




function insertImageSimple(el,cmd)
{
  var html='<img src="'+cmd+'">'

  insertHTML(el,html)

  // add eventlisten Image
  var imgA=el.document.getElementsByTagName('img')
  for(var i=0;i<imgA.length;i++)
     imgA[i].addEventListener("click", clickIMG, true)
}



function clickIMG(e)
{
 curIMG=e.currentTarget
}



function swapCharCode()
{
 var el=document.getElementById(fID).contentWindow; 
 if(!el){alert(EDISELECT);return}
 el.focus()

 var eStyle=el.document.body.style;
 var strx;
 if(format[fID]=="HTML")
 {
  swapMode();
  strx=el.document.body.innerHTML
  format[fID]="Text"
 }
 else if(viewm[fID]==0)
 {
  strx=el.document.body.innerHTML
  strx=strx.replace(/\&amp;#/g,"&#")
  el.document.body.innerHTML=strx
  viewm[fID]=1 - viewm[fID]
  return
 }
 else strx=el.document.body.innerHTML

 if(viewm[fID]) strx=toUnicode(strx)

 strx=strx.replace(/\&#/g,"&amp;#")
 el.document.body.innerHTML=strx
 
 viewm[fID]=1 - viewm[fID]
}



function toUnicode(str1)
{
  var code, str2 , j=0;
  var len
  while(j<2)
   {
    len=str1.length
    str2=''
    for(var i=0;i<len;i++) 
     {
      code=str1.charCodeAt(i);
      if(code<128) continue;
      str2 +=str1.substring(0,i) + '&#' + code + ';'
      str1=str1.substring(i+1,str1.length)
      len=str1.length
      i=0
     }
    str1=str2+str1
    j++;
   }
  return str1;
}



function swapMode()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}
  el.focus()

 var eStyle=el.document.body.style	 
 if(format[fID]=="HTML") // view -> source code
 {
  if(curTD)
   { 
    curTD.setAttribute("bgcolor",oldCOLOR); curTD=null 
    curTB.setAttribute("bgcolor",oldCOLOR1); curTB=null 
   }

  FACE[fID]=eStyle.fontFamily
  SIZE[fID]=eStyle.fontSize
  COLOR[fID]=eStyle.color
  BCOLOR[fID]=eStyle.backgroundColor
  BIMAGE[fID]=eStyle.backgroundImage
  BIMAGE[fID]=BIMAGE[fID].substring( BIMAGE[fID].indexOf('(')+1,BIMAGE[fID].indexOf(')') )

  eStyle.fontFamily="";
  eStyle.fontSize="12pt"
  eStyle.fontStyle="normal"
  eStyle.color="black"
  eStyle.backgroundColor="#e0e0f0"
  eStyle.backgroundImage=''

  el.document.body.innerHTML=objInnerText(el);
  format[fID]="Text"
 }
 else // source code -> preview
 {
  eStyle.fontFamily=FACE[fID]
  eStyle.fontSize=SIZE[fID]
  eStyle.color=COLOR[fID]
  eStyle.backgroundColor=BCOLOR[fID]
  eStyle.backgroundImage="url(" + BIMAGE[fID] + ")"
  

  el.document.body.innerHTML=objInnerHTML(el);
  format[fID]="HTML"
  viewm[fID]=1

  // add event listen
  var tdA=el.document.getElementsByTagName('td')
  for(var i=0; i<tdA.length;i++)
   { tdA[i].addEventListener("click", clickTD, true) }

  // add event listen Images
  var imgA=el.document.getElementsByTagName('img')
  for(var i=0; i<imgA.length;i++)
    imgA[i].addEventListener("click", clickIMG, true)

  // add event listen Div
  addEventToDiv()

 }


}




function objInnerText(el)
{
  var con=el.document.body.innerHTML
  con=con.replace(/<br>\r\n/g,"<br>");
  con=con.replace(/&/g,"&amp;");
  con=con.replace(/\</g,"&lt;");

  con=exchangeTags(con,"&lt;div>","&lt;/div>","",""); //delete trick div

  con=con.replace(/>&lt;table/ig,"><br>&lt;table");
  con=con.replace(/>&lt;tbody/ig,"><br>&lt;tbody");
  con=con.replace(/>&lt;tr/ig,"><br>&lt;tr");
  con=con.replace(/>&lt;td/ig,"><br>&lt;td");

  return con;
}



function objInnerHTML(el) 
{
  var con=el.document.body.innerHTML;

  con=con.replace(/\r\n/g," ");
  con=con.replace(/&amp;lt;/g,"&amp;amp;lt;");
  con=con.replace(/&amp;/g,"&");
  con=con.replace(/&lt;/g,"<");
  con=con.replace(/&gt;/g,">");
  con=con.replace(/&amp;lt;/g,"&lt;");

  con=con.replace(/><br>( *?)<table/ig,"><table");
  con=con.replace(/><br>( *?)<tbody/ig,"><tbody");
  con=con.replace(/><br>( *?)<tr/ig,"><tr");
  con=con.replace(/><br>( *?)<td/ig,"><td");

  return con;
}




function selectAll()
{ 
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var sel=el.getSelection()
  var range=sel.getRangeAt(0);
  var body=el.document.getElementsByTagName("body")[0]
  range.selectNodeContents(body)
  el.focus()
}





function highLight(key)
{
  switch(key)
  {  
   case 48: doFormatF('RemoveFormat'); break; // ctrl+0  no highlight
   case 49: doFormatF('ForeColor,red'); break; // ctrl+1
   case 50: doFormatF('ForeColor,green'); break; // ctrl+2
   case 51: doFormatF('ForeColor,blue'); break; // ctrl+3
   case 52: doFormatF('ForeColor,#00AAFF'); break; // ctrl+4
   case 53: doFormatF('ForeColor,magenta'); break; // ctrl+5
   case 54: doFormatF('HiliteColor,yellow'); doFormatF('ForeColor,black'); break; // ctrl+6
   case 55: doFormatF('HiliteColor,cyan'); doFormatF('ForeColor,black'); break; // ctrl+7
   case 56: doFormatF('HiliteColor,#00FF00'); doFormatF('ForeColor,black'); break; // ctrl+8
   case 57: doFormatF('HiliteColor,#FF00AA'); doFormatF('ForeColor,white'); break; // ctrl+9
  }
}




function FKPress(e)
{
  var o=document.getElementById(fID).contentWindow; 
  if(!o){alert(EDISELECT); return}

  if(e.ctrlKey && (e.charCode==99 || e.charCode==120)) return;  // Ctrl+C or X

  var key=e.charCode
  var stop=false
  if(e.ctrlKey)
   {
     switch(key)
	{
	 case 98: o.document.execCommand("Bold", false, null); stop=true; break; // Ctrl+b
	 case 105: o.document.execCommand("Italic", false, null); stop=true; break; // Ctrl+i
	 case 117: o.document.execCommand("Underline", false, null); stop=true; break; // Ctrl+u
         case 71: findText(); stop=true ; break; // ctrl+G search
	 case 75: findTextHotKey(0); stop=true; break; // ctrl+K  search forward
	 case 74: findTextHotKey(1); stop=true; break; //ctrl+J  search backward 
	 case 83: if(SYMBOLE!=''){ SmartcardData(); stop=true }; break; // ctrl+S 
	 case 84: swapMode(); stop=true; break; // ctrl+T swapMode
	 case 48:case 49:case 50:case 51:case 52:
	 case 53:case 54:case 55:case 56:case 57: highLight(key); stop=true; break; // ctrl 0-9 Highlight
	}
    if(stop==true){ e.preventDefault(); return}
  }

}


/*
You can use for inserting any Html-Tag into Editor
at cursor postion.
*/
function insertHTML(e,html)
{
  e.focus();
  
  var div=e.document.createElement("div");
  div.innerHTML=html
  var child=div.firstChild
  if(!child.nextSibling) insertNodeAtSelection(e, child)
  else insertNodeAtSelection(e, div)
}




/*** This function comes original from Mozdev.org (s. Midas-Demo)***/
function insertNodeAtSelection(win, insertNode)
{
   var afterNode;

   // get current selection
   var sel=win.getSelection();

   var range=sel.getRangeAt(0);

   sel.removeAllRanges();
   range.deleteContents();

   var container=range.startContainer;
   var pos=range.startOffset;

   // make a new range for the new selection
   range=document.createRange();

   if (container.nodeType==3 && insertNode.nodeType==3) 
	{
        // if we insert text in a textnode, do optimized insertion
        container.insertData(pos, insertNode.nodeValue);

        // put cursor after inserted text
        range.setEnd(container, pos+insertNode.length);
        range.setStart(container, pos+insertNode.length);

   } 
   else 
   {
     if (container.nodeType==3) 
      {
       // when inserting into a textnode
       // we create 2 new textnodes and put the insertNode in between
       var textNode=container;
       container=textNode.parentNode;
       var text=textNode.nodeValue;

       // text before the split
       var textBefore=text.substr(0,pos);
       // text after the split
       var textAfter=text.substr(pos);

       var beforeNode=document.createTextNode(textBefore);
       var afterNode=document.createTextNode(textAfter);

       // insert the 3 new nodes before the old one
       container.insertBefore(afterNode, textNode);
       container.insertBefore(insertNode, afterNode);
       container.insertBefore(beforeNode, insertNode);

        // remove the old node
        container.removeChild(textNode);

      } 
	  else 
	  {
        // else simply insert the node
        afterNode=container.childNodes[pos];
        container.insertBefore(insertNode, afterNode);
      }

      //range.setEnd(afterNode, 0);
      //range.setStart(afterNode, 0);
    }

   // sel.addRange(range);
};





function exchangeTags(text,oOpen,oClose,nOpen,nClose)
{
  var str1, str2, idx, idx1;
  var len1=oOpen.length;
  var len2=oClose.length;

  var oOpen1=oOpen.substring(0,len1-1)
  var chr1=oOpen1.replace(/^(.)/,"$1TrickTag");
  var chr2=oClose.replace(/^(.)/,"$1TrickTag");
  var oOpen2

  while(1)
   {
    str1=''
    while(2)
    {
     idx=text.indexOf(oClose);
     if(idx<0) break;
     str2=text.substring(0,idx);
 
     text=text.substr(idx+len2);
     idx1=str2.lastIndexOf(oOpen1)
     idx=str2.lastIndexOf(oOpen)

     if(idx1>=0 && idx>=0 && idx1>idx)
     {
      oOpen2=str2.substring(idx1+len1-1,idx1+len1)
      str1 +=str2.substring(0,idx1) + chr1+oOpen2;
      str1 +=str2.substr(idx1+len1) + chr2;
      break;
     }
     else if(idx>=0)
     {
      str1 +=str2.substring(0,idx) + nOpen ;
      str1 +=str2.substr(idx+len1) + nClose
     }
     else str1 +=str2 + oClose
    } // while2

   str1 +=text
   if(str1.indexOf(oOpen)<0) break
   text=str1; 
  } // while1

  str1=str1.replace(/TrickTag/g,"")
  return str1;
}





function formatDialog()
{
  var urlx=QBPATH + '/dialog.html'

  var twidth=400;
  var theight=350;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()
}




function createLink()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var urlx=QBPATH + '/createlink.html'

  var twidth=350;
  var theight=150;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}



function insertImage()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var urlx=QBPATH + '/insertimage.html'

  var twidth=500;
  var theight=170;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}



function doFormatDialog(file,cmd,arg)
{ 
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var urlx=QBPATH + '/' + file +"?"+cmd

  var twidth=350;
  var theight=300;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}




function characters()
{
  var el=document.getElementById(fID).contentWindow; 
  if(!el){alert(EDISELECT);return}

  var urlx=QBPATH + '/selchar.html'

  var twidth=350;
  var theight=400;
  var tposx=(screen.width- twidth)/2
  var tposy=screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}



if(USETABLE) document.writeln('<script src="'+QBPATH+'/tabedit.js"></script>');
if(UNICODE) document.writeln('<script src="'+QBPATH+'/unicode.js"></script>');



// VISUAL=0 : Textarea to Editor after confirmation
// VISUAL=1 : all Textarea to Editor
// VISUAL=2 : change only specific textarea
// VISUAL=3 : all Iframe to Editor
// VISUAL=4 : some specific iframes 
// VISUAL=other : no Visual-Editor, only use Rightmouse-Control
switch(VISUAL)
{
  case 1: changeAllTextareaToEditors(); break;
  case 2: changetoIframeEditor(document.forms[xxx].yyy); break;// please replace xxx=formIndex and yyy=textareaName
  case 3: changeAllIframeToEditors(); break;
  case 4: changeIframeToEditor('contents2'); break;//please replace contents..=frame id
}





function doMDown(e)
{
 var el=e.currentTarget
 
 var button=e.which

 if(el.type=='text' || el.type=='input')
   {
    TXT=el; fID=''
    if(button>1 && POPWIN==1){ formatDialog();}
   }
}



function doMUp(e)
{
 var el=e.currentTarget
 if(!el.type) return
 var fidx=fID

 if(el.type!='text'&&el.type!='input'&&el.type!='password'&&el.type!='file')
  {
   if(!el.name || el.name.substring(0,7)!='QBCNTRL')
   { 
     actualize(); 
     if(el.type !='select-one' && el.type !='select-multiple') el.focus(); 
   }

   fID=fidx
   return
  }

 var visual=''
 if(typeof(ASKED)=="undefined" && el.type=='input' && VISUAL==0)
  { visual=confirm(VISMODE); if(!visual) ASKED=1; }
 	 
 if(visual){ changetoIframeEditor(el); userInit() }
 else{ TXT=el; fID=null }
}

/* for use if user you to add function after creating editor */
function userInit(){};


function findText()
{
  if(!fID && !TXT){alert(EDISELECT);return}
  if(fID) document.getElementById(fID).contentWindow.focus();
  else TXT.focus()

  var urlx=QBPATH + '/dfindtext.html'

  var newWin=window.open(urlx,"find","toolbar=no, width=350px,height=220px,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no;scroll=no")
  newWin.moveTo(screen.width-500,50);
  newWin.focus()
}



function FMUp(e)
{ 
 curDIV=null; curIMG=null;
}

/* Changes made to this file:
   Replace all 'input' with 'input'
   Comment out addEventToObj() and add addEventToObj2 ()
   Additionarl Functions:
 */

function myFunction() {
	if (VISUAL)
	{
		destroyEditor();
		VISUAL =0;
	} else {
		changetoIframeEditor(document.form[26]);
		VISUAL = 10;
	}

}
