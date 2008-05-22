var demo = demo || {};

demo.initMyLayout = function (basepath) {
	var myLayoutContainer = fluid.utils.jById ("contentwrapper");
	
	var myOrderableFinder = function () {
	return jQuery ("[id^=atutor]", myLayoutContainer);
	};
	
	var myGrabHandleFinder = function (item) {
		return jQuery ("[id=toolbar." + item.id.split(".")[1] + "]");
	};	
	
	var findItems = {movables: myOrderableFinder, 
		grabHandle: myGrabHandleFinder
	}
	var layoutHandler = new fluid.GridLayoutHandler (myOrderableFinder, { 
		orderChangedCallback : function(){ 
			//save the state to the db
			var myDivs = jQuery ("[id^=atutor]", myLayoutContainer);
			jQuery.post(basepath+"themes/fluid/save_state.php", { 'left':myDivs[0].id },
				function(data) { 
					//alert(data);
				});     
		} });
	
	return new fluid.Reorderer (myLayoutContainer, findItems, layoutHandler);
};
