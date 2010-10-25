/*
 * jqGrid  3.3 - jQuery Grid
 * Copyright (c) 2008, Tony Tomov, tony@trirand.com
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2008-10-14 rev 64
 */


function tableToGrid(selector){$(selector).each(function(){if(this.grid){return;}
$(this).width("99%");var w=$(this).width();var inputCheckbox=$('input[type=checkbox]:first',$(this));var inputRadio=$('input[type=radio]:first',$(this));var selectMultiple=inputCheckbox.length>0;var selectSingle=!selectMultiple&&inputRadio.length>0;var selectable=selectMultiple||selectSingle;var inputName=inputCheckbox.attr("name")||inputRadio.attr("name");var colModel=[];var colNames=[];$('th',$(this)).each(function(){if(colModel.length==0&&selectable){colModel.push({name:'__selection__',index:'__selection__',width:0,hidden:true});colNames.push('__selection__');}else{colModel.push({name:$(this).html(),index:$(this).html(),width:$(this).width()||150});colNames.push($(this).html());}});var data=[];var rowIds=[];var rowChecked=[];$('tbody > tr',$(this)).each(function(){var row={};var rowPos=0;data.push(row);$('td',$(this)).each(function(){if(rowPos==0&&selectable){var input=$('input',$(this));var rowId=input.attr("value");rowIds.push(rowId||data.length);if(input.attr("checked")){rowChecked.push(rowId);}
row[colModel[rowPos].name]=input.attr("value");}else{row[colModel[rowPos].name]=$(this).html();}
rowPos++;});});$(this).empty();$(this).addClass("scroll");$(this).jqGrid({datatype:"local",width:w,colNames:colNames,colModel:colModel,multiselect:selectMultiple});for(var a=0;a<data.length;a++){var id=null;if(rowIds.length>0){id=rowIds[a];if(id&&id.replace){id=encodeURIComponent(id).replace(/[.\-%]/g,"_");}}
if(id==null){id=a+1;}
$(this).addRowData(id,data[a]);}
for(var a=0;a<rowChecked.length;a++){$(this).setSelection(rowChecked[a]);}});};