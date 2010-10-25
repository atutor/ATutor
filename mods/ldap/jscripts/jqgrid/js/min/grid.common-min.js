/*
 * jqGrid  3.3 - jQuery Grid
 * Copyright (c) 2008, Tony Tomov, tony@trirand.com
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2008-10-14 rev 64
 */


var showModal=function(h){h.w.show();};var closeModal=function(h){h.w.hide();if(h.o){h.o.remove();}};function createModal(aIDs,content,p,insertSelector,posSelector,appendsel){var clicon=p.imgpath?p.imgpath+p.closeicon:p.closeicon;var mw=document.createElement('div');jQuery(mw).addClass("modalwin").attr("id",aIDs.themodal);var mh=jQuery('<div id="'+aIDs.modalhead+'"><table width="100%"><tbody><tr><td class="modaltext">'+p.caption+'</td> <td align="right"><a href="javascript:void(0);" class="jqmClose">'+(clicon!=''?'<img src="'+clicon+'" border="0"/>':'X')+'</a></td></tr></tbody></table> </div>').addClass("modalhead");var mc=document.createElement('div');jQuery(mc).addClass("modalcontent").attr("id",aIDs.modalcontent);jQuery(mc).append(content);mw.appendChild(mc);var loading=document.createElement("div");jQuery(loading).addClass("loading").html(p.processData||"");jQuery(mw).prepend(loading);jQuery(mw).prepend(mh);jQuery(mw).addClass("jqmWindow");if(p.drag){jQuery(mw).append("<img  class='jqResize' src='"+p.imgpath+"resize.gif'/>");}
if(appendsel===true){jQuery('body').append(mw);}
else{jQuery(mw).insertBefore(insertSelector);}
if(p.left==0&&p.top==0){var pos=[];pos=findPos(posSelector);p.left=pos[0]+4;p.top=pos[1]+4;}
if(p.width==0||!p.width){p.width=300;}
if(p.height==0||!p.width){p.height=200;}
if(!p.zIndex){p.zIndex=950;}
jQuery(mw).css({top:p.top+"px",left:p.left+"px",width:p.width+"px",height:p.height+"px",zIndex:p.zIndex});return false;};function viewModal(selector,o){o=jQuery.extend({toTop:true,overlay:10,modal:false,drag:true,onShow:showModal,onHide:closeModal},o||{});jQuery(selector).jqm(o).jqmShow();return false;};function DnRModal(modwin,handler){jQuery(handler).css('cursor','move');jQuery(modwin).jqDrag(handler).jqResize(".jqResize");return false;};function info_dialog(caption,content,c_b,pathimg){var cnt="<div id='info_id'>";cnt+="<div align='center'><br />"+content+"<br /><br />";cnt+="<input type='button' size='10' id='closedialog' class='jqmClose EditButton' value='"+c_b+"' />";cnt+="</div></div>";createModal({themodal:'info_dialog',modalhead:'info_head',modalcontent:'info_content'},cnt,{width:290,height:120,drag:false,caption:"<b>"+caption+"</b>",imgpath:pathimg,closeicon:'ico-close.gif',left:250,top:170},'','',true);viewModal("#info_dialog",{onShow:function(h){h.w.show();},onHide:function(h){h.w.hide().remove();if(h.o){h.o.remove();}},modal:true});};function findPos(obj){var curleft=curtop=0;if(obj.offsetParent){do{curleft+=obj.offsetLeft;curtop+=obj.offsetTop;}while(obj=obj.offsetParent);}
return[curleft,curtop];};function isArray(obj){if(obj.constructor.toString().indexOf("Array")==-1){return false;}else{return true;}};function createEl(eltype,options,vl,elm){var elem="";switch(eltype)
{case"textarea":elem=document.createElement("textarea");jQuery(elem).attr(options);jQuery(elem).html(vl);break;case"checkbox":elem=document.createElement("input");elem.type="checkbox";jQuery(elem).attr({id:options.id,name:options.name});if(!options.value){if(vl.toLowerCase()=='on'){elem.checked=true;elem.defaultChecked=true;elem.value=vl;}else{elem.value="on";}
jQuery(elem).attr("offval","off");}else{var cbval=options.value.split(":");if(vl==cbval[0]){elem.checked=true;elem.defaultChecked=true;}
elem.value=cbval[0];jQuery(elem).attr("offval",cbval[1]);}
break;case"select":var so=options.value.split(";"),sv,ov;elem=document.createElement("select");var msl=options.multiple===true?true:false;jQuery(elem).attr({id:options.id,name:options.name,size:Math.min(options.size,so.length),multiple:msl});for(var i=0;i<so.length;i++){sv=so[i].split(":");ov=document.createElement("option");ov.value=sv[0];ov.innerHTML=sv[1];if(!msl&&sv[1]==vl)ov.selected="selected";if(msl&&jQuery.inArray(sv[1],vl.split(","))>-1)ov.selected="selected";elem.appendChild(ov);}
break;case"text":elem=document.createElement("input");elem.type="text";elem.value=vl;if(!options.size&&elm){jQuery(elem).css("width",jQuery(elm).width()-4);}
jQuery(elem).attr(options);break;case"password":elem=document.createElement("input");elem.type="password";elem.value=vl;if(!options.size){jQuery(elem).css("width",jQuery(elm).width()-4);}
jQuery(elem).attr(options);break;case"image":elem=document.createElement("input");elem.type="image";jQuery(elem).attr(options);break;}
return elem;};function checkValues(val,valref,g){if(valref>=0){var edtrul=g.p.colModel[valref].editrules;}
if(edtrul){if(edtrul.required==true){if(val.match(/^s+$/)||val=="")return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.required,""];}
if(edtrul.number==true){if(isNaN(val))return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.number,""];}
if(edtrul.minValue&&!isNaN(edtrul.minValue)){if(parseFloat(val)<parseFloat(edtrul.minValue))return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.minValue+" "+edtrul.minValue,""];}
if(edtrul.maxValue&&!isNaN(edtrul.maxValue)){if(parseFloat(val)>parseFloat(edtrul.maxValue))return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.maxValue+" "+edtrul.maxValue,""];}
if(edtrul.email==true){var filter=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;if(!filter.test(val)){return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.email,""];}}
if(edtrul.integer==true){if(isNaN(val))return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.integer,""];if((val<0)||(val%1!=0)||(val.indexOf('.')!=-1))return[false,g.p.colNames[valref]+": "+jQuery.jgrid.edit.msg.integer,""];}}
return[true,"",""];};