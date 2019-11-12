<?php
/*~ file.php (QuickCache "file" type of cache
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
 * could/should use it too, please submit it to (andy.prevost@worxteam.com),
 * and we will include it in a next release (with full credits, ofcourse).
 */

/* quickcache_restore()
 * Will (try to) restore the cachedata.
 */
function quickcache_restore() {
  // Construct filename
  $filename = $GLOBALS["QUICKCACHE_DIR"]."/".$GLOBALS["QUICKCACHE_FILEPREFIX"].$GLOBALS["quickcache_key"];

  // read file and unserialize the data
  $cachedata=unserialize(quickcache_fileread($filename));
  if (is_array($cachedata)) {
    // Only read cachefiles of my version
    if ($cachedata["quickcache_version"] == $GLOBALS["QUICKCACHE_VERSION"]) {
      if (($cachedata["quickcache_expire"] == "0") ||
        ($cachedata["quickcache_expire"] >= time()))
      {
        //Restore data
        $GLOBALS["quickcachedata_gzdata"]   = $cachedata["quickcachedata_gzdata"];
        $GLOBALS["quickcachedata_datasize"] = $cachedata["quickcachedata_datasize"];
        $GLOBALS["quickcachedata_datacrc"]  = $cachedata["quickcachedata_datacrc"];
        return TRUE;
      } else {
        quickcache_debug("Data in cachefile $filename has expired");
      }
    } else {
      // Invalid version of cache-file
      quickcache_debug("Invalid version of cache-file $filename");
    }
  } else {
    // Invalid cache-file
    quickcache_debug("Invalid content of cache-file $filename");
  }

  return FALSE;
}

/* quickcache_write()
 * Will (try to) write out the cachedata to the db
 */
function quickcache_write($gzdata, $datasize, $datacrc) {
  // Construct filename
  $filename = $GLOBALS["QUICKCACHE_DIR"]."/".$GLOBALS["QUICKCACHE_FILEPREFIX"].$GLOBALS["quickcache_key"];

  // Create and fill cachedata-array
  $cachedata = array();
  $cachedata["quickcache_version"] = $GLOBALS["QUICKCACHE_VERSION"];
  $cachedata["quickcache_expire"] = ($GLOBALS["QUICKCACHE_TIME"] > 0) ?
                                      time() + $GLOBALS["QUICKCACHE_TIME"] :
                                      0;
  $cachedata["quickcachedata_gzdata"] = $gzdata;
  $cachedata["quickcachedata_datasize"] = $datasize;
  $cachedata["quickcachedata_datacrc"] = $datacrc;

  // And write the data
  if (quickcache_filewrite($filename, serialize($cachedata))) {
    quickcache_debug("Successfully wrote cachefile $filename");
  } else {
    quickcache_debug("Unable to write cachefile $filename");
  }
}

/* quickcache_do_gc()
 * Performs the actual garbagecollection
 */
function quickcache_do_gc() {
  $dp=opendir($GLOBALS["QUICKCACHE_DIR"]);

  // Can we access directory ?
  if (!$dp)
  {
      quickcache_debug("Error opening ". $GLOBALS["QUICKCACHE_DIR"] ." for garbage-collection");
  }

  while (!(($de=readdir($dp))===FALSE))
  {
    // To get around strange php-strpos, add additional char
    // Only read quickcache-files.
    if (strpos("x$de", $GLOBALS["QUICKCACHE_FILEPREFIX"])==1) {
      $filename=$GLOBALS["QUICKCACHE_DIR"] . "/" . $de;
      // read file and unserializes the data
      $cachedata=unserialize(quickcache_fileread($filename));

      // Check data in array.
      if (is_array($cachedata)) {
        if ($cachedata["quickcache_expire"]!="0" && $cachedata["quickcache_expire"]<=time()) {
          // Unlink file, we do not need to get a lock
          $deleted = @unlink($filename);
          if ($deleted) {
              quickcache_debug("Successfully unlinked $filename");
          } else {
            quickcache_debug("Failed to unlink $filename");
          }
        }
      }
    }
  }
}

/* quickcache_do_start()
 * Additional code that is executed before real quickcache-code kicks in
 */
function quickcache_do_start() {
  // Add additional code you might require
}

/* quickcache_do_end()
 * Additional code that is executed after caching has been performed,
 * but just before output is returned. No new output can be added!
 */
function quickcache_do_end() {
  // Add additional code you might require
}

/* This internal function reads in the cache-file */
function quickcache_fileread($filename) {
  // php.net suggested I should use rb to make it work under Windows
  $fp=@fopen($filename, "rb");
  if (!$fp) {
    quickcache_debug("Failed to open for read of $filename");
    return NULL;
  }

  // Get a shared lock
  flock($fp, LOCK_SH);

  $buff="";
  // Be gentle, so read in 4k blocks
  while (($tmp=fread($fp, 4096))) {
    $buff.=$tmp;
  }

  // Release lock
  flock($fp, LOCK_UN);
  fclose($fp);
  // Return
  return $buff;
}

/* This internal function writes the cache-file */
function quickcache_filewrite($filename, $data) {
  $return = FALSE;
  // Lock file, ignore warnings as we might be creating this file
  $fpt = @fopen($filename, "rb");
  @flock($fpt, LOCK_EX);

  // php.net suggested I should use wb to make it work under Windows
  $fp=@fopen($filename, "wb+");
  if (!$fp) {
    // Strange! We are not able to write the file!
    quickcache_debug("Failed to open for write of $filename");
  } else {
    fwrite($fp, $data, strlen($data));
    fclose($fp);
    $return = TRUE;
  }

  // Release lock
  @flock($fpt, LOCK_UN);
  @fclose($fpt);
  // Return
  return $return;
}

// Make sure no additional lines/characters are after the closing-tag!
?>
