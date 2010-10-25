;(function($){
/**
 * jqGrid English Translation
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = {};

$.jgrid.defaults = {
	recordtext: "Row(s)",
	loadtext: "Loading...",
	pgtext : "/"
};
$.jgrid.search = {
    caption: "Search...",
    Find: "Find",
    Reset: "Reset",
    odata : ['equal', 'not equal', 'less', 'less or equal','greater','greater or equal', 'begins with','ends with','contains' ]
};
$.jgrid.edit = {
    addCaption: "Add Record",
    editCaption: "Edit Record",
    bSubmit: "Submit",
    bCancel: "Cancel",
	bClose: "Close",
    processData: "Processing...",
    msg: {
        required:"Field is required",
        number:"Please, enter valid number",
        minValue:"value must be greater than or equal to ",
        maxValue:"value must be less than or equal to",
        email: "is not a valid e-mail",
        integer: "Please, enter valid integer value"
    }
};
$.jgrid.del = {
    caption: "Delete",
    msg: "Delete selected record(s)?",
    bSubmit: "Delete",
    bCancel: "Cancel",
    processData: "Processing..."
};
$.jgrid.nav = {
	edittext: " ",
    edittitle: "Edit selected row",
	addtext:" ",
    addtitle: "Add new row",
    deltext: " ",
    deltitle: "Delete selected row",
    searchtext: " ",
    searchtitle: "Find records",
    refreshtext: "",
    refreshtitle: "Reload Grid",
    alertcap: "Warning",
    alerttext: "Please, select row"
};
// setcolumns module
$.jgrid.col ={
    caption: "Show/Hide Columns",
    bSubmit: "Submit",
    bCancel: "Cancel"	
};
$.jgrid.errors = {
	errcap : "Error",
	nourl : "No url is set",
	norecords: "No records to process"
};
})(jQuery);
