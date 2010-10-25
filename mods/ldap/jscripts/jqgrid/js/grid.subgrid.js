;(function($){
/**
 * jqGrid extension for SubGrid Data
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.fn.addSubGrid = function(t,row,pos,rowelem) {
	return this.each(function(){
		var ts = this;
		if (!ts.grid ) { return; }
		var td, res,_id, pID;
		td = document.createElement("td");
		$(td,t).html("<img src='"+ts.p.imgpath+"plus.gif'/>")
			.toggle( function(e) {
				$(this).html("<img src='"+ts.p.imgpath+"minus.gif'/>");
				pID = $("table:first",ts.grid.bDiv).attr("id");
				res = $(this).parent();
				var atd= pos==1?'<td></td>':'';
				_id = $(res).attr("id");
				var nhc = 0;
				$.each(ts.p.colModel,function(i,v){
					if(this.hidden === true) {nhc++;}
				});
				var subdata = "<tr class='subgrid'>"+atd+"<td><img src='"+ts.p.imgpath+"line3.gif'/></td><td colspan='"+parseInt(ts.p.colNames.length-1-nhc)+"'><div id="+pID+"_"+_id+" class='tablediv'>";
				$(this).parent().after( subdata+ "</div></td></tr>" );
				$(".tablediv",ts).css("width", ts.grid.width-20+"px");
				if( typeof ts.p.subGridRowExpanded === 'function') {
					ts.p.subGridRowExpanded(pID+"_"+ _id,_id);
				} else {
					populatesubgrid(res);
				}
			}, function(e) {
				if( typeof ts.p.subGridRowColapsed === 'function') {
					res = $(this).parent();
					_id = $(res).attr("id");
					ts.p.subGridRowColapsed(pID+"_"+_id,_id );
				};
				$(this).parent().next().remove(".subgrid");
				$(this).html("<img src='"+ts.p.imgpath+"plus.gif'/>");
			});
		row.appendChild(td);
		//-------------------------
		var populatesubgrid = function( rd ) {
			var res,sid,dp;
			sid = $(rd).attr("id");
			dp = {id:sid};
			if(!ts.p.subGridModel[0]) { return false; }
			if(ts.p.subGridModel[0].params) {
				for(var j=0; j < ts.p.subGridModel[0].params.length; j++) {
					for(var i=0; i<ts.p.colModel.length; i++) {
						if(ts.p.colModel[i].name == ts.p.subGridModel[0].params[j]) {
							dp[ts.p.colModel[i].name]= $("td:eq("+i+")",rd).text().replace(/\&nbsp\;/ig,'');
						}
					}
				}
			}
			if(!ts.grid.hDiv.loading) {
				ts.grid.hDiv.loading = true;
				$("div.loading",ts.grid.hDiv).fadeIn("fast");
				switch(ts.p.datatype) {
					case "xml":
					$.ajax({type:ts.p.mtype, url: ts.p.subGridUrl, dataType:"xml",data: dp, complete: function(sxml) { subGridJXml(sxml.responseXML, sid); } });
					break;
					case "json":
					$.ajax({type:ts.p.mtype, url: ts.p.subGridUrl, dataType:"json",data: dp, complete: function(JSON) { res = subGridJXml(JSON,sid); } });
					break;
				}
			}
			return false;
		};
		var subGridCell = function(trdiv,cell,pos){
			var tddiv;
			tddiv = document.createElement("div");
			tddiv.className = "celldiv";
			$(tddiv).html(cell);
			$(tddiv).width( ts.p.subGridModel[0].width[pos] || 80);
			trdiv.appendChild(tddiv);
		};
		var subGridJXml = function(sjxml, sbid){
			var trdiv, tddiv,result = "", i,cur, sgmap;
			var dummy = document.createElement("span");
			trdiv = document.createElement("div");
			trdiv.className="rowdiv";
			for (i = 0; i<ts.p.subGridModel[0].name.length; i++) {
				tddiv = document.createElement("div");
				tddiv.className = "celldivth";
				$(tddiv).html(ts.p.subGridModel[0].name[i]);
				$(tddiv).width( ts.p.subGridModel[0].width[i]);
				trdiv.appendChild(tddiv);
			}
			dummy.appendChild(trdiv);
			if (sjxml){
				if(ts.p.datatype === "xml") {
					sgmap = ts.p.xmlReader.subgrid;
					$(sgmap.root+">"+sgmap.row, sjxml).each( function(){
						trdiv = document.createElement("div");
						trdiv.className="rowdiv";
						if(sgmap.repeatitems === true) {
							$(sgmap.cell,this).each( function(i) {
								subGridCell(trdiv, this.textContent || this.text || '&nbsp;',i);
							});
						} else {
							var f = ts.p.subGridModel[0].mapping;
							if (f) {
								for (i=0;i<f.length;i++) {
									subGridCell(trdiv, $(f[i],this).text() || '&nbsp;',i);
								}
							}
						}
						dummy.appendChild(trdiv);
					});
				} else {
					sjxml = eval("("+sjxml.responseText+")");
					sgmap = ts.p.jsonReader.subgrid;
					for (i=0;i<sjxml[sgmap.root].length;i++) {
						cur = sjxml[sgmap.root][i];
						trdiv = document.createElement("div");
						trdiv.className="rowdiv";
						if(sgmap.repeatitems === true) {
							if(sgmap.cell) { cur=cur[sgmap.cell]; }
							for (var j=0;j<cur.length;j++) {
								subGridCell(trdiv, cur[j] || '&nbsp;',j);
							}
						} else {
							var f = ts.p.subGridModel[0].mapping;
							if(f.length) {
								for (var j=0;j<f.length;j++) {
									subGridCell(trdiv, cur[f[j]] || '&nbsp;',j);
								}
							}
						}
						dummy.appendChild(trdiv);
					}
				}
				var pID = $("table:first",ts.grid.bDiv).attr("id")+"_";
				$("#"+pID+sbid).append($(dummy).html());
				sjxml = null;
				ts.grid.hDiv.loading = false;
				$("div.loading",ts.grid.hDiv).fadeOut("fast");
			}
			return false;
		}
	});
};
})(jQuery);
