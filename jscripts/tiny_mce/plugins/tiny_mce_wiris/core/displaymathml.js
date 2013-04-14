//
//  Copyright (c) 2011, Maths for More S.L. http://www.wiris.com
//  This file is part of WIRIS Plugin.
//
//  WIRIS Plugin is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  any later version.
//
//  WIRIS Plugin is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with WIRIS Plugin. If not, see <http://www.gnu.org/licenses/>.
//

function generateEntities(s) {
	d = "";
	for (i=0;i<s.length;i++) {
		c = s.charCodeAt(i);
		if (c>=128) {
			d+="&#"+c+";";
		} else {
			d+=s.charAt(i);
		}
	}
	return d;
}

function isInAContentEditableElement(element) {
	if (element == null) {
		return false;
	}
			
	if (element.contentEditable && element.contentEditable !== 'inherit') {
		return true;
	}

	return isInAContentEditableElement(element.parentNode);
}

function fixVerticalAlign(img) {
	img.align = '';
	img.style.verticalAlign = (-img.height / 2) + 'px';
}

function fixVerticalAlignForAll() {
	var images = document.getElementsByTagName('img');
		
	for (var i = images.length - 1; i >= 0; --i) {
		if (images[i].className == 'Wirisformula' && !isInAContentEditableElement(images[i])) {
			images[i].align = '';
			images[i].style.verticalAlign = (-images[i].height / 2) + 'px';
		}
	}
}

function mathmlFunction() {

	var maths = document.getElementsByTagName('math');
	
	for (i = 0; i < maths.length; i++) {
		var mathNode = maths[i];
		var container = document.createElement('span');
		container.className = 'wrs_viewer';
		mathNode.parentNode.replaceChild(container, mathNode);
		container.appendChild(mathNode);
		
		var mathml = container.innerHTML;
		if (mathml.indexOf("<?XML")==0) {
			j=mathml.indexOf("/>");
			if (j>=0)
				mathml=mathml.substring(j+2);
		}
		var img = document.createElement('img');
		mathml = generateEntities(mathml);
		// Needed only for ie
		if (img.attachEvent!=null) {
			img.attachEvent( "onload", function() {fixVerticalAlign(img);});
		}
		img.src = 'http://localhost/aspx-demo_nicedit_wiris/nicedit/nicedit_wiris/integration/showimage.aspx?mml='+encodeURIComponent(mathml);
		img.align = 'middle';
		img.className = 'Wirisformula';
		
		container.parentNode.replaceChild(img, container);
	}
	// setTimeout(fixVerticalAlignForAll,1);
}

if (window.attachEvent) {
	window.attachEvent('onload', mathmlFunction);
}

window.addEventListener('load',mathmlFunction,false);
