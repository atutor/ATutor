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

if (window.attachEvent) {
	window.attachEvent('onload', function () {
		function isInAContentEditableElement(element) {
			if (element == null) {
				return false;
			}
			
			if (element.contentEditable && element.contentEditable !== 'inherit') {
				return true;
			}
			
			return isInAContentEditableElement(element.parentNode);
		}
		
		var images = document.getElementsByTagName('img');
		
		for (var i = images.length - 1; i >= 0; --i) {
			if (images[i].className == 'Wirisformula' && !isInAContentEditableElement(images[i])) {
				images[i].align = '';
				images[i].style.verticalAlign = (-images[i].height / 2) + 'px';
			}
		}
	});
}
