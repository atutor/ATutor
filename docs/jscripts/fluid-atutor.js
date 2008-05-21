var demo = demo || {};

demo.initMyLayout = function () {
  var myLayoutContainer = fluid.utils.jById ("contentwrapper");

  var myOrderableFinder = function () {
    return jQuery ("[id^=atutor]", myLayoutContainer);
  };

  var layoutHandler = new fluid.GridLayoutHandler (myOrderableFinder, { 
		orderChangedCallback : function(){ 
			//save the state to the db
			var myDivs = jQuery ("[id^=atutor]", myLayoutContainer);
			jQuery.post("/atutor/trunk/docs/themes/default_fluid/save_state.php", { 'left':myDivs[0].id },
                function(data) { 
                	//alert(data);
                });     
		} });

  return new fluid.Reorderer (myLayoutContainer, myOrderableFinder, layoutHandler);
};
