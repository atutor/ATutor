/*
 * jqGrid  3.3 - jQuery Grid
 * Copyright (c) 2008, Tony Tomov, tony@trirand.com
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2008-10-14 rev 64
 */

;(function($){$.fn.extend({editCell:function(iRow,iCol,ed,fg){return this.each(function(){var $t=this,nm,tmp,cc;if(!$t.grid||$t.p.cellEdit!==true){return;}
var currentFocus=null;if($.browser.msie&&$.browser.version<=6&&ed===true&&fg===true){iCol=getAbsoluteIndex($t.rows[iRow],iCol);}
$t.p.selrow=$t.rows[iRow].id;if(!$t.p.knv){$($t).GridNav();}
if($t.p.savedRow.length>0){if(ed===true){if(iRow==$t.p.iRow&&iCol==$t.p.iCol){return;}}
var vl=$("td:eq("+$t.p.savedRow[0].ic+")>#"+$t.p.savedRow[0].id+"_"+$t.p.savedRow[0].name,$t.rows[$t.p.savedRow[0].id]).val();if($t.p.savedRow[0].v!=vl){$($t).saveCell($t.p.savedRow[0].id,$t.p.savedRow[0].ic)}else{$($t).restoreCell($t.p.savedRow[0].id,$t.p.savedRow[0].ic);}}else{window.setTimeout(function(){$("#"+$t.p.knv).attr("tabindex","-1").focus();},0);}
nm=$t.p.colModel[iCol].name;if(nm=='subgrid'){return;}
if($t.p.colModel[iCol].editable===true&&ed===true){cc=$("td:eq("+iCol+")",$t.rows[iRow]);if(parseInt($t.p.iCol)>=0&&parseInt($t.p.iRow)>=0){$("td:eq("+$t.p.iCol+")",$t.rows[$t.p.iRow]).removeClass("edit-cell");}
$(cc).addClass("edit-cell");tmp=$(cc).html().replace(/\&nbsp\;/ig,'');var opt=$.extend($t.p.colModel[iCol].editoptions||{},{id:iRow+"_"+nm,name:nm});if(!$t.p.colModel[iCol].edittype){$t.p.colModel[iCol].edittype="text";}
var elc=createEl($t.p.colModel[iCol].edittype,opt,tmp,cc);if($.isFunction($t.p.beforeEditCell)){$t.p.beforeEditCell($t.rows[iRow].id,nm,tmp,iRow,iCol);}
$(cc).html("").append(elc);window.setTimeout(function(){$(elc).focus();},0);$t.p.savedRow.push({id:iRow,ic:iCol,name:nm,v:tmp});$("input, select, textarea",cc).bind("keydown",function(e){if(e.keyCode===27){$($t).restoreCell(iRow,iCol);}
if(e.keyCode===13){$($t).saveCell(iRow,iCol);}
if(e.keyCode==9){$($t).nextCell(iRow,iCol);}
e.stopPropagation();});if($.isFunction($t.p.afterEditCell)){$t.p.afterEditCell($t.rows[iRow].id,nm,tmp,iRow,iCol);}}else{if(parseInt($t.p.iCol)>=0&&parseInt($t.p.iRow)>=0){$("td:eq("+$t.p.iCol+")",$t.rows[$t.p.iRow]).removeClass("edit-cell");}
$("td:eq("+iCol+")",$t.rows[iRow]).addClass("edit-cell");if($.isFunction($t.p.onSelectCell)){tmp=$("td:eq("+iCol+")",$t.rows[iRow]).html().replace(/\&nbsp\;/ig,'');$t.p.onSelectCell($t.rows[iRow].id,nm,tmp,iRow,iCol);}}
$t.p.iCol=iCol;$t.p.iRow=iRow;function getAbsoluteIndex(t,relIndex)
{var countnotvisible=0;var countvisible=0;for(i=0;i<t.cells.length;i++){var cell=t.cells(i);if(cell.style.display=='none')countnotvisible++;else countvisible++;if(countvisible>relIndex)return i;}
return i;}});},saveCell:function(iRow,iCol){return this.each(function(){var $t=this,nm,fr=null;if(!$t.grid||$t.p.cellEdit!==true){return;}
for(var k=0;k<$t.p.savedRow.length;k++){if($t.p.savedRow[k].id===iRow){fr=k;break;}};if(fr!=null){var cc=$("td:eq("+iCol+")",$t.rows[iRow]);nm=$t.p.colModel[iCol].name;var v,v2;switch($t.p.colModel[iCol].edittype){case"select":v=$("#"+iRow+"_"+nm+">option:selected",$t.rows[iRow]).val();v2=$("#"+iRow+"_"+nm+">option:selected",$t.rows[iRow]).text();break;case"checkbox":var cbv=$t.p.colModel[iCol].editoptions.value.split(":")||["Yes","No"];v=$("#"+iRow+"_"+nm,$t.rows[iRow]).attr("checked")?cbv[0]:cbv[1];v2=v;break;case"password":case"text":case"textarea":v=$("#"+iRow+"_"+nm,$t.rows[iRow]).val();v2=v;break;}
if(v2!=$t.p.savedRow[fr].v){if($.isFunction($t.p.beforeSaveCell)){var vv=$t.p.beforeSaveCell($t.rows[iRow].id,nm,v,iRow,iCol);if(vv){v=vv;}}
var cv=checkValues(v,iCol,$t);if(cv[0]===true){var addpost={};if($.isFunction($t.p.beforeSubmitCell)){addpost=$t.p.beforeSubmitCell($t.rows[iRow].id,nm,v,iRow,iCol);if(!addpost){addpost={};}}
if($t.p.cellsubmit=='remote'){if($t.p.cellurl){var postdata={};postdata[nm]=v;postdata["id"]=$t.rows[iRow].id;postdata=$.extend(addpost,postdata);$.ajax({url:$t.p.cellurl,data:postdata,type:"POST",complete:function(result,stat){if(stat=='success'){if($.isFunction($t.p.afterSubmitCell)){var ret=$t.p.afterSubmitCell(result,postdata.id,nm,v,iRow,iCol);if(ret&&ret[0]===true){$(cc).empty().html(v2||"&nbsp;");$(cc).addClass("dirty-cell");$($t.rows[iRow]).addClass("edited");if($.isFunction($t.p.afterSaveCell)){$t.p.afterSaveCell($t.rows[iRow].id,nm,v,iRow,iCol);}
$t.p.savedRow.splice(fr,1);}else{info_dialog($.jgrid.errors.errcap,ret[1],$.jgrid.edit.bClose,$t.p.imgpath);$($t).restoreCell(iRow,iCol);}}else{$(cc).empty().html(v2||"&nbsp;");$(cc).addClass("dirty-cell");$($t.rows[iRow]).addClass("edited");if($.isFunction($t.p.afterSaveCell)){$t.p.afterSaveCell($t.rows[iRow].id,nm,v,iRow,iCol);}
$t.p.savedRow.splice(fr,1);}}},error:function(res,stat){if($.isFunction($t.p.errorCell)){$t.p.errorCell(res,stat);}else{info_dialog($.jgrid.errors.errcap,res.status+" : "+res.statusText+"<br/>"+stat,$.jgrid.edit.bClose,$t.p.imgpath);$($t).restoreCell(iRow,iCol);}}});}else{try{info_dialog($.jgrid.errors.errcap,$.jgrid.errors.nourl,$.jgrid.edit.bClose,$t.p.imgpath);$($t).restoreCell(iRow,iCol);}catch(e){}}}
if($t.p.cellsubmit=='clientArray'){$(cc).empty().html(v2||"&nbsp;");$(cc).addClass("dirty-cell");$($t.rows[iRow]).addClass("edited");if($.isFunction($t.p.afterSaveCell)){$t.p.afterSaveCell($t.rows[iRow].id,nm,v,iRow,iCol);}
$t.p.savedRow.splice(fr,1);}}else{try{window.setTimeout(function(){info_dialog($.jgrid.errors.errcap,v+" "+cv[1],$.jgrid.edit.bClose,$t.p.imgpath)},100);$($t).restoreCell(iRow,iCol);}catch(e){}}}else{$($t).restoreCell(iRow,iCol);}}
if($.browser.opera){$("#"+$t.p.knv).attr("tabindex","-1").focus();}else{window.setTimeout(function(){$("#"+$t.p.knv).attr("tabindex","-1").focus();},0);}});},nextCell:function(iRow,iCol){return this.each(function(){var $t=this,nCol=false,tmp;if(!$t.grid||$t.p.cellEdit!==true){return;}
for(var i=iCol+1;i<$t.p.colModel.length;i++){if($t.p.colModel[i].editable===true){nCol=i;break;}}
if(nCol!==false){$($t).saveCell(iRow,iCol);$($t).editCell(iRow,nCol,true);}else{if($t.p.savedRow.length>0){$($t).saveCell(iRow,iCol);}}});},restoreCell:function(iRow,iCol){return this.each(function(){var $t=this,nm,fr=null;if(!$t.grid||$t.p.cellEdit!==true){return;}
for(var k=0;k<$t.p.savedRow.length;k++){if($t.p.savedRow[k].id===iRow){fr=k;break;}}
if(fr!=null){var cc=$("td:eq("+iCol+")",$t.rows[iRow]);try{$.datepicker.hideDatepicker();}catch(e){$.datepicker._hideDatepicker();}
$(":input",cc).unbind();nm=$t.p.colModel[iCol].name;$(cc).empty().html($t.p.savedRow[fr].v||"&nbsp;");$t.p.savedRow=[];}
window.setTimeout(function(){$("#"+$t.p.knv).attr("tabindex","-1").focus();},0);});},GridNav:function(){return this.each(function(){var $t=this;if(!$t.grid||$t.p.cellEdit!==true){return;}
$t.p.knv=$("table:first",$t.grid.bDiv).attr("id")+"_kn";var selection=$("<span style='width:0px;height:0px;background-color:black;' tabindex='0'><span tabindex='-1' style='width:0px;height:0px;background-color:grey' id='"+$t.p.knv+"'></span></span>");$(selection).insertBefore($t.grid.cDiv);$("#"+$t.p.knv).focus();$("#"+$t.p.knv).keydown(function(e){switch(e.keyCode){case 38:if($t.p.iRow-1>=1){scrollGrid($t.p.iRow-1,$t.p.iCol,'vu');$($t).editCell($t.p.iRow-1,$t.p.iCol,false);}
break;case 40:if($t.p.iRow+1<=$t.rows.length-1){scrollGrid($t.p.iRow+1,$t.p.iCol,'vd');$($t).editCell($t.p.iRow+1,$t.p.iCol,false);}
break;case 37:if($t.p.iCol-1>=0){var i=findNextVisible($t.p.iCol-1,'lft');scrollGrid($t.p.iRow,i,'h');$($t).editCell($t.p.iRow,i,false);}
break;case 39:if($t.p.iCol+1<=$t.p.colModel.length-1){var i=findNextVisible($t.p.iCol+1,'rgt');scrollGrid($t.p.iRow,i,'h');$($t).editCell($t.p.iRow,i,false);}
break;case 13:if($t.p.iCol&&$t.p.iRow){$($t).editCell($t.p.iRow,$t.p.iCol,true);}
break;}
return false;});function scrollGrid(iR,iC,tp){if(tp.substr(0,1)=='v'){var ch=$($t.grid.bDiv)[0].clientHeight;var st=$($t.grid.bDiv)[0].scrollTop;var nROT=$t.rows[iR].offsetTop+$t.rows[iR].clientHeight;var pROT=$t.rows[iR].offsetTop;if(tp=='vd'){if(nROT>=ch){$($t.grid.bDiv)[0].scrollTop=$($t.grid.bDiv)[0].scrollTop+$t.rows[iR].clientHeight;}}
if(tp=='vu'){if(pROT<st){$($t.grid.bDiv)[0].scrollTop=$($t.grid.bDiv)[0].scrollTop-$t.rows[iR].clientHeight;}}}
if(tp=='h'){var cw=$($t.grid.bDiv)[0].clientWidth;var sl=$($t.grid.bDiv)[0].scrollLeft;var nCOL=$t.rows[iR].cells[iC].offsetLeft+$t.rows[iR].cells[iC].clientWidth;var pCOL=$t.rows[iR].cells[iC].offsetLeft;if(nCOL>=cw+parseInt(sl)){$($t.grid.bDiv)[0].scrollLeft=$($t.grid.bDiv)[0].scrollLeft+$t.rows[iR].cells[iC].clientWidth;}else if(pCOL<sl){$($t.grid.bDiv)[0].scrollLeft=$($t.grid.bDiv)[0].scrollLeft-$t.rows[iR].cells[iC].clientWidth;}}};function findNextVisible(iC,act){var ind;if(act=='lft'){ind=iC+1;for(var i=iC;i>=0;i--){if($t.p.colModel[i].hidden!==true){ind=i;break;}}}
if(act=='rgt'){ind=iC-1;for(var i=iC;i<$t.p.colModel.length;i++){if($t.p.colModel[i].hidden!==true){ind=i;break;}}}
return ind;};});},getChangedCells:function(mthd){var ret=[];if(!mthd){mthd='all';}
this.each(function(){var $t=this;if(!$t.grid||$t.p.cellEdit!==true){return;}
$($t.rows).slice(1).each(function(j){var res={};if($(this).hasClass("edited")){$('td',this).each(function(i){nm=$t.p.colModel[i].name;if(nm!=='cb'&&nm!=='subgrid'){if(mthd=='dirty'){if($(this).hasClass('dirty-cell')){res[nm]=$(this).html().replace(/\&nbsp\;/ig,'');}}else{res[nm]=$(this).html().replace(/\&nbsp\;/ig,'');}}});res["id"]=this.id;ret.push(res);}})});return ret;}});})(jQuery);