/* DIALOG POPWIN **/
function formatDialogF()
{
  var y = screen.height -parseInt('30em')*14 - 30 
  var feature = "font-family:Arial;font-size:10pt;dialogWidth:30em;dialogHeight:27em;dialogTop:"+y
      feature+= ";edge:sunken;help:no;status:no"

  var dialog= QBPATH+'/dialog.html'
  var arr= showModalDialog(dialog, "visual", feature);
  if(arr==null) return ; 

  doFormatF(arr)
  	  
}

function formatDialog()
{
  TXTOBJ.focus();
  var caret=TXTOBJ.document.selection.createRange()
  TXTOBJ.curword=caret.duplicate();
  
  var y = screen.height -parseInt('27em')*14 - 30 
  var feature = "font-family:Arial;font-size:10pt;dialogWidth:30em;dialogHeight:27em;dialogTop:"+y
      feature+= ";edge:sunken;help:no;status:no"

  var dialog= QBPATH+'/dialog.html'
  var arr= showModalDialog(dialog, "", feature);
  if(arr==null) return ;

  doFormat(arr,caret)

}


/* RETURN-NEWLINE */
function insertNewline(obj)
{
  if(obj.document.selection.type=='Control') return;
  var Range = obj.document.selection.createRange();
  if(!Range.duplicate) return;
  Range.pasteHTML('<br>.');
  Range.findText('.',10,5)
  Range.select();
  obj.curword=Range.duplicate();
  obj.curword.text = ''; 
  Range.select();
}

function insertNewParagraph(obj)
{
  if(obj.document.selection.type=='Control') return;
  var Range = obj.document.selection.createRange();
  if(!Range.duplicate) return;
  Range.pasteHTML('<P>.');
  Range.findText('.',10,5)
  Range.select();
  obj.curword=Range.duplicate();
  obj.curword.text = '' ;
  Range.select();

}


/* CONFIRMATION UNLOAD*/
if(UNLOADCON) window.onbeforeunload= saveBefore

function saveBefore()
{
  if(!document.all){ alert(NOTSAVEABLE); return }

  if(typeof(ASKED)!="undefined") return;

  actualize()
  var arr= isNeedSave()
  if(!arr) return
  
  var yes= confirm(TOCLIPBRD)
  if(yes) savetoClipboard(arr)
  ASKED=1
}


function isNeedSave()
{
   if(!document.forms || document.forms.length==0) return;

   var fidx, el;
   var oForm, strx1='', conts='', afield=null, fIDx ;

   for(fidx=0; fidx<document.forms.length; fidx++)
    {
	 oForm= document.forms[fidx]
     for(var i=0; i<oForm.elements.length; i++)
     {
      el= oForm.elements[i]
      if(el.type!='text' && el.type!='textarea' && el.type!='hidden') continue

      fIDx= fidx +'VDevID'+ el.name
      if(!afield && el.type=='hidden' && document.frames[fIDx]) afield= document.frames[fIDx]
      if(conts=='' && el.type=='hidden' && document.frames[fIDx]) 
		conts= el.document.frames[fIDx].document.body.innerText
      strx1 += el.value + SYMBOLE ;
     }
	}
   strx1 += "END" ;

   if(!afield || conts=='') return;

   var arr= new Array(afield,strx1)

   return arr;

}


function savetoClipboard(arr)
{
   var afield= arr[0];
   var strx1= arr[1];

   var strx2= afield.document.body.innerHTML
   afield.document.body.innerText= strx1 ;

   var rng= afield.document.body.createTextRange()
   rng.execCommand('SelectAll')
   rng.execCommand("Copy");

   afield.document.body.innerHTML=strx2;
}


/* SMARTCARD RECOVER */
function SmartcardData()
{
  if(!document.forms || document.forms.length==0) return ;

  var fidx, oForm, el , objF=null ;
  var fIDx, linex, lidx=0;
  for(fidx=0; fidx<document.forms.length; fidx++)
	{
	 oForm= document.forms[fidx]
	 for(var i=0; i<oForm.elements.length; i++)
	  {
       el= oForm.elements[i]
       if(el.type!='hidden') continue
        
       fIDx= fidx +'VDevID'+ el.name
	   if(document.frames[fIDx]){ objF=document.frames[fIDx]; break;}
     } // end for i
	} // end for fidx

  if(!objF) return;

  objF.document.body.innerText=''
  var s=objF.document.body.createTextRange()
  s.execCommand('Paste')
  var cbstr= objF.document.body.innerText
  objF.document.body.innerText=''

  var cbArr= cbstr.split(SYMBOLE);
  for(fidx=0; fidx<document.forms.length; fidx++)
	{
	 oForm= document.forms[fidx]
	 for(var i=0; i<oForm.elements.length; i++, linex='')
	  {
       el= oForm.elements[i]
       if(el.type!='text' && el.type!='textarea' && el.type!='hidden') continue
    
	   linex= cbArr[lidx++];
    
       fIDx= fidx +'VDevID'+ el.name
	   if(el.type=='hidden' && document.frames[fIDx] && linex) initDefaultOptions2(linex,fIDx)
	   else if(el.type!='hidden') el.value= linex;
     } // end for i
	} // end for fidx
  
}

function initDefaultOptions2(linex,fIDx)
{
  var oFrame= document.frames[fIDx]
  var oStyle= oFrame.document.body.style

   // remove old Style
  var oSS= DEFCSS[fIDx]
  if(oSS) for(var i=0; i<oSS.rules.length; i++) oSS.removeRule(i);
  DEFCSS[fIDx]= null 

  var retArr= new Array();

  retArr= DefaultOptions(linex);

  oStyle.fontFamily=retArr[0]
  oStyle.fontSize=retArr[1]
  oStyle.color=retArr[3]
  oStyle.backgroundColor=retArr[2]
  oStyle.backgroundImage= "url("+retArr[4]+")"
  DEFCSS[fIDx]= oFrame.document.createStyleSheet(retArr[5])
  DEFFFACE[fIDx]= retArr[0];
  DEFFSIZE[fIDx]= retArr[1];
  DEFBCOLOR[fIDx]= retArr[2];
  DEFCOLOR[fIDx]= retArr[3];
  DEFBIMAGE[fIDx]= retArr[4];

  oFrame.document.body.innerHTML= retArr[6];

}
