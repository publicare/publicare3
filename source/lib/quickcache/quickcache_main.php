<?php
/*~ quickcache_main.php (QuickCache main file)
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

/* Take a wild guess... */
function quickcache_debug($s) {
  static $quickcache_debugline;

  if ($GLOBALS["QUICKCACHE_DEBUG"]) {
    $quickcache_debugline++;
    header("X-CacheDebug-$quickcache_debugline: $s");
  }
}

/* quickcache_key()
 * Returns a hashvalue for the current. Maybe md5 is too heavy,
 * so you can implement your own hashing-function.
 */
function quickcache_key() {
  if ($GLOBALS["QUICKCACHE_CLEANKEYS"]) {
    $key = eregi_replace("[^A-Z,0-9,=]", "_", quickcache_scriptkey());
    $key .= ".".eregi_replace("[^A-Z,0-9,=]", "_", quickcache_varkey());
    if (strlen($key) > 255) {
      // Too large, fallback to md5!
      $key = md5(quickcache_scriptkey().quickcache_varkey());
    }
  } else {
      $key = md5(quickcache_scriptkey().quickcache_varkey());
  }
  quickcache_debug("Cachekey is set to $key");
  return $key;
}

/* quickcache_varkey()
 * Returns a serialized version of POST & GET vars
 * If you want to take cookies into account in the varkey too,
 * add them inhere.
 */
function quickcache_varkey() {
  $varkey = "";
  if ($GLOBALS["QUICKCACHE_POST"]) {
    $varkey = "POST=".serialize($_POST);
  }
  $varkey .= "GET=".serialize($_GET);
  quickcache_debug("Cache varkey is set to $varkey");
  return $varkey;
}

/* quickcache_scriptkey()
 * Returns the script-identifier for the request
 */
function quickcache_scriptkey() {
  // These should be available, unless running commandline
  if ($GLOBALS["QUICKCACHE_IGNORE_DOMAIN"]) {
    $name = $_SERVER["REQUEST_URI"]; //$_SERVER["PHP_SELF"];
  } else {
    $name = $_SERVER["SERVER_NAME"]."/".$_SERVER["REQUEST_URI"];
  }

  // Commandline mode will also fail this one, I'm afraid, as there is no
  // way to determine the scriptname
  if ($name=="") {
    $name="http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
  }
  
  quickcache_debug("Cache scriptkey is set to $name");
  return $name;
}


/* quickcache_check() */
function quickcache_check() {
  if (!$GLOBALS["QUICKCACHE_ON"]) {
    quickcache_debug("Cache has been disabled!");
    return false;
  }

  // We need to set this global, as ob_start only calls the given method
  // with no parameters.
  $GLOBALS["quickcache_key"] = quickcache_key();

  // Can we read the cached data for this key ?
  if (quickcache_restore()) {
    quickcache_debug("Cachedata for ".$GLOBALS["quickcache_key"]." found, data restored");
    return true;
  } else {
    // No cache data (yet) or unable to read
    quickcache_debug("No (valid) cachedata for ".$GLOBALS["quickcache_key"]);
    return false;
  }
}

/* quickcache_encoding()
 * Are we capable of receiving gzipped data ?
 * Returns the encoding that is accepted. Maybe additional check for Mac ?
 */
function quickcache_encoding() {
  if (headers_sent() || connection_aborted()) {
    return false;
  }
  if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],'x-gzip') !== false) {
    return "x-gzip";
  }
  if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],'gzip') !== false) {
    return "gzip";
  }
  return false;
}

/* quickcache_init()
 * Checks some global variables and might decide to disable caching
 */
function quickcache_init() {
  // Override default QUICKCACHE_TIME ?
  if (isset($GLOBALS["cachetimeout"])) {
    $GLOBALS["QUICKCACHE_TIME"]=$GLOBALS["cachetimeout"];
  }

  // Force gzip off if gzcompress does not exist
  if (!function_exists('gzcompress')) {
    $GLOBALS["QUICKCACHE_USE_GZIP"]  = 0;
  }

  // Force cache off when POST occured when you don't want it cached
  if (!$GLOBALS["QUICKCACHE_POST"] && (count($_POST) > 0)) {
    $GLOBALS["QUICKCACHE_ON"] = 0;
    $GLOBALS["QUICKCACHE_TIME"] = -1;
  }

  // A cachetimeout of -1 disables writing, only ETag and content encoding
  if ($GLOBALS["QUICKCACHE_TIME"] == -1) {
    $GLOBALS["QUICKCACHE_ON"] = 0;
  }

  // Output header to recognize version
  header("X-Cache: QuickCache v".$GLOBALS["QUICKCACHE_VERSION"].
          " - ".$GLOBALS["QUICKCACHE_TYPE"]);
}

