/************************/
function saveBefore()
{
  actualize()
  var arr= isNeedSave()
  if(!arr) return
  
  savetoClipboard(arr)
  alert('Your work is now in clipboard.\n You can recover it later with button "content recover".')
}


function isNeedSave()
{
   if(!document.forms || document.forms.length==0) return;

   var fidx, el;
   var oForm, strx1='', afield=null, fIDx ;

   for(fidx=0; fidx<document.forms.length; fidx++)
    {
	 oForm= document.forms[fidx]
     for(var i=0; i<oForm.elements.length; i++)
     {
      el= oForm.elements[i]
      if(el.type!='text' && el.type!='textarea' && el.type!='hidden') continue

      fIDx= fidx +'VDevID'+ el.name
      if(!afield && el.type=='hidden' && document.frames[fIDx]) afield= document.frames[fIDx]
	  var temp= el.value
	  temp= temp.replace(/\r/g,'');	temp= temp.replace(/\n/g,'&#13;');
      strx1 += temp + SYMBOLE ;
     }
	}
   strx1 += SYMBOLE ;

   if(!afield) return;

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





function SmartcardData() // insert from clipboard
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
	   else if(el.type!='hidden') el.value= linex.replace(/&#13;/g,"\n");
     } // end for i
	} // end for fidx
  
}




function initDefaultOptions2(linex,fIDx)
{
  var oFrame= document.frames[fIDx]
  var oStyle= oFrame.document.body.style

   // remove old Style
  var oSS= CSS[fIDx]
  if(oSS) for(var i=0; i<oSS.rules.length; i++) oSS.removeRule(i);
  CSS[fIDx]= null 

  var retArr= new Array();

  retArr= DefaultOptions(linex);

  oStyle.fontFamily=retArr[0]
  oStyle.fontSize=retArr[1]
  oStyle.color=retArr[3]
  oStyle.backgroundColor=retArr[2]
  oStyle.backgroundImage= "url("+retArr[4]+")"
  CSS[fIDx]= oFrame.document.createStyleSheet(retArr[5])
  FACE[fIDx]= retArr[0];
  SIZE[fIDx]= retArr[1];
  BCOLOR[fIDx]= retArr[2];
  COLOR[fIDx]= retArr[3];
  BIMAGE[fIDx]= retArr[4];
  var conts= retArr[6].replace(/&#13;/g,"\n")
  conts= conts.replace(/&#39;/g,"\'")
  oFrame.document.body.innerHTML= conts;
}