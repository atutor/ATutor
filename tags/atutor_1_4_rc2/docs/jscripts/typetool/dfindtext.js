var objF;
if(window.opener.fID) objF= window.opener.document.frames[window.opener.fID];
else objF= window.opener.TXTOBJ;

function init()
{
  var fobj= document.forms[0];

  var rng = objF.document.selection.createRange();
  rng.select();

  var word=''
  objF.curword=rng.duplicate();
  word= objF.curword.text;
  if(word=='') { word= window.opener.FWORD }
  fobj.fword.value= word
  fobj.rword.value= window.opener.RWORD
  if(fobj.fword.value=='undefined') fobj.fword.value=''
  if(fobj.rword.value=='undefined') fobj.rword.value=''

  var flags=window.opener.FLAGS;

  if(flags==2) fobj.match2.checked=true
  if(flags==4) fobj.match3.checked=true
  if(flags==6){ fobj.match3.checked=true; fobj.match2.checked=true }

  fobj.elements[0].focus(); fobj.elements[0].select();

}


function goFind(forward,replace)
{
  var fobj= document.forms[0];

  var sel = objF.document.selection;
  if(sel.type=="Control") return 0

  var fword= fobj.fword.value
  if(fword=='') return 0;
  var rword= fobj.rword.value

  window.opener.FWORD= fword
  window.opener.RWORD= rword
  window.opener.FLAGS=0
  var flags=0;
  if(!forward) flags += 1 ; // backwards
  if(fobj.match2.checked){ flags += 2; window.opener.FLAGS +=2 } // whole words only
  if(fobj.match3.checked){ flags += 4; window.opener.FLAGS +=4 } // case insensitive


  var rng = objF.document.selection.createRange();
  objF.curword=rng.duplicate();


  if(objF.curword.text != '')
   {
     if(!forward) rng.moveEnd("character", -1 );  
	 else rng.moveStart("character", 1 );  
   }

  if(rng.findText(fword,100000,flags)==true)
   {
	 rng.select();  
	 rng.scrollIntoView();
	 if(replace) 
	   {
         rng= objF.document.selection.createRange();
		 objF.curword=rng.duplicate();
		 objF.curword.text=rword
		 if(forward && rword!='') rng.findText(rword,100000,flags)
		 else if(rword!='') rng.findText(rword,100000,flags-1)
		 rng.select()
	   }
     return 1;
   }

  return 0 
}



function goReplaceAll()
{
  if(!objF) return;
  var rng= objF.document.selection.createRange();
  rng.moveToPoint(0,0); 
  rng.select()

  var result=1;
  while(result) result= goFind(1,1); 
 
}