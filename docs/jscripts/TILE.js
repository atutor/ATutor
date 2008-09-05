
 function queryPopUp(width,height,q,wname,url) {
  var winopts = "resizable=yes,scrollbars=yes,toolbar=no,location=no,height=" + height + ",width=" + width;
  var query = url + "&query=" + q; 
  return window.open(query,wname,winopts);
}

function popUp(width,height,url,wname,smallwindow) {
  var windowWidth = 0, windowHeight = 0;
  if (smallwindow) {
    windowWidth = width;
    windowHeight = height;
  }
  else {
      if (screen && screen.width && screen.height) {
        // Desktop
        windowWidth = screen.width;
        windowHeight = screen.height * 0.85;
      }
      else if (window.innerWidth && window.innerHeight) {
        //Non-IE
        windowWidth = window.innerWidth;
        windowHeight = window.innerHeight;
      }
      else if (document.documentElement &&
               (document.documentElement.offsetWidth &&
                document.documentElement.offsetHeight)) {
        //IE 6+ in 'standards compliant mode'
        windowWidth = document.documentElement.offsetWidth;
        windowHeight = document.documentElement.offsetHeight;
      }
      else if (document.body &&
               (document.body.offsetWidth && document.body.offsetHeight)) {
        //IE 4 compatible
        windowWidth = document.body.offsetWidth;
        windowHeight = document.body.offsetHeight;
      }
      else {
        windowWidth = width;
        windowHeight = height;
      }
  }
  windowWidth *= 0.95;
  var winopts = "resizable=yes,scrollbars=yes,toolbar=yes,location=no,height=" + windowHeight + ",width=" + windowWidth;
  var helpWindowzzz = window.open(url,wname,winopts)
  helpWindowzzz.focus();
  helpWindowzzz.moveTo(0, 0);

}
      function changeColour(c1, c2, c3) {
  var frm = document.forms[1];
  var c1Num = frm.elements[c1].selectedIndex;
  var c2Num = frm.elements[c2].selectedIndex;
  var c3Num = frm.elements[c3].selectedIndex;
  if (c1Num == c2Num) {
    if (c1Num == 0) {
      frm.elements[c2].selectedIndex = 1;
      c2Num = 1;
    } else {
      frm.elements[c2].selectedIndex = 0;
      c2Num = 0;
    }
  } else if (c1Num == c3Num) {
    if (c1Num == 0) {
      frm.elements[c3].selectedIndex = 1;
      c3Num = 1;
    } else {
      frm.elements[c3].selectedIndex = 0;
      c3Num = 0;
    }
  }

  if (c2Num == c3Num) {
    if (c1Num == 0 || c1Num == 1) {
      frm.elements[c3].selectedIndex = 2;
    } else if (c2Num == 0) {
      frm.elements[c3].selectedIndex = 1;
    } else if (c2Num == 1) {
      frm.elements[c3].selectedIndex = 0;
    }
  }
}

function setPreviewSize(fontVal) {
	var fontSet = document.getElementById('fontsize');
	var docSize = document.getElementById('fontsize').value+'pt';
	var docBase = document.getElementById('previewText');
	docBase.style.fontSize = docSize;
	docBase = document.getElementById('highlightedPreview');
	docBase.style.fontSize = docSize;
}
function setPreviewFace() {
	var faceSet = document.getElementById('fontface');
	var faceVal = document.getElementById('fontface').value;
	var docBase = document.getElementById('previewText');
	docBase.style.fontFamily = faceVal;
	docBase = document.getElementById('highlightedPreview');
	docBase.style.fontFamily = faceVal;
}
function setPreviewColours() {
	var fgSet = document.getElementById('fg');
	var fgVal = document.getElementById('fg').value;
	var bgSet = document.getElementById('bg');
	var bgVal = document.getElementById('bg').value;
	var hlSet = document.getElementById('hl');
	var hlVal = document.getElementById('hl').value;

        fgVal = '\#'+fgVal.substr(0,6);
        bgVal = '\#'+bgVal.substr(0,6);
        hlVal = '\#'+hlVal.substr(0,6);
        
	var docBase = document.getElementById('previewText');
	docBase.style.color = fgVal;
	docBase.style.backgroundColor = bgVal;

	docBase = document.getElementById('highlightedPreview');
	docBase.style.backgroundColor = hlVal;
}
      function checkATTSignLang() {
  var frm = document.forms[0];
  var value = null;
  if (frm.attSignLang[0].checked)
    value = frm.attSignLang[0].value;
  else if (frm.attSignLang[1].checked)
    value = frm.attSignLang[1].value;

  if (value == "false")
    frm.attSignLangVal.disabled=true;
  else if (value == "true")
    frm.attSignLangVal.disabled=false;
}
      function checkAudioDesc() {
  var frm = document.forms[0];
  var value = null;
  if (frm.audioDesc[0].checked)
    value = frm.audioDesc[0].value;
  else if (frm.audioDesc[1].checked)
    value = frm.audioDesc[1].value;

  if (value == "false") {
    frm.audioDescLang.disabled=true;
    frm.audioDescType[0].disabled=true;
    frm.audioDescType[1].disabled=true;
  }
  else if (value == "true") {
    frm.audioDescLang.disabled=false;
    frm.audioDescType[0].disabled=false;
    frm.audioDescType[1].disabled=false;
  }
}

