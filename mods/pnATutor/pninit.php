<?php
// $Id: pninit.php
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Filename: 1.0
// Based on : pnATutor
// Postnuked  by Cas Nuy
// Purpose of file:  Initialisation functions for pnATutor
// ----------------------------------------------------------------------

/**
 * initialise the pnATutor module
 * This function is only ever called once during the lifetime of this module
 */
function pnATutor_init()
{

    // pnATutor Default Settings
	pnModSetVar(pnATutor, '_loc', '/ATutor');
	pnModSetVar(pnATutor, '_window', 0);
	pnModSetVar(pnATutor, '_db', 'atutor');
	pnModSetVar(pnATutor, '_prf', '');
	pnModSetVar(pnATutor, '_guest', 'n');
	pnModSetVar(pnATutor, '_users', 'n');
	pnModSetVar(pnATutor, '_version','1.3.1');
   return true;
}
/**
 * update the pnATutor module
 * This function is only ever called once during the lifetime of this module
 */
function pnATutor_upgrade()
{
pnModSetVar(pnATutor, '_prf', '');
return true;
}


/**
 * delete the pnATutor module
 * This function is only ever called once during the lifetime of this module
 */
function pnATutor_delete()
{

pnModDelVar(pnATutor, '_loc');
pnModDelVar(pnATutor, '_window');
pnModDelVar(pnATutor, '_wrap');
pnModDelVar(pnATutor, '_db');
pnModDelVar(pnATutor, '_guest');
pnModDelVar(pnATutor, '_users');
pnModDelVar(pnATutor, '_version');
pnModDelVar(pnATutor, '_prf');

return true;
}

?>
