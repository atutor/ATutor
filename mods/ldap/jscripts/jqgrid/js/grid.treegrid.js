;(function($) {
/*
**
 * jqGrid extension - Tree Grid
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/ 
$.fn.extend({
	setTreeNode : function(rd, row){
		return this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			var expCol=0,i=0;
			if(!$t.p.expColInd) {
				for (var key in $t.p.colModel){
					if($t.p.colModel[key].name == $t.p.ExpandColumn) {
						expCol = i;
						$t.p.expColInd = expCol;
						break;
					}
					i++;
				}
				if(!$t.p.expColInd ) {$t.p.expColInd = expCol;}
			} else {
				expCol = $t.p.expColInd;
			}
			var level = $t.p.treeReader.level_field;
			var expanded = $t.p.treeReader.expanded_field;
			var isLeaf = $t.p.treeReader.leaf_field;
			row.lft = rd[$t.p.treeReader.left_field];
			row.rgt = rd[$t.p.treeReader.right_field];
			row.level = rd[level];
			if(!rd[isLeaf]) {
				// NS Model
				rd[isLeaf] = (parseInt(row.rgt,10) === parseInt(row.lft,10)+1) ? 'true' : 'false';
			}
			var curExpand = (rd[expanded] && rd[expanded] == "true") ? true : false;
			var curLevel = parseInt(row.level,10);
			var ident,lftpos;
			if($t.p.tree_root_level === 0) {
				ident = curLevel+1;
				lftpos = curLevel;
			} else {
				ident = curLevel;
				lftpos = curLevel -1;
			}
			var twrap = document.createElement("div");
			$(twrap).addClass("tree-wrap").width(ident*18);
			var treeimg = document.createElement("div");
			$(treeimg).css("left",lftpos*18);
			twrap.appendChild(treeimg);

			if(rd[isLeaf] == "true") {
				$(treeimg).addClass("tree-leaf");
				row.isLeaf = true;
			} else {
				if(rd[expanded] == "true") {
					$(treeimg).addClass("tree-minus treeclick");
					row.expanded = true;
				} else {
					$(treeimg).addClass("tree-plus treeclick");
					row.expanded = false;
				}
			}
			if(parseInt(rd[level],10) !== parseInt($t.p.tree_root_level,10)) {                
				if(!$($t).isVisibleNode(row)){ 
					$(row).css("display","none");
				}
			}
			var mhtm = $("td:eq("+expCol+")",row).html();
			var thecell = $("td:eq("+expCol+")",row).html("<span>"+mhtm+"</span>").prepend(twrap);
			$(".treeclick",thecell).click(function(e){
				var target = e.target || e.srcElement;
				var ind =$(target,$t.rows).parents("tr:first")[0].rowIndex;
				if(!$t.rows[ind].isLeaf){
					if($t.rows[ind].expanded){
						$($t).collapseRow($t.rows[ind]);
						$($t).collapseNode($t.rows[ind]);
					} else {
						$($t).expandRow($t.rows[ind]);
						$($t).expandNode($t.rows[ind]);
					}
				}
				e.stopPropagation();
			});
		});
	},
	expandRow: function (record){
		this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			var childern = $($t).getNodeChildren(record);
			//if ($($t).isVisibleNode(record)) {
			$(childern).each(function(i){
				$(this).css("display","");
				if(this.expanded) {
					$($t).expandRow(this);
				}
			});
			//}
		});
	},
	collapseRow : function (record) {
		this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			var childern = $($t).getNodeChildren(record);
			$(childern).each(function(i){
				$(this).css("display","none");
				$($t).collapseRow(this);
			});
		});
	},
	// NS model
	getRootNodes : function() {
		var result = [];
		this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			$($t.rows).each(function(i){
				if(parseInt(this.level,10) === parseInt($t.p.tree_root_level,10)) {
					result.push(this);
				}
			});
		});
		return result;
	},
	getNodeDepth : function(rc) {
		var ret = null;
		this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			ret = parseInt(rc.level,10) - parseInt(this.p.tree_root_level,10);                
		});
		return ret;
	},
	getNodeParent : function(rc) {
		var result = null;
		this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			var lft = parseInt(rc.lft,10), rgt = parseInt(rc.rgt,10), level = parseInt(rc.level,10);
			$(this.rows).each(function(){
				if(parseInt(this.level,10) === level-1 && parseInt(this.lft) < lft && parseInt(this.rgt) > rgt) {
					result = this;
					return false;
				}
			});
		});
		return result;
	},
	getNodeChildren : function(rc) {
		var result = [];
		this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			var lft = parseInt(rc.lft,10), rgt = parseInt(rc.rgt,10), level = parseInt(rc.level,10);
			var ind = rc.rowIndex;
			$(this.rows).slice(1).each(function(i){
				if(parseInt(this.level,10) === level+1 && parseInt(this.lft,10) > lft && parseInt(this.rgt,10) < rgt) {
					result.push(this);
				}
			});
		});
		return result;
	},
	// End NS Model
	getNodeAncestors : function(rc) {
		var ancestors = [];
		this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			var parent = $(this).getNodeParent(rc);
			while (parent) {
				ancestors.push(parent);
				parent = $(this).getNodeParent(parent);	
			}
		});
		return ancestors;
	},
	isVisibleNode : function(rc) {
		var result = true;
		this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			var ancestors = $($t).getNodeAncestors(rc);
			$(ancestors).each(function(){
				result = result && this.expanded;
				if(!result) {return false;}
			});
		});
		return result;
	},
	isNodeLoaded : function(rc) {
		var result;
		this.each(function(){
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			if(rc.loaded !== undefined) {
				result = rc.loaded;
			} else if( rc.isLeaf || $($t).getNodeChildren(rc).length > 0){
				result = true;
			} else {
				result = false;
			}
		});
		return result;
	},
	expandNode : function(rc) {
		return this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			if(!rc.expanded) {
				if( $(this).isNodeLoaded(rc) ) {
					rc.expanded = true;
					$("div.treeclick",rc).removeClass("tree-plus").addClass("tree-minus");
				} else {
					rc.expanded = true;
					$("div.treeclick",rc).removeClass("tree-plus").addClass("tree-minus");
					this.p.treeANode = rc.rowIndex;
					this.p.datatype = this.p.treedatatype;
					$(this).setGridParam({postData:{nodeid:rc.id,n_left:rc.lft,n_right:rc.rgt,n_level:rc.level}});
					$(this).trigger("reloadGrid");
					this.treeANode = 0;
					$(this).setGridParam({postData:{nodeid:'',n_left:'',n_right:'',n_level:''}})
				}
			}
		});
	},
	collapseNode : function(rc) {
		return this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			if(rc.expanded) {
				rc.expanded = false;
				$("div.treeclick",rc).removeClass("tree-minus").addClass("tree-plus");
			}
		});
	},
	SortTree : function( newDir) {
		return this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			var i, len,
			rec, records = [],
			roots = $(this).getRootNodes();
			// Sorting roots
			roots.sort(function(a, b) {
				if (a.sortKey < b.sortKey) {return -newDir;}
				if (a.sortKey > b.sortKey) {return newDir;}
				return 0;
			});
			// Sorting children
			for (i = 0, len = roots.length; i < len; i++) {
				rec = roots[i];
				records.push(rec);
				$(this).collectChildrenSortTree(records, rec, newDir);
			}
			var $t = this;
			$.each(records, function(index, row) {
				$('tbody',$t.grid.bDiv).append(row);
				row.sortKey = null;
			});
		});
	},
	collectChildrenSortTree : function(records, rec, newDir) {
		return this.each(function(){
			if(!this.grid || !this.p.treeGrid) { return; }
			var i, len,
			child, 
			children = $(this).getNodeChildren(rec);
			children.sort(function(a, b) {
				if (a.sortKey < b.sortKey) {return -newDir;}
				if (a.sortKey > b.sortKey) {return newDir;}
				return 0;
			});
			for (i = 0, len = children.length; i < len; i++) {
				child = children[i];
				records.push(child);
				$(this).collectChildrenSortTree(records, child,newDir); 
			}
		});
	},
	// experimental 
	setTreeRow : function(rowid, data) {
		var nm, success=false;
		this.each(function(){
			var t = this;
			if(!t.grid || !t.p.treeGrid) { return false; }
			if( data ) {
				var ind = $(t).getInd(t.rows,rowid);
				if(!ind) {return success;}
				success=true;
				$(this.p.colModel).each(function(i){
					nm = this.name;
					if(data[nm] !== 'undefined') {
						if(nm == t.p.ExpandColumn && t.p.treeGrid===true) {
							$("td:eq("+i+") > span:first",t.rows[ind]).html(data[nm]);
						} else {
							$("td:eq("+i+")",t.rows[ind]).html(data[nm]);
						}
						success = true;
					}
				});
			}
		});
		return success;
	},
	delTreeNode : function (rowid) {
		return this.each(function () {
			var $t = this;
			if(!$t.grid || !$t.p.treeGrid) { return; }
			var rc = $($t).getInd($t.rows,rowid,true);
			if (rc) {
				var dr = $($t).getNodeChildren(rc);
				if(dr.length>0){
					for (var i=0;i<dr.length;i++){
						$($t).delRowData(dr[i].id);
					}
				}
				$($t).delRowData(rc.id);
			}
		});
	}
});
})(jQuery);