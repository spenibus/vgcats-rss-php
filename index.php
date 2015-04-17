<?php
/*******************************************************************************
vgcats-rss
version: 20150406-1945
spenibus.net
*******************************************************************************/

error_reporting(!E_ALL);
mb_internal_encoding('utf-8');

$CFG_TIME = time();

$CFG_HOST        = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
$CFG_SELF        = $_SERVER['SCRIPT_NAME'];
$CFG_REQUEST_URI = $_SERVER['REQUEST_URI'];

$CFG_HOST_SELF        = $CFG_HOST.$CFG_SELF;
$CFG_HOST_REQUEST_URI = $CFG_HOST.$CFG_REQUEST_URI;

$CFG_URL_SOURCE = 'http://www.vgcats.com/comics/';
$CFG_URL_STRIP  = $CFG_URL_SOURCE.'?strip_id=';

$CFG_CACHE_FILE = './cache/items.xml';




/******************************************************************************/
function hsc($str='') {
   return htmlspecialchars($str);
}




/********************************************************************** build */


// renew cache
if(
   // no cache
   !file_exists($CFG_CACHE_FILE)
   // empty cache
   || filesize($CFG_CACHE_FILE) == 0
   // old cache
   || $CFG_TIME - filemtime($CFG_CACHE_FILE) > 3600
) {

   $src = file_get_contents($CFG_URL_SOURCE);

   preg_match('/\?strip_id=(\d+)/si', $src, $m);
   $last = $m[1] + 1;

   $items = '';

   for($i=0; $i<30; ++$i) {

      $id = $last - $i;

      if($id < 0) {
         break;
      }

      $items .= '
      <item>
         <title>vgcats '.$id.'</title>
         <link>'.hsc($CFG_URL_STRIP.$id).'</link>
      </item>';
   }

   file_put_contents($CFG_CACHE_FILE, $items);
}


// serve cache
header('content-type: application/xml');

$items = file_get_contents($CFG_CACHE_FILE);

exit('<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
   <channel>
      <title>vgcats</title>
      <description>vgcats unofficial rss feed</description>
      <pubDate>'.gmdate(DATE_RSS).'</pubDate>
      <link>'.hsc($CFG_HOST_REQUEST_URI).'</link>'.
      $items.'
   </channel>
</rss>');
?>