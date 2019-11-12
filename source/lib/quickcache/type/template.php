<?php
/*~ template.php (QuickCache "other" type of cache *** YOU NEED TO POPULATE CODE
.---------------------------------------------------------------------------.
|  Software: QuickCache                                                     |
|   Version: 2.1rc1                                                         |
|   Contact: andy.prevost@worxteam.com                                      |
|      Info: http://sourceforge.net/projects/quickcache                     |
|   Support: http://sourceforge.net/projects/quickcache                     |
| ------------------------------------------------------------------------- |
|    Author: Andy Prevost andy.prevost@worxteam.com (admin)                 |
|    Author: Jean-Pierre Deckers (original founder)                         |
| Copyright (c) 2004-2007, Andy Prevost. All Rights Reserved.               |
| Copyright (c) 2001-2003, Jean-Pierre Deckers jp@jpcache.com               |
|    * NOTE: QuickCache is the 'jpcache' project renamed. 'jpcache          |
|            information and downloads can still be accessed at the         |
|            sourceforge.net site                                           |
| ------------------------------------------------------------------------- |
|   License: Distributed under the General Public License (GPL)             |
|            http://www.gnu.org/copyleft/gpl.html                           |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
| ------------------------------------------------------------------------- |
| We offer a number of paid services:                                       |
| - Web Hosting on highly optimized fast and secure servers                 |
| - Technology Consulting                                                   |
| - Oursourcing (highly qualified programmers and graphic designers)        |
'---------------------------------------------------------------------------'
Last modified: October 31 2007 ~*/

/* Template for other types of cache
 * You'll need to implement these 5 functions, add
 * additional functions inhere.
 *
 * Add variables you use to quickcache_config.php
 *
 * When you've implemented a new storage-system, and think that the world
 * could/should use it too, please submit it to me (andy.prevost@worxteam.com),
 * and we will include it in a next release (with full credits, of course).
 */

/* quickcache_restore()
 * Will (try to) restore the cachedata.
 */
function quickcache_restore() {
  global $QUICKCACHE_TIME, $cache_key, $cachedata_gzdata, $cachedata_datasize, $cachedata_datacrc;

  // Implement restoring of cached data
  //
  // Use $cache_key to lookup data, use $JPACHE_TIME to check if
  // data is still valid.
  //
  // If data-retrieval was succesfull, you'll need to set
  // $cachedata_gzdata, $cachedata_datasize and $cachedata_datacrc
  // and return true, if unsuccesfull, return false.

  return false;
}

/* quickcache_write()
 * Will (try to) write out the cachedata to the db
 */
function quickcache_write($gzcontents, $size, $crc32) {
  global $QUICKCACHE_TIME, $QUICKCACHE_ON, $cache_key;

  // Implement writing of the data as given.
  // Store on $cache_key and add a 'field' for $QUICKCACHE_TIME
  // Store the 3 parameters seperatly
}

/* quickcache_do_gc()
 * Performs the actual garbagecollection
 */
function quickcache_do_gc() {
  // Implement garbage-collection
}


/* quickcache_do_start()
 * Additional code that is executed before real quickcache-code kicks
 */
function quickcache_do_start() {
  // Add additional code you might require
}

/* quickcache_do_end()
 * Additional code that us executed after caching has been performed,
 * but just before output is returned. No new output can be added.
 */
function quickcache_do_end() {
  // Add additional code you might require
}

// Make sure no additional lines/characters are after the closing-tag!
?>
