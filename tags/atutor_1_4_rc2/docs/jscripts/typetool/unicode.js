/*
*
*/
function swapUnicode()
{
 var el;
 if(document.all) el= document.frames[fID]; 
 else el= document.getElementById(fID).contentWindow; 

 if(!el){alert('Please click to select the editor');return}
 el.focus()

 if(format[fID]!="HTML")
  { swapMode(); format[fID]=="HTML";}

 var strx;
 strx= objInnerHTML(el); //el.document.body.innerHTML

 UNICODE= 1-UNICODE
 if(UNICODE) strx=UnicodeToCompound(strx)
 else strx= CompoundToUnicode(strx)

 var eStyle= el.document.body.style
 eStyle.fontFamily= FACE[fID]
 eStyle.fontSize= SIZE[fID]
 eStyle.color= COLOR[fID]
 eStyle.backgroundColor= BCOLOR[fID]
 eStyle.backgroundImage= "url(" + BIMAGE[fID] + ")"
 el.document.body.innerHTML= strx
}




function UnicodeToCompound(str1)
{
  str1= str1.replace(/\u00E1/g,'a\u0301');
  str1= str1.replace(/\u00C1/g,'A\u0301');
  str1= str1.replace(/\u00E0/g,'a\u0300');
  str1= str1.replace(/\u00C0/g,'A\u0300');
  str1= str1.replace(/\u1EA3/g,'a\u0309');
  str1= str1.replace(/\u1EA2/g,'A\u0309');
  str1= str1.replace(/\u00E3/g,'a\u0303');
  str1= str1.replace(/\u00C3/g,'A\u0303');
  str1= str1.replace(/\u1EA1/g,'a\u0323');
  str1= str1.replace(/\u1EA0/g,'A\u0323');
  //á Á

  str1= str1.replace(/\u0103/g,'a\u0306');
  str1= str1.replace(/\u0102/g,'A\u0306');
  str1= str1.replace(/\u1EAF/g,'a\u0306\u0301');
  str1= str1.replace(/\u1EAE/g,'A\u0306\u0301');
  str1= str1.replace(/\u1EB1/g,'a\u0306\u0300');
  str1= str1.replace(/\u1EB0/g,'A\u0306\u0300');
  str1= str1.replace(/\u1EB3/g,'a\u0306\u0309');
  str1= str1.replace(/\u1EB2/g,'A\u0306\u0309');
  str1= str1.replace(/\u1EB5/g,'a\u0306\u0303');
  str1= str1.replace(/\u1EB4/g,'A\u0306\u0303');
  str1= str1.replace(/\u1EB7/g,'a\u0306\u0323');
  str1= str1.replace(/\u1EB6/g,'A\u0306\u0323');
  //a+ A+ 



  str1= str1.replace(/\u00E2/g,'a\u0302');
  str1= str1.replace(/\u00C2/g,'A\u0302');
  str1= str1.replace(/\u1EA5/g,'a\u0302\u0301');
  str1= str1.replace(/\u1EA4/g,'A\u0302\u0301');
  str1= str1.replace(/\u1EA7/g,'a\u0302\u0300');
  str1= str1.replace(/\u1EA6/g,'A\u0302\u0300');
  str1= str1.replace(/\u1EA9/g,'a\u0302\u0309');
  str1= str1.replace(/\u1EA8/g,'A\u0302\u0309');
  str1= str1.replace(/\u1EAB/g,'a\u0302\u0303');
  str1= str1.replace(/\u1EAA/g,'A\u0302\u0303');
  str1= str1.replace(/\u1EAD/g,'a\u0302\u0323');
  str1= str1.replace(/\u1EAC/g,'A\u0302\u0323');
  // â Â 




  str1= str1.replace(/\u00E9/g,'e\u0301');
  str1= str1.replace(/\u00C9/g,'E\u0301');
  str1= str1.replace(/\u00E8/g,'e\u0300');
  str1= str1.replace(/\u00C8/g,'E\u0300');
  str1= str1.replace(/\u1EBB/g,'e\u0309');
  str1= str1.replace(/\u1EBA/g,'E\u0309');
  str1= str1.replace(/\u1EBD/g,'e\u0303');
  str1= str1.replace(/\u1EBC/g,'E\u0303');
  str1= str1.replace(/\u1EB9/g,'e\u0323');
  str1= str1.replace(/\u1EB8/g,'E\u0323');
  // é É 


  str1= str1.replace(/\u00EA/g,'e\u0302');
  str1= str1.replace(/\u00CA/g,'E\u0302');
  str1= str1.replace(/\u1EBF/g,'e\u0302\u0301');
  str1= str1.replace(/\u1EBE/g,'E\u0302\u0301');
  str1= str1.replace(/\u1EC1/g,'e\u0302\u0300');
  str1= str1.replace(/\u1EC0/g,'E\u0302\u0300');
  str1= str1.replace(/\u1EC3/g,'e\u0302\u0309');
  str1= str1.replace(/\u1EC2/g,'E\u0302\u0309');
  str1= str1.replace(/\u1EC5/g,'e\u0302\u0303');
  str1= str1.replace(/\u1EC4/g,'E\u0302\u0303');
  str1= str1.replace(/\u1EC7/g,'e\u0302\u0323');
  str1= str1.replace(/\u1EC6/g,'E\u0302\u0323');
  // ê Ê
 


  str1= str1.replace(/\u00ED/g,'i\u0301');
  str1= str1.replace(/\u00CD/g,'I\u0301');
  str1= str1.replace(/\u00EC/g,'i\u0300');
  str1= str1.replace(/\u00CC/g,'I\u0300');
  str1= str1.replace(/\u1EC9/g,'i\u0309');
  str1= str1.replace(/\u1EC8/g,'I\u0309');
  str1= str1.replace(/\u0129/g,'i\u0303');
  str1= str1.replace(/\u0128/g,'I\u0303');
  str1= str1.replace(/\u1ECB/g,'i\u0323');
  str1= str1.replace(/\u1ECA/g,'I\u0323');
  // í Í



  str1= str1.replace(/\u00F3/g,'o\u0301');
  str1= str1.replace(/\u00D3/g,'O\u0301');
  str1= str1.replace(/\u00F2/g,'o\u0300');
  str1= str1.replace(/\u00D2/g,'O\u0300');
  str1= str1.replace(/\u1ECF/g,'o\u0309');
  str1= str1.replace(/\u1ECE/g,'O\u0309');
  str1= str1.replace(/\u00F5/g,'o\u0303');
  str1= str1.replace(/\u00D5/g,'O\u0303');
  str1= str1.replace(/\u1ECD/g,'o\u0323');
  str1= str1.replace(/\u1ECC/g,'O\u0323');
  // ó Ó




  str1= str1.replace(/\u01A1/g,'o\u031B');
  str1= str1.replace(/\u01A0/g,'O\u031B');
  str1= str1.replace(/\u1EDB/g,'o\u031B\u0301');
  str1= str1.replace(/\u1EDA/g,'O\u031B\u0301');
  str1= str1.replace(/\u1EDD/g,'o\u031B\u0300');
  str1= str1.replace(/\u1EDC/g,'O\u031B\u0300');
  str1= str1.replace(/\u1EDF/g,'o\u031B\u0309');
  str1= str1.replace(/\u1EDE/g,'O\u031B\u0309');
  str1= str1.replace(/\u1EE1/g,'o\u031B\u0303');
  str1= str1.replace(/\u1EE0/g,'O\u031B\u0303');
  str1= str1.replace(/\u1EE3/g,'o\u031B\u0323');
  str1= str1.replace(/\u1EE2/g,'O\u031B\u0323');
  // o+ O+



  str1= str1.replace(/\u00F4/g,'o\u0302');
  str1= str1.replace(/\u00D4/g,'O\u0302');
  str1= str1.replace(/\u1ED1/g,'o\u0302\u0301');
  str1= str1.replace(/\u1ED0/g,'O\u0302\u0301');
  str1= str1.replace(/\u1ED3/g,'o\u0302\u0300');
  str1= str1.replace(/\u1ED2/g,'O\u0302\u0300');
  str1= str1.replace(/\u1ED5/g,'o\u0302\u0309');
  str1= str1.replace(/\u1ED4/g,'O\u0302\u0309');
  str1= str1.replace(/\u1ED7/g,'o\u0302\u0303');
  str1= str1.replace(/\u1ED6/g,'O\u0302\u0303');
  str1= str1.replace(/\u1ED9/g,'o\u0302\u0323');
  str1= str1.replace(/\u1ED8/g,'O\u0302\u0323');
  // ô Ô
 



  str1= str1.replace(/\u00FA/g,'u\u0301');
  str1= str1.replace(/\u00DA/g,'U\u0301');
  str1= str1.replace(/\u00F9/g,'u\u0300');
  str1= str1.replace(/\u00D9/g,'U\u0300');
  str1= str1.replace(/\u1EE7/g,'u\u0309');
  str1= str1.replace(/\u1EE6/g,'U\u0309');
  str1= str1.replace(/\u0169/g,'u\u0303');
  str1= str1.replace(/\u0168/g,'U\u0303');
  str1= str1.replace(/\u1EE5/g,'u\u0323');
  str1= str1.replace(/\u1EE4/g,'U\u0323');
  // ú Ú
 



  str1= str1.replace(/\u01B0/g,'u\u031B');
  str1= str1.replace(/\u01AF/g,'U\u031B');
  str1= str1.replace(/\u1EE9/g,'u\u031B\u0301');
  str1= str1.replace(/\u1EE8/g,'U\u031B\u0301');
  str1= str1.replace(/\u1EEB/g,'u\u031B\u0300');
  str1= str1.replace(/\u1EEA/g,'U\u031B\u0300');
  str1= str1.replace(/\u1EED/g,'u\u031B\u0309');
  str1= str1.replace(/\u1EEC/g,'U\u031B\u0309');
  str1= str1.replace(/\u1EEF/g,'u\u031B\u0303');
  str1= str1.replace(/\u1EEE/g,'U\u031B\u0303');
  str1= str1.replace(/\u1EF1/g,'u\u031B\u0323');
  str1= str1.replace(/\u1EF0/g,'U\u031B\u0323');
  // u+ U+


  str1= str1.replace(/\u00FD/g,'y\u0301');
  str1= str1.replace(/\u00DD/g,'Y\u0301');
  str1= str1.replace(/\u1EF3/g,'y\u0300');
  str1= str1.replace(/\u1EF2/g,'Y\u0300');
  str1= str1.replace(/\u1EF7/g,'y\u0309');
  str1= str1.replace(/\u1EF6/g,'Y\u0309');
  str1= str1.replace(/\u1EF9/g,'y\u0303');
  str1= str1.replace(/\u1EF8/g,'Y\u0303');
  str1= str1.replace(/\u1EF5/g,'y\u0323');
  str1= str1.replace(/\u1EF4/g,'Y\u0323');
  // ý Ý


  return str1;

}





