/**********************************************
* CopyRight by VietDev 2002 (GPL)
* http://vietdev.sourceforge.net
* please fairplay to let the notice in tact
* 07.07.2002 for IE
* 12.04.2003 also for Mozilla 1.3 and newer
**********************************************/
var IE= document.all


function isTDSeleted()
{
 if(!curTD)
  {
   alert(NOCELLSEL);
   if(fID && IE) addEventToTable(document.frames[fID])
   else if(fID) addEventToTable(document.getElementById(fID).contentWindow)
   return 0
  } 
 return 1;  
}  



///////////////////////////////////////////////
// global variable	
var curTD	
var tmpCOLOR= 'cyan'
var oldCOLOR= ''
var tmpCOLOR1= '#00C0C0'
var oldCOLOR1= ''
var curTB


function clickTD(e)
{
  var el= document.getElementById(fID).contentWindow;
  if(IE) el= document.frames[fID]

  var oTD;
  if(IE) oTD= el.event.srcElement;
  else oTD= e.currentTarget;

  setCurrent(oTD)
}	


function setCurrent(oTD)
{

  // cell
  if(curTD) setBackground(curTD,oldCOLOR);
  oldCOLOR= oTD.getAttribute("bgcolor")
  setBackground(oTD,tmpCOLOR)
  curTD= oTD

  // table
  var oTB= oTD.parentNode.parentNode.parentNode
  if(curTB) setBackground(curTB,oldCOLOR1)
  curTB= oTB

  if(oTB.getAttribute("bgcolor")) return;
  oldCOLOR1= oTB.getAttribute("bgcolor")
  setBackground(oTB,tmpCOLOR1)
}


function setBackground(obj,color)
{
  if(IE) obj.runtimeStyle.backgroundColor= color
  else obj.setAttribute("bgcolor",color)
}





