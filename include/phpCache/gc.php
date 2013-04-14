<?
/*
   phpCache v1.4 - PHP caching engine 
   Copyright (C) 2001 Nathan <nathan@0x00.org> 

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/* This script should be run periodically via a cron job or by hand.  phpCache *WILL* try to clean up the cache on its own, but it does not do a full pass of the entire cache structure like this does.  Running this once a day is recommended.  */

	set_time_limit(0);
	include("phpCache.inc");
	$ret=cache_gc();
	print "cache_gc(): $ret\n";
?>