/* quickcache_gc()
 * Checks if garbagecollection is needed.
 */
function quickcache_gc() {
  // Should we garbage collect ?
  if ($GLOBALS["QUICKCACHE_GC"]>0) {
    mt_srand(time(NULL));
    $precision=100000;
    // Garbagecollection probability
    if (((mt_rand()%$precision)/$precision) <=
        ($GLOBALS["QUICKCACHE_GC"]/100))
    {
      quickcache_debug("GarbageCollection hit!");
      quickcache_do_gc();
    }
  }
}

/* quickcache_start()
 * Sets the handler for callback
 */
function quickcache_start() {
  // Initialize cache
  quickcache_init();

  // Handle type-specific additional code if required
  quickcache_do_start();

  // Check cache
  if (quickcache_check()) {
    // Cache is valid and restored: flush it!
    print quickcache_flush($GLOBALS["quickcachedata_gzdata"],
                        $GLOBALS["quickcachedata_datasize"],
                        $GLOBALS["quickcachedata_datacrc"]);
    // Handle type-specific additional code if required
    quickcache_do_end();
    exit;
  } else {
    // if we came here, cache is invalid: go generate page
    // and wait for quickcache_end() which will be called automagically

    // Check garbagecollection
    quickcache_gc();

    // Go generate page and wait for callback
    ob_start("quickcache_end");
    ob_implicit_flush(0);
  }
}

/* quickcache_end()
 * This one is called by the callback-funtion of the ob_start.
 */
function quickcache_end($contents) {
  quickcache_debug("Callback happened");

  $datasize = strlen($contents);
  $datacrc = crc32($contents);

  if ($GLOBALS["QUICKCACHE_USE_GZIP"]) {
    $gzdata = gzcompress($contents, $GLOBALS["QUICKCACHE_GZIP_LEVEL"]);
  } else {
    $gzdata = $contents;
  }

  // If the connection was aborted, do not write the cache.
  // We don't know if the data we have is valid, as the user
  // has interupted the generation of the page.
  // Also check if quickcache is not disabled
  if ((!connection_aborted()) &&
       $GLOBALS["QUICKCACHE_ON"] &&
      ($GLOBALS["QUICKCACHE_TIME"] >= 0))
  {
    quickcache_debug("Writing cached data to storage");
    // write the cache with the current data
    quickcache_write($gzdata, $datasize, $datacrc);
  }

  // Handle type-specific additional code if required
  quickcache_do_end();

  // Return flushed data
  return quickcache_flush($gzdata, $datasize, $datacrc);
}

/* quickcache_flush()
 * Responsible for final flushing everything.
 * Sets ETag-headers and returns "Not modified" when possible
 * When ETag doesn't match (or is invalid), it is tried to send
 * the gzipped data. If that is also not possible, we sadly have to
 * uncompress (assuming QUICKCACHE_USE_GZIP is on)
 */
function quickcache_flush($gzdata, $datasize, $datacrc) {
  // First check if we can send last-modified
  $myETag = "\"qcd-$datacrc.$datasize\"";
  header("ETag: $myETag");
  $foundETag = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? stripslashes($_SERVER["HTTP_IF_NONE_MATCH"]) : "";
  $ret = NULL;

  if (strstr($foundETag, $myETag)) {
    // Not modified!
    if(stristr($_SERVER["SERVER_SOFTWARE"], "microsoft")) {
      // IIS has already sent a HTTP/1.1 200 by this stage for
      // some strange reason
      header("Status: 304 Not Modified");
    } else {
      if ( $QUICKCACHE_ISCGI ) {
        header('Status: 304 Not Modified');
      } else {
        header('HTTP/1.0 304');
      }
    }
  } else {
    // Are we gzipping ?
    if ($GLOBALS["QUICKCACHE_USE_GZIP"]) {
      $ENCODING = quickcache_encoding();
      if ($ENCODING) {
        // compressed output: set header. Need to modify, as
        // in some versions, the gzipped content is not what
        // your browser expects.
        header("Content-Encoding: $ENCODING");
        $ret =  "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $ret .= substr($gzdata, 0, strlen($gzdata) - 4);
        $ret .= pack('V',$datacrc);
        $ret .= pack('V',$datasize);
      } else {
        // Darn, we need to uncompress :(
        $ret = gzuncompress($gzdata);
      }
    } else {
      // So content isn't gzipped either
      $ret=$gzdata;
    }
  }
  return $ret;
}

?>
