/*** Freeware Open Source writen by ngoCanh 2002-05                  */
/*** Original by Vietdev  http://vietdev.sourceforge.net             */
/*** Release 2004-03-15  R9.0                                        */
/*** GPL - Copyright protected                                       */
/*********************************************************************/

/*** CONFIGURATION - HERE YOU CAN SET DEFAULT-VALUES ********************/
if(typeof(SECURE)=="undefined") SECURE=1; //=0,1
if(typeof(VISUAL)=="undefined") VISUAL=0; //=0,1,2,3 see bottom of this file
if(typeof(POPWIN)=="undefined") POPWIN=1; //=1,0 Rightclick Popup dialog for textarea
if(typeof(DFFACE)=="undefined") DFFACE=''; // 'times new roman'; // Default fontFamily of Editor
if(typeof(DFSIZE)=="undefined") DFSIZE=''; // '14px'; // Default fontSize
if(typeof(DCOLOR)=="undefined") DCOLOR=''; // 'blue'; // Default color
if(typeof(DBGCOL)=="undefined") DBGCOL=''; // 'green'; // Default backgroundColor
if(typeof(DBGIMG)=="undefined") DBGIMG=''; // Default URL-backgroundImage 
if(typeof(DCSS)=="undefined") DCSS='stylesheet.css'; // 'test.css'; // Default-Stylesheet-URL
if(typeof(SYMBOLE)=="undefined") SYMBOLE='<QBFBR>' ; // Symbole for end-of-field in clipboard-chipcard.
if(typeof(USETABLE)=="undefined") USETABLE=1; // Enable table editor
if(typeof(USEFORM)=="undefined") USEFORM=0; // Enable form input
if(typeof(RETURNNL)=="undefined") RETURNNL=1; // Return-Button= Newline; Shift+Return= New Paragraph
if(typeof(FULLCTRL)=="undefined") FULLCTRL=0; //=0,1; 0=fast loading; 1=all control rows at bottom of Edi.
if(typeof(VDEVCSS)=="undefined") VDEVCSS= 'vdev.css'; // Edi. layout file - not for content of editor
if(typeof(LANGUAGE)=="undefined") LANGUAGE= 'language.js'; //your language file
if(typeof(UNICODE)=='undefined') UNICODE=0; // 0,1 
/*********************** END CONFIGURATION ****************************/


var fID; //***   IFRAME ID
var TXTOBJ=null; //***   TEXT Obj
var format=new Array();
var viewm=new Array();
var FACE= new Array();
var SIZE= new Array();
var COLOR= new Array();
var BCOLOR= new Array();
var BIMAGE= new Array();
var CSS= new Array();
var FWORD, FLAGS=0;



function getFullScriptPath(script)
{
  var i=0,p='';
  var scrA=document.getElementsByTagName('script')
  while(scrA[i])
  { 
   var src= scrA[i].src
   if( src && src.lastIndexOf(script)>=0 ){ p=src.substring(0,src.lastIndexOf(script)); break;}
   i++
  }

  if(p.indexOf("://")>=0) return p
  p= p.replace(/^\.\//,"/")

  var href= document.location.href
  href= href.substring(0,href.lastIndexOf('/'))

  if(p=='.' || p=='') return href
  else if(p.indexOf('..')>=0)
   {
    var sub= ''
    if(p.length>2) sub= p.substr(p.lastIndexOf('../')+2)
    var temp= p.split('..')
    for( var i=1; i<temp.length;i++)
     { href= href.substring(0,href.lastIndexOf('/')); }
    if(sub!='/..') href += sub
   }
  else if(p!='') href += p;
  return href
}


/*QBPATH2= getFullScriptPath('./quickbuild.js');*/
/* The path above is relative to the editor directory, and as a result breaks the call
to IE & Moz scripts, and language scripts, Need a way to dynamically generate an absolute path */

QBPATH= getFullScriptPath('/quickbuild.js');
//QBPATH = "/cvs/atutor/docs/jscripts/typetool";
//document.writeln(document.location.href);

document.writeln('<style>@import url("' + QBPATH + '/skin/'+ VDEVCSS +'");</style>');
document.writeln('<script src="'+QBPATH+'/skin/'+ LANGUAGE +'"></script>');

if(document.all) document.writeln('<script src="'+QBPATH+'/quickbuild_IE.js"></script>');
else document.writeln('<script src="'+QBPATH+'/quickbuild_Moz.js"></script>');
