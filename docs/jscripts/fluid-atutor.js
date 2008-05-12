var demo = demo || {};

demo.initMyLayout = function () {
  var myLayoutContainer = fluid.utils.jById ("contentwrapper");

  var myOrderableFinder = function () {
    return jQuery ("[id^=atutor]", myLayoutContainer);
  };

  var layoutHandler = new fluid.GridLayoutHandler (myOrderableFinder, { 
		orderChangedCallback : function(){ 
			//save the state to the db
			$(this).load("save_state.php", { id : this.id })

		} });



  return new fluid.Reorderer (myLayoutContainer, myOrderableFinder, layoutHandler);
};