function checkVisualText() {
  var frm = document.forms[0];
  var value = null;
  if (frm.visualText[0].checked)
    value = frm.visualText[0].value;
  else if (frm.visualText[1].checked)
    value = frm.visualText[1].value;

  if (value == "false") {
    frm.altTextLang.disabled=true;
    frm.longDescLang.disabled=true;
  }
  else if (value == "true") {
    frm.altTextLang.disabled=false;
    frm.longDescLang.disabled=false;
  }
}
      function checkCaptions() {
  var frm = document.forms[0];
  var value = null;
  if (frm.caption[0].checked)
    value = frm.caption[0].value;
  else if (frm.caption[1].checked)
    value = frm.caption[1].value;

  if (value == "false") {
    frm.captionType[0].disabled=true;
    frm.captionType[1].disabled=true;
    frm.captionLang.disabled=true;
    frm.enhancedCaption[0].disabled=true;
    frm.enhancedCaption[1].disabled=true;
    frm.reducedSpeed[0].disabled=true;
    frm.reducedSpeed[1].disabled=true;
    frm.captionRate.disabled=true;
  }
  else if (value == "true") {
    frm.captionType[0].disabled=false;
    frm.captionType[1].disabled=false;
    frm.captionLang.disabled=false;
    frm.enhancedCaption[0].disabled=false;
    frm.enhancedCaption[1].disabled=false;
    frm.reducedSpeed[0].disabled=false;
    frm.reducedSpeed[1].disabled=false;
    frm.captionRate.disabled=false;
    checkCaptionRate();
  }
}

function checkCaptionRate() {
  var frm = document.forms[0];
  var value = null;
  if (frm.reducedSpeed[0].checked)
    value = frm.reducedSpeed[0].value;
  else if (frm.reducedSpeed[1].checked)
    value = frm.reducedSpeed[1].value;

  if (value == "false")
    frm.captionRate.disabled=true;
  else if (value == "true")
    frm.captionRate.disabled=false;
}

function checkATASignLang() {
  var frm = document.forms[0];
  var value = null;
  if (frm.ataSignLang[0].checked)
    value = frm.ataSignLang[0].value;
  else if (frm.ataSignLang[1].checked)
    value = frm.ataSignLang[1].value;

  if (value == "false")
    frm.ataSignLangVal.disabled=true;
  else if (value == "true")
    frm.ataSignLangVal.disabled=false;
}

function allDigits(str) {
  var digits = "0123456789";
  var result = true;
  for (var i = 0; i < str.length; i++) {
    if (digits.indexOf(str.substr(i, 1)) < 0 ) {
      result = false;
      break;
    }
  }
  return result;
}

function checkCaptionRateValue() {
  var frm = document.forms[0];
  var value = null;
  var result = true;
  if (!frm.captionRate.disabled) {
    if (!allDigits(frm.captionRate.value)) {
      alert('Please enter a number for the "Caption Rate" field.');
      frm.captionRate.focus();
      result = false;
    }
    else {
      value = parseInt(frm.captionRate.value);
      if (isNaN(value) || value < 1 || value > 300) {
        alert('Please enter a number between 1 and 300 for the "Caption Rate" field.');
        frm.captionRate.focus();
        result = false;
      }
    }
  } 
  return result;
}
      var cssFilter=/^http:\/\/.+\..{2,3}\/.+/;
function checkCSS() {
  var theForm = document.forms[0];
  if (!cssFilter.test(theForm.ssURL.value)) {
    alert('Please enter a valid URL to a CSS file.');
    return false;
  }
  return true;
}
