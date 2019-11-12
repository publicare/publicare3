<?php
$QUICKCACHE_VERSION 	="v2.1.1rc1";
$includedir     		= _dirDefault."/quickcache";
$QUICKCACHE_DIR 		= _dirCache;

$QUICKCACHE_TYPE = 'file';  /* means this is a 'file' type cache */

$QUICKCACHE_TIME         =   _tempoCache; //900; // Default number of seconds to cache a page
$QUICKCACHE_DEBUG        =   0;   // Turn debugging on/off
$QUICKCACHE_IGNORE_DOMAIN=   0;   // Ignore domain name in request(single site)
$QUICKCACHE_ON           =   1;   // Turn caching on/off
$QUICKCACHE_USE_GZIP     =   0;   // Whether or not to use GZIP
$QUICKCACHE_POST         =   0;   // Should POST's be cached (default OFF)
$QUICKCACHE_GC           =   1;   // Probability % of garbage collection
$QUICKCACHE_GZIP_LEVEL   =   9;   // GZIPcompressionlevel to use (1=low,9=high)
$QUICKCACHE_CLEANKEYS    =   0;   // Set to 1 to avoid hashing storage-key:
                               // you can easily see cachefile-origin.

$QUICKCACHE_FILEPREFIX = _prefixo_cache; //'qcc-';
                         // Prefix used in the filename. This enables
                         // QuickCache to (more accurately) recognize
                         // quickcache files.

if ( isCGI() ) {
  $QUICKCACHE_ISCGI = 1;    // CGI-PHP is running
} else {
  $QUICKCACHE_ISCGI = 0;    // PHP is running as module - definitely not CGI
}

// Standard functions
require $includedir . "/quickcache_main.php";

// Type specific implementations
require $includedir . "/type/" . $QUICKCACHE_TYPE . ".php";

// Start caching
quickcache_start();

/* function to determine if PHP is loaded as a CGI-PHP or as an Apache module */
function isCGI() {
  if (substr(php_sapi_name(), 0, 3) == 'cgi') {
    return true;
  } else {
    return false;
  }
}

?>
