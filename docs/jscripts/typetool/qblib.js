var CURRENT=null

/******************* LIB FUNCTIONS *********************/
function checkFiletype(oform,ename)
{
  var ename1 = 'TX_' + ename.substr(2)

  var bgx= oform[ename].value
  var reg= /gif|jpg|png/
  if( !reg.test(bgx) ) alert(FILENOTIMG) 
  else oform[ename1].value= bgx
}



function getOptionIndex(oSel,text)
{ 
  text= text.toLowerCase()

  var opt
  for(var i=0; i<oSel.options.length; i++)
   {
    opt= oSel.options[i].text
	opt= opt.toLowerCase()
    if(opt==text) return i
   }
}


function setTextOption(oSel,text)
{
  text= text.toLowerCase()

  var opt;
  for(var i=0; i<oSel.options.length; i++)
   {
    opt= oSel.options[i].text
	opt= opt.toLowerCase()
    if(opt==text){ oSel.selectedIndex=i; return }
   }

  oSel.selectedIndex=0
}



function rgb2hex(rgbcol)
{
  //rgb(255, 255, 255): mozilla
  if( rgbcol.substring(0,4) !=  'rgb(' ) return rgbcol

  var strX1;
  var x1, x2;
  var hexcol=''
  rgbcol= rgbcol.substring(4,rgbcol.length-1)
  var colA= rgbcol.split(",");

  for(var i=0; i<3; i++)
   {
	 strX1 = colA[i]; strX1++; strX1--;
	 x1= strX1%16; x1= toHex(x1)
	 x2= parseInt(strX1/16) ; x2= toHex(x2)
	 hexcol += x2+x1
   }

  return '#'+hexcol
}



function toInt(cha)
{
 cha= cha.toUpperCase();
 var X = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F")
 for(var i=0; i<16; i++)
  {
   if( cha==X[i] ) return i
  }
 return -1 
}



function toHex(deci)
{
  var X = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F")
  if(deci>15) return -1
  else return X[deci]
}



function colToHex(col)
{
  var defcolor = new Array ( 'aliceblue','#F0F8FF','antiquewhite','#FAEBD7','aqua','#00FFFF','aquamarine','#7FFFD4',
                           'azure','#F0FFFF', 'beige','#F5F5DC','bisque','#FFE4C4','black','#000000','blanchedalmond','#FFEBCD', 
						   'blue','#0000FF','blueviolet','#8A2BE2','brown','#A52A2A','burlywood','#DEB887','cadetblue','#5F9EA0',
						   'chartreuse','#7FFF00','chocolate','#D2691E','coral','#FF7F50','cornflowerblue','#6495ED',
						   'cornsilk','#FFF8DC','crimson','#DC143C','cyan','#00FFFF','darkblue','#00008B','darkcyan','#008B8B',
						   'darkgoldenrod','#B8860B','darkgray','#A9A9A9','darkgreen','#006400','darkkhaki','#BDB76B',
						   'darkmagenta','#8B008B','darkolivegreen','#556B2F','darkorange','#FF8C00','darkorchid','#9932CC',
						   'darkred','#8B0000','darksalmon','#E9967A','darkseagreen','#8FBC8F','darkslateblue','#483D8B',
						   'darkslategray','#2F4F4F','darkturquoise','#00CED1','darkviolet','#9400D3','deeppink','#FF1493',
						   'deepskyblue','#00BFFF','dimgray','#696969','dodgerblue','#1E90FF','firebrick','#B22222',
						   'floralwhite','#FFFAF0','forestgreen','#228B22','fuchsia','#FF00FF','gainsboro','#DCDCDC',
						   'ghostwhite','#F8F8FF','gold','#FFD700','goldenrod','#DAA520','gray','#808080','green','#008000',
						   'greenyellow','#ADFF2F','honeydew','#F0FFF0','hotpink','#FF69B4','indianred','#CD5C5C',
						   'indigo','#4B0082','ivory','#FFFFF0','khaki','#F0E68C','lavender','#E6E6FA','lavenderblush','#FFF0F5',
						   'lawngreen','#7CFC00','lemonchiffon','#FFFACD','lightblue','#ADD8E6','lightcoral','#F08080',
						   'lightcyan','#E0FFFF','lightgoldenrodyellow','#FAFAD2','lightgreen','#90EE90','lightgrey','#D3D3D3',
						   'lightpink','#FFB6C1','lightsalmon','#FFA07A','lightseagreen','#20B2AA','lightskyblue','#87CEFA',
						   'lightslategray','#778899','lightsteelblue','#B0C4DE','lightyellow','#FFFFE0','lime','#00FF00',
						   'limegreen','#32CD32','linen','#FAF0E6','magenta','#FF00FF','maroon','#800000','mediumaquamarine','#66CDAA',
						   'mediumblue','#0000CD','mediumorchid','#BA55D3','mediumpurple','#9370DB','mediumseagreen','#3CB371',
						   'mediumslateblue','#7B68EE','mediumspringgreen','#00FA9A','mediumturquoise','#48D1CC',
						   'mediumvioletred','#C71585','midnightblue','#191970','mintcream','#F5FFFA','mistyrose','#FFE4E1',
						   'moccasin','#FFE4B5','navajowhite','#FFDEAD','navy','#000080','oldlace','#FDF5E6','olive','#808000',
						   'olivedrab','#6B8E23','orange','#FFA500','orangered','#FF4500','orchid','#DA70D6','palegoldenrod','#EEE8AA',
						   'palegreen','#98FB98','paleturquoise','#AFEEEE','palevioletred','#DB7093','papayawhip','#FFEFD5',
						   'peachpuff','#FFDAB9','peru','#CD853F','pink','#FFC0CB','plum','#DDA0DD','powderblue','#B0E0E6',
						   'purple','#800080','red','#FF0000','rosybrown','#BC8F8F','royalblue','#4169E1','saddlebrown','#8B4513',
						   'salmon','#FA8072','sandybrown','#F4A460','seagreen','#2E8B57','seashell','#FFF5EE','sienna','#A0522D',
						   'silver','#C0C0C0','skyblue','#87CEEB','slateblue','#6A5ACD','slategray','#708090','snow','#FFFAFA',
						   'springgreen','#00FF7F','steelblue','#4682B4','tan','#D2B48C','teal','#008080','thistle','#D8BFD8',
						   'tomato','#FF6347','turquoise','#40E0D0','violet','#EE82EE','wheat','#F5DEB3','white','#FFFFFF',
						   'whitesmoke','#F5F5F5','yellow','#FFFF00','yellowgreen','#9ACD32' )
  
  col = col.toLowerCase();
  for(var i=0 ; i<defcolor.length-1 ; i += 2)
   {
     if( col==defcolor[i] )  return defcolor[i+1]
   }
  return 'undef'
}



function changeColor(plus,RGB,funcx)
{
  if(!DELTA) return

  var bgcol= CURRENT.value;

  bgcol= rgb2hex(bgcol);

  if (bgcol=='transparent' || bgcol=='') { bgcol= '#000000';  }

  if( bgcol.substring(0,1) !=  '#' )
   {
    bgcol=colToHex(bgcol)
    if( bgcol=='undef' ) return 'undef'
   }

  var strX1, strX2, strX3;
  var str1, str2, x1, x2 , x3;

  strX1 = bgcol.substring(1,3)
  strX2 = bgcol.substring(3,5)
  strX3 = bgcol.substring(5,7)

  if( RGB=='red' || RGB=='all' )
   {
    str1 = strX1.substring(0,1); str2 = strX1.substring(1,2)

	x1 = toInt(str1); x2 = toInt(str2)
	x1 = x1*16 + x2
	if(plus) x1 += DELTA
	else x1 -= DELTA
	if(x1<0 || x1>255 ) return
    x3 = x1 >> 4;  x2 = x1 & 15

	strX1 = toHex(x3) + toHex(x2)
   }
  if( RGB=='green' || RGB=='all' )
   {
    str1 = strX2.substring(0,1); str2 = strX2.substring(1,2)

	x1 = toInt(str1); x2 = toInt(str2)
	x1 = x1*16 + x2
	if(plus) x1 += DELTA
	else x1 -= DELTA
	if(x1<0 || x1>255 ) return
    x3 = x1 >> 4;  x2 = x1 & 15

	strX2 = toHex(x3) + toHex(x2)
   }
  if( RGB=='blue' || RGB=='all' )
   {
    str1 = strX3.substring(0,1); str2 = strX3.substring(1,2)
	
	x1 = toInt(str1); x2 = toInt(str2)
	x1 = x1*16 + x2
	if(plus) x1 += DELTA
	else x1 -= DELTA
	if(x1<0 || x1>255 ) return
    x3 = x1 >> 4;  x2 = x1 & 15

	strX3 = toHex(x3) + toHex(x2)
   }


  str1 = '#' + strX1 + strX2 + strX3
  
  CURRENT.value = str1
  eval(funcx); // function call // retdivFilter() ; retdivBorder() ; retdivStyle()

  DELTA += DELTA ; 
  DELTA = (DELTA>16) ? 16 : DELTA

  setTimeout("changeColor("+plus+",'"+RGB+"','"+funcx+"')",100)
    
}




function addValue(type,funcx)
{ 
  if( DELTA==0 ) return   

  var val= CURRENT.value
  val= val.replace(/px$/,"")

  var percent=''
  if( /%$/.test(val)){ val= val.replace(/%$/,''); percent='%';}

  val++; val--
  
  val = DELTA + val
  
  switch(type)
  {
   case 'IN': case 'RE': break;
   case 'PI': case 'PR': if(val<0) val=0; break;
   case 'PE': if(val<0) val=0; else if(val>100) val=100; break;
   case 'DE': if(val<0) val=0; else if(val>360) val=360; break;
  }

  CURRENT.value= val + percent
  eval(funcx); // function call // retdivFilter() ;  retdivStyle() ; retdivBorder() ;

  DELTA += DELTA ; 
  DELTA = (DELTA>16) ? 16 : DELTA

  setTimeout("addValue('"+type+"','"+funcx+"')",100)
}





function setValueBigger(funcx)
{
 if(!CURRENT){ alert(ELESELECT); return }

 var na= CURRENT.name;
 var twice= na.split('_')
 if(!twice[1]) return; // without PRE

 switch(twice[0])
 {
   case 'IN': DELTA=1; addValue('IN',funcx) ; break; // integer 
   case 'PI': DELTA=1; addValue('PI',funcx) ; break; // positive
   case 'RE': DELTA=0.1; addValue('RE',funcx) ; break;
   case 'PR': DELTA=0.1; addValue('PR',funcx) ; break; // pos. Real
   case 'PE': DELTA=1; addValue('PE',funcx) ; break; // procent 0-100
   case 'DE': DELTA=1; addValue('DE',funcx) ; break; // Degree 0-360
   case 'OP': var idx=CURRENT.selectedIndex; 
              if(idx==CURRENT.options.length-1) idx=CURRENT.options.length-2
              CURRENT.selectedIndex = idx+1 ; 
			  if(CURRENT.name=='OP_filter') changeOptionTable(CURRENT)
			  else eval(funcx); // function call retdivFilter() ; // retdivBorder() ; retdivStyle()
			  break;
   case 'CO': DELTA=1;changeColor(1,'all',funcx); break;
 }

// CURRENT.focus();

}



function setValueSmaller(funcx)
{
 if(!CURRENT){ alert(ELESELECT); return }

 var na= CURRENT.name;
 var twice= na.split('_')
 if(!twice[1]) return; // without PREXIS

 switch(twice[0])
 {
   case 'IN': DELTA=-1; addValue('IN',funcx) ; break;
   case 'RE': DELTA=-0.1; addValue('RE',funcx) ; break;
   case 'PI': DELTA=-1; addValue('PI',funcx) ; break; // positive
   case 'PR': DELTA=-0.1; addValue('PR',funcx) ; break;
   case 'PE': DELTA=-1; addValue('PE',funcx) ; break; // procent 0-100
   case 'DE': DELTA=-1; addValue('DE',funcx) ; break; // Degree 0-360
   case 'OP': var idx=CURRENT.selectedIndex; 
              if(idx==0) idx=1
              CURRENT.selectedIndex = idx-1 ; 
			  if(CURRENT.name=='OP_filter') changeOptionTable(CURRENT)
			  else eval(funcx); // function call retdivFilter() ; //retdivBorder(); retdivStyle()
              break;
   case 'CO': DELTA=1;changeColor(0,'all',funcx); break;
 }

// CURRENT.focus();

}





function setObjectColor(plus,RGB,funcx)
{
 if(!CURRENT){ alert(CONSELECT); return }

 var na= CURRENT.name;
 var twice= na.split('_')
 if(!twice[1] || twice[0]!='CO' ) return; 

 DELTA=1; changeColor(plus,RGB,funcx);

 CURRENT.focus();

}




function checkElement(e)
{
  var el
  if(document.all) el= event.srcElement
  else el= e.currentTarget

  if(el.type=='button') return ;

  if(el.type!='text' && el.type!='select-one') CURRENT=null;
  else CURRENT=el
}




function addEventToForm()
{
  var fEl= document.forms[0].elements
  for(var i=0; i<fEl.length;i++)
   {
	if(document.all)
     {
	  fEl[i].attachEvent("onmousedown", checkElement)
	  fEl[i].attachEvent("onmouseup", function(){ DELTA=0 })
	 }
	else
	 { 
	  fEl[i].addEventListener("mousedown", checkElement, true) 
	  fEl[i].addEventListener("mouseup", function(){ DELTA=0 }, true) 
	 }
   }
}


