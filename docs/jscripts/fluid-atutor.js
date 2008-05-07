var demo = demo || {};

demo.initMyLayout = function () {
  var myLayoutContainer = fluid.utils.jById ("contentwrapper");

  var myOrderableFinder = function () {
    return jQuery ("[id^=atutor]", myLayoutContainer);
  };

  var layoutHandler = new fluid.GridLayoutHandler (myOrderableFinder);

  return new fluid.Reorderer (myLayoutContainer, myOrderableFinder, layoutHandler);
};