function CompoundToUnicode(str1)
{
  str1= str1.replace(/a\u0301/g,'\u00E1');
  str1= str1.replace(/A\u0301/g,'\u00C1');
  str1= str1.replace(/a\u0300/g,'\u00E0');
  str1= str1.replace(/A\u0300/g,'\u00C0');
  str1= str1.replace(/a\u0309/g,'\u1EA3');
  str1= str1.replace(/A\u0309/g,'\u1EA2');
  str1= str1.replace(/a\u0303/g,'\u00E3');
  str1= str1.replace(/A\u0303/g,'\u00C3');
  str1= str1.replace(/a\u0323/g,'\u1EA1');
  str1= str1.replace(/A\u0323/g,'\u1EA0');
  //á Á 


  str1= str1.replace(/a\u0306\u0301/g,'\u1EAF');
  str1= str1.replace(/A\u0306\u0301/g,'\u1EAE');
  str1= str1.replace(/a\u0306\u0300/g,'\u1EB1');
  str1= str1.replace(/A\u0306\u0300/g,'\u1EB0');
  str1= str1.replace(/a\u0306\u0309/g,'\u1EB3');
  str1= str1.replace(/A\u0306\u0309/g,'\u1EB2');
  str1= str1.replace(/a\u0306\u0303/g,'\u1EB5');
  str1= str1.replace(/A\u0306\u0303/g,'\u1EB4');
  str1= str1.replace(/a\u0306\u0323/g,'\u1EB7');
  str1= str1.replace(/A\u0306\u0323/g,'\u1EB6');
  str1= str1.replace(/a\u0306/g,'\u0103');
  str1= str1.replace(/A\u0306/g,'\u0102');
  //a+ A+


  str1= str1.replace(/a\u0302\u0301/g,'\u1EA5');
  str1= str1.replace(/A\u0302\u0301/g,'\u1EA4');
  str1= str1.replace(/a\u0302\u0300/g,'\u1EA7');
  str1= str1.replace(/A\u0302\u0300/g,'\u1EA6');
  str1= str1.replace(/a\u0302\u0309/g,'\u1EA9');
  str1= str1.replace(/A\u0302\u0309/g,'\u1EA8');
  str1= str1.replace(/a\u0302\u0303/g,'\u1EAB');
  str1= str1.replace(/A\u0302\u0303/g,'\u1EAA');
  str1= str1.replace(/a\u0302\u0323/g,'\u1EAD');
  str1= str1.replace(/A\u0302\u0323/g,'\u1EAC');
  str1= str1.replace(/a\u0302/g,'\u00E2');
  str1= str1.replace(/A\u0302/g,'\u00C2');
  // â Â 




  str1= str1.replace(/e\u0301/g,'\u00E9');
  str1= str1.replace(/E\u0301/g,'\u00C9');
  str1= str1.replace(/e\u0300/g,'\u00E8');
  str1= str1.replace(/E\u0300/g,'\u00C8');
  str1= str1.replace(/e\u0309/g,'\u1EBB');
  str1= str1.replace(/E\u0309/g,'\u1EBA');
  str1= str1.replace(/e\u0303/g,'\u1EBD');
  str1= str1.replace(/E\u0303/g,'\u1EBC');
  str1= str1.replace(/e\u0323/g,'\u1EB9');
  str1= str1.replace(/E\u0323/g,'\u1EB8');
  // é É



  str1= str1.replace(/e\u0302\u0301/g,'\u1EBF');
  str1= str1.replace(/E\u0302\u0301/g,'\u1EBE');
  str1= str1.replace(/e\u0302\u0300/g,'\u1EC1');
  str1= str1.replace(/E\u0302\u0300/g,'\u1EC0');
  str1= str1.replace(/e\u0302\u0309/g,'\u1EC3');
  str1= str1.replace(/E\u0302\u0309/g,'\u1EC2');
  str1= str1.replace(/e\u0302\u0303/g,'\u1EC5');
  str1= str1.replace(/E\u0302\u0303/g,'\u1EC4');
  str1= str1.replace(/e\u0302\u0323/g,'\u1EC7');
  str1= str1.replace(/E\u0302\u0323/g,'\u1EC6');
  str1= str1.replace(/e\u0302/g,'\u00EA');
  str1= str1.replace(/E\u0302/g,'\u00CA');
  // ê Ê


  str1= str1.replace(/i\u0301/g,'\u00ED');
  str1= str1.replace(/I\u0301/g,'\u00CD');
  str1= str1.replace(/i\u0300/g,'\u00EC');
  str1= str1.replace(/I\u0300/g,'\u00CC');
  str1= str1.replace(/i\u0309/g,'\u1EC9');
  str1= str1.replace(/I\u0309/g,'\u1EC8');
  str1= str1.replace(/i\u0303/g,'\u0129');
  str1= str1.replace(/I\u0303/g,'\u0128');
  str1= str1.replace(/i\u0323/g,'\u1ECB');
  str1= str1.replace(/I\u0323/g,'\u1ECA');
  // í Í




  str1= str1.replace(/o\u0301/g,'\u00F3');
  str1= str1.replace(/O\u0301/g,'\u00D3');
  str1= str1.replace(/o\u0300/g,'\u00F2');
  str1= str1.replace(/O\u0300/g,'\u00D2');
  str1= str1.replace(/o\u0309/g,'\u1ECF');
  str1= str1.replace(/O\u0309/g,'\u1ECE');
  str1= str1.replace(/o\u0303/g,'\u00F5');
  str1= str1.replace(/O\u0303/g,'\u00D5');
  str1= str1.replace(/o\u0323/g,'\u1ECD');
  str1= str1.replace(/O\u0323/g,'\u1ECC');
  // ó Ó




  str1= str1.replace(/o\u031B\u0301/g,'\u1EDB');
  str1= str1.replace(/O\u031B\u0301/g,'\u1EDA');
  str1= str1.replace(/o\u031B\u0300/g,'\u1EDD');
  str1= str1.replace(/O\u031B\u0300/g,'\u1EDC');
  str1= str1.replace(/o\u031B\u0309/g,'\u1EDF');
  str1= str1.replace(/O\u031B\u0309/g,'\u1EDE');
  str1= str1.replace(/o\u031B\u0303/g,'\u1EE1');
  str1= str1.replace(/O\u031B\u0303/g,'\u1EE0');
  str1= str1.replace(/o\u031B\u0323/g,'\u1EE3');
  str1= str1.replace(/O\u031B\u0323/g,'\u1EE2');
  str1= str1.replace(/o\u031B/g,'\u01A1');
  str1= str1.replace(/O\u031B/g,'\u01A0');
  // o+ O+



  str1= str1.replace(/o\u0302\u0301/g,'\u1ED1');
  str1= str1.replace(/O\u0302\u0301/g,'\u1ED0');
  str1= str1.replace(/o\u0302\u0300/g,'\u1ED3');
  str1= str1.replace(/O\u0302\u0300/g,'\u1ED2');
  str1= str1.replace(/o\u0302\u0309/g,'\u1ED5');
  str1= str1.replace(/O\u0302\u0309/g,'\u1ED4');
  str1= str1.replace(/o\u0302\u0303/g,'\u1ED7');
  str1= str1.replace(/O\u0302\u0303/g,'\u1ED6');
  str1= str1.replace(/o\u0302\u0323/g,'\u1ED9');
  str1= str1.replace(/O\u0302\u0323/g,'\u1ED8');
  str1= str1.replace(/o\u0302/g,'\u00F4');
  str1= str1.replace(/O\u0302/g,'\u00D4');
  // ô Ô




  str1= str1.replace(/u\u0301/g,'\u00FA');
  str1= str1.replace(/U\u0301/g,'\u00DA');
  str1= str1.replace(/u\u0300/g,'\u00F9');
  str1= str1.replace(/U\u0300/g,'\u00D9');
  str1= str1.replace(/u\u0309/g,'\u1EE7');
  str1= str1.replace(/U\u0309/g,'\u1EE6');
  str1= str1.replace(/u\u0303/g,'\u0169');
  str1= str1.replace(/U\u0303/g,'\u0168');
  str1= str1.replace(/u\u0323/g,'\u1EE5');
  str1= str1.replace(/U\u0323/g,'\u1EE4');
  // ú Ú




  str1= str1.replace(/u\u031B\u0301/g,'\u1EE9');
  str1= str1.replace(/U\u031B\u0301/g,'\u1EE8');
  str1= str1.replace(/u\u031B\u0300/g,'\u1EEB');
  str1= str1.replace(/U\u031B\u0300/g,'\u1EEA');
  str1= str1.replace(/u\u031B\u0309/g,'\u1EED');
  str1= str1.replace(/U\u031B\u0309/g,'\u1EEC');
  str1= str1.replace(/u\u031B\u0303/g,'\u1EEF');
  str1= str1.replace(/U\u031B\u0303/g,'\u1EEE');
  str1= str1.replace(/u\u031B\u0323/g,'\u1EF1');
  str1= str1.replace(/U\u031B\u0323/g,'\u1EF0');
  str1= str1.replace(/u\u031B/g,'\u01B0');
  str1= str1.replace(/U\u031B/g,'\u01AF');
  // u+ U+


  str1= str1.replace(/y\u0301/g,'\u00FD');
  str1= str1.replace(/Y\u0301/g,'\u00DD');
  str1= str1.replace(/y\u0300/g,'\u1EF3');
  str1= str1.replace(/Y\u0300/g,'\u1EF2');
  str1= str1.replace(/y\u0309/g,'\u1EF7');
  str1= str1.replace(/Y\u0309/g,'\u1EF6');
  str1= str1.replace(/y\u0303/g,'\u1EF9');
  str1= str1.replace(/Y\u0303/g,'\u1EF8');
  str1= str1.replace(/y\u0323/g,'\u1EF5');
  str1= str1.replace(/Y\u0323/g,'\u1EF4');
  // ý Ý 


  return str1;

}





/***********
//Unicode in Hexal-Form \uXXXX

function dec2hex(dec)
{
  var hex= new Array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
  if(dec<16) return hex[dec]

  var xstr='', x;
  while(dec>15)
   {
	 x= dec%16;
	 xstr= hex[x]+ xstr
	 dec= Math.floor(dec/16)
   }
  if(dec) xstr= hex[dec]+ xstr

  while(xstr.length<4) xstr= "0"+xstr;
  return "\\u" + xstr;
}


function hex2dec(hex)
{
  if(hex=='') return 0

  var hexA= new  Array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
  var hexH= new Array();
  for(var i=0;i<hexA.length;i++) hexH[hexA[i]]=i;

  hex= hex.toUpperCase();
  var chrA= hex.split('')
  var len=chrA.length
  var res=0;
  var fact= 1
  for(var i=len-1; i>=0;i--)
	{
	  res += fact*hexH[chrA[i]]
	  fact *= 16
	}

  return res;
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
	  str2 +=str1.substring(0,i) + dec2hex(code)
      str1=str1.substring(i+1,str1.length)
      len=str1.length
      i=0
     }
    str1=str2+str1
    j++;
   }
  return str1;
}

function  viewISOCode(str1)
{
 var c0, str2='', strx='', idx;
 
 idx=str1.indexOf('\\u')

 if(idx<0) return str1
 var i=0
 while (i<str1.length)
  {
    if(str1.length<5) break
    c0=str1.substring(i,i+2)
    i++
    if(c0 !='\\u') continue
    strx += str1.substring(0,i-1)
    str2 = str1.substring(i-1+2,i-1+6)
	str1=str1.substring(i-1+6,str1.length)

    var code= hex2dec(str2)
    strx += String.fromCharCode(code);
	i=0;
  }
 return strx+str1;
}
*************/