///////////////////////////////////////////////
///////////////////////////////////////////////
function insertTable()
{
  if(IE) return insertTableIE();

  var el= document.getElementById(fID).contentWindow;

  if(!el){alert(EDISELECT);return}
  el.focus();

  var urlx= QBPATH + '/createtable.html'

  var twidth= 350;
  var theight=250;
  var tposx= (screen.width- twidth)/2
  var tposy= screen.height- theight - 55
  	    	  
  var newWin1=window.open(urlx,"format","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()

}



function insertTableIE()
{
  var urlx= QBPATH + '/createtable.html'

  var el= document.frames[fID]
  if(!el){alert(EDISELECT);return}

  var arr=showModalDialog(urlx, '', "font-family:Verdana;font-size:12;dialogWidth:30em;dialogHeight:25em; edge:sunken;help:no;status:no");
  if(arr==null) return;
  
  insertHTML(el,arr)

  // add event listen
  var tdA= el.document.getElementsByTagName('td')
  for(var i=0; i<tdA.length;i++)
   tdA[i].attachEvent("onclick", clickTD)
}



function insertTableCell()
{
 if(! isTDSeleted()) return 
 var rowSelect= curTD.parentNode
 var newCell= rowSelect.insertCell(curTD.cellIndex+1,1);
 newCell.innerHTML= curTD.innerHTML ;

 if(IE) newCell.attachEvent("onclick", clickTD)
 else newCell.addEventListener("click", clickTD, true) 

}



function deleteTableCell()
{
 if(! isTDSeleted()) return 

 var col= curTD.cellIndex
 var rowSelect= curTD.parentNode
 rowSelect.deleteCell(col)
 curTD = rowSelect.cells[col]
 if(!curTD) curTD = rowSelect.cells[col-1]

 if(curTD) setBackground(curTD,tmpCOLOR)

}



function insertTableRow()
{
 if(!isTDSeleted()) return 
 
 var rowSelect= curTD.parentNode
 var tableSelect= rowSelect.parentNode
 var ridx= rowSelect.rowIndex;


 var row= tableSelect.rows[ridx]; // first row
 var idx=0; 
 for(var j=0; j<row.cells.length; j++) // j= cellIndex
  {
    if(!row.cells[j]) break;
    idx += row.cells[j].colSpan-1
  }
 
 var colx= j+idx

 var newRow= tableSelect.insertRow(ridx);
 var newCell;
 for(var i=0; i<colx; i++)
  { 
	newCell=newRow.insertCell(0,1); 
    newCell.innerHTML='&nbsp;' 
	
	if(IE) newCell.attachEvent("onclick", clickTD)
    else newCell.addEventListener("click", clickTD, true) 
  }


 for(var i=0; i<=ridx; i++)
  {
	row= tableSelect.rows[i]; 
	for(var j=0; j<row.cells.length; j++) // j= cellIndex
	 {
       if(row.cells[j].rowSpan>1 && i+row.cells[j].rowSpan>ridx)
		 row.cells[j].rowSpan += 1
	 }
  }

}


function deleteTableRow()
{
 if(! isTDSeleted()) return 
 var rowSelect= curTD.parentNode
 var tableSelect= rowSelect.parentNode
 var ridx= rowSelect.rowIndex 

 row= rowSelect; 
 var rlen=row.cells.length;
 for(var i=0; i<rlen; i++)
  {
    if(row.cells[i].rowSpan>1)
	 {
      var newCell= tableSelect.rows[ridx+1].insertCell(i);
      newCell.rowSpan= row.cells[i].rowSpan - 1 ;
	  newCell.innerHTML= row.cells[i].innerHTML ;
	  row.cells[i].rowSpan =1
	 }
  }


 while(row.cells.length) { row.deleteCell(0); }


 for(var i=0; i<=ridx; i++)
  {
	row= tableSelect.rows[i]; 
	for(var j=0; j<row.cells.length; j++) 
	 {
       if(row.cells[j].rowSpan>1 && i+row.cells[j].rowSpan>ridx)
		 row.cells[j].rowSpan -= 1
	 }
   }

  if(row.cells.length==0) tableSelect.deleteRow(ridx)

}




function insertTableCol()
{
 if(! isTDSeleted()) return 
 
 var rowSelect= curTD.parentNode
 var tableSelect= rowSelect.parentNode
 var lines= tableSelect.rows

 var colx= getColumnNo(curTD)

 var rspan= new Array();
 var newCell, cs ;
 for(var i=0; i<lines.length; i++)
  {
   row= tableSelect.rows[i]
   idx=0; 
   for(var j=0; j<=colx ; j++) // j= cellIndex
   	{
	 if(!rspan[j+idx])rspan[j+idx]=0;
   	 
	 while(rspan[j+idx]){rspan[j+idx]--; idx++ }

     if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
	 if(!row.cells[j] || (j+idx>=colx) )
   	 {
	  if(row.cells[j-1]) cs=row.cells[j-1].colSpan
	  else cs=1
	  if(cs==1)
		{
		  newCell=row.insertCell(j); 
	      newCell.innerHTML='&nbsp;'

		  if(IE) newCell.attachEvent("onclick", clickTD)
		  else newCell.addEventListener("click", clickTD, true) 
	      break; 
	    }
	  else
		{
		  /*  for cut later ************
		  var cont= row.cells[j-1].innerHTML
		  if(cs - (j+idx-colx)>0) row.cells[j-1].colSpan= cs - (j+idx-colx)
		  newCell=row.insertCell(j)
		  newCell.runtimeStyle.backgroundColor = "#b09090" ;
          newCell=row.insertCell(j+1)
		  if(j+idx-colx>0) newCell.colSpan= j+idx-colx
		  newCell.innerHTML= cont
		  */
		  row.cells[j-1].colSpan += 1
		  break ;
	    }
   	 }
  	 idx += row.cells[j].colSpan-1
   	}
  }

}




function deleteTableCol()
{
 if(! isTDSeleted()) return 
 
 var rowSelect= curTD.parentNode
 var tableSelect= rowSelect.parentNode
 var lines= tableSelect.rows

 var colx= getColumnNo(curTD)

 var rspan= new Array();
 var newCell, cs ;
 for(var i=0; i<lines.length; i++)
  {
   row= tableSelect.rows[i]
   idx=0; 
   for(var j=0; j<=colx ; j++) // j= cellIndex
   	{
	 if(!rspan[j+idx])rspan[j+idx]=0;
   	 while(rspan[j+idx]){rspan[j+idx]--; idx++ }
     if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
	 if(!row.cells[j] || (j+idx>=colx) )
   	 {
	  if(row.cells[j-1]) cs=row.cells[j-1].colSpan
	  else cs=1
	  if(cs==1) row.deleteCell(j)
	  else row.cells[j-1].colSpan -= 1
	  break ;
   	 }
  	 idx += row.cells[j].colSpan-1
   	}
  }

}




function morecolSpan()
{
  if(! isTDSeleted()) return 

  var cidx= curTD.cellIndex
  var row= curTD.parentNode
  var nxt= row.cells[cidx+1]
   
  if(!nxt) return;

  var maxcol= getMaxColumn()
  var colx= getColumnNo(curTD) ; // current
  var coln= getColumnNo(nxt) ; // next

  if(colx+curTD.colSpan>=maxcol || colx+curTD.colSpan<coln 
	 || curTD.rowSpan != nxt.rowSpan
    ) return

  curTD.innerHTML += nxt.innerHTML
  curTD.colSpan += nxt.colSpan
  row.deleteCell(cidx+1)

}




function lesscolSpan()
{
  if(! isTDSeleted()) return 
  if(curTD.colSpan==1) return
  var col= curTD.cellIndex
  curTD.colSpan -= 1
  var newCell= curTD.parentNode.insertCell(col+1,1)
  newCell.innerHTML= '&nbsp;' ;
  newCell.rowSpan= curTD.rowSpan ;

  if(IE) newCell.attachEvent("onclick", clickTD)
  else newCell.addEventListener("click", clickTD, true) 

}



function morerowSpan()
{
  if(!isTDSeleted()) return 

  var rowSpan= curTD.rowSpan
  var rowSelect=curTD.parentNode
  var tableSelect=rowSelect.parentNode
  var rowNum= tableSelect.rows.length
  var ridx= rowSelect.rowIndex+rowSpan; // next

  if( ridx>=rowNum) return 

  var colx= getColumnNo(curTD) ; // current

  var rowNext= tableSelect.rows[ridx]
  var cidx=getCellIndex(colx, rowNext); // Next
  var cellNext= rowNext.cells[cidx];
  if(!cellNext) return;

  var coln=  getColumnNo(cellNext) ; // cell Next row
  if(coln != colx || cellNext.colSpan != curTD.colSpan ) return;

  curTD.rowSpan += rowNext.cells[cidx].rowSpan
  curTD.innerHTML += rowNext.cells[cidx].innerHTML
  rowNext.deleteCell(cidx)
 
}


function lessrowSpan()
{
  if(! isTDSeleted()) return
  if(curTD.rowSpan==1) return

  var rowSpan= curTD.rowSpan
  var rowSelect=curTD.parentNode
  var tableSelect=rowSelect.parentNode
  var rowNum= tableSelect.rows.length
  var ridx= rowSelect.rowIndex+rowSpan-1; // next


  var colx= getColumnNo(curTD) ; // current
  var rowNext= tableSelect.rows[ridx]
  var cidx=getCellIndex(colx, rowNext); // Next

  curTD.rowSpan -= 1

  var newCell= rowNext.insertCell(cidx,1);
  newCell.innerHTML= '&nbsp;' ;
  rowNext.cells[cidx].colSpan = curTD.colSpan

  if(IE) newCell.attachEvent("onclick", clickTD)
  else newCell.addEventListener("click", clickTD, true) 

}





function getColumnNo(oTD)
{
 if(! isTDSeleted()) return 
 
 var cidx= oTD.cellIndex
 var rowSelect= oTD.parentNode
 var tableSelect= rowSelect.parentNode

 var idx, row, colx ;
 var rspan = new Array() ;
 for(var i=0; i<rowSelect.rowIndex+1; i++)
  {
   row= tableSelect.rows[i]
   idx=0; 
   for(var j=0; j<row.cells.length; j++) // j= cellIndex
   	{
     if(!rspan[j+idx])rspan[j+idx]=0
	 if(!row.cells[j]) break;

	 while(rspan[j+idx]>0) { rspan[j+idx]--; idx++ }
     rspan[j+idx]=row.cells[j].rowSpan-1

   	 if(i==rowSelect.rowIndex && j==cidx){ colx=j+idx; break }

  	 idx += row.cells[j].colSpan-1
   	}
  }

 return colx

}





function getCellIndex(colx, row)
{
 var tableSelect= row.parentNode
 var rowIdx= row.rowIndex

 var rspan= new Array();
 var newCell, cs , idx;
 for(var i=0; i<rowIdx+1; i++)
  {
   row= tableSelect.rows[i]
   idx=0; 
   for(var j=0; j<=colx ; j++) // j= cellIndex
   	{
	 if(!rspan[j+idx])rspan[j+idx]=0;
   	 
	 while(rspan[j+idx]){rspan[j+idx]--; idx++ }

     if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
	 if(!row.cells[j] || (j+idx>=colx) )
   	 {
       if(i==rowIdx) return j;
	   else break;
     }
  	 idx += row.cells[j].colSpan-1
   	}
  }

}




function getMaxColumn()
{
 var rowSelect= curTD.parentNode
 var tableSelect= rowSelect.parentNode
 var cell, colnum=0
 for(var i=0; i<tableSelect.rows[0].cells.length ; i++) // i= cellIndex
  {
   cell= tableSelect.rows[0].cells[i]
   colnum += cell.colSpan
  }
 return colnum
}





function cellProp()
{
  if(! isTDSeleted()) return 
 	 
  var twidth= screen.width/2, theight=250;
  var tposx= (screen.width- twidth)/2
  var tposy= screen.height- theight - 55

  var urlx= QBPATH + '/cellpro.html'

  var newWin1=window.open(urlx,"cell","toolbar=no,width="+twidth+",height=" + theight+ ", directories=no,status=no,scrollbars=yes,resizable=no,menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()
	
}





function tableProp()
{
  if(!isTDSeleted()) return 

  var twidth= 0.8*screen.width, theight=210;
  var tposx= (screen.width- twidth)/2
  var tposy= screen.height- theight - 55

  var urlx= QBPATH + '/tablepro.html'
	    	  
  newWin1=window.open(urlx,"table","toolbar=no,width="+ twidth+",height="+ theight+ ",directories=no,status=no,scrollbars=yes,resizable=no, menubar=no")
  newWin1.moveTo(tposx,tposy);
  newWin1.focus()
}



// addeventlistener for table-cell
function addEventToTable(obj)
{
  var tdA= obj.document.getElementsByTagName('td')
  for(var i=0; i<tdA.length;i++)
   { 
	if(IE) tdA[i].attachEvent("onclick", clickTD) 
    else tdA[i].addEventListener("click", clickTD, true) 
   }
}
