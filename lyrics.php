<?php
error_reporting(E_ALL);
if(isset($argc))
{
  define("CLI",true);
  if($argc != 3) die("Usage: {$argv[0]} <artist> <song>\n");
  $nl = "\n";
  $artist = $argv[1];
  $song = $argv[2];
}
else
{
  define("CLI",false);
  $nl = "<br />\n";
  $artist = $_REQUEST['artist'];
  $song = $_REQUEST['song'];
}
$song = fix(strtolower($song));
$artist = fix(strtolower($artist));
$dom = new DOMDocument();
$html = ge("http://www.azlyrics.com/lyrics/{$artist}/{$song}.html");
@$dom->loadHTML($html);
$b = $dom->getElementsByTagName('b');
$header = $b->item(0);
$div = $header->nextSibling->nextSibling->nextSibling;
$innerHTML = "";
$children  = $div->childNodes;
foreach ($children as $child)
{
  $innerHTML .= $div->ownerDocument->saveHTML($child);
}
$result = str_replace("’","'",str_replace("<br>",$nl,substr($innerHTML,9)));
if(!CLI) echo "<!DOCTYPE html>\n<html>\n<head>\n<title>" . $dom->getElementsByTagName('title')->item(0)->textContent . "</title>\n</head>\n<body>\n";
echo "Lyrics for: " . $header -> textContent . $nl;
echo $result;
if(!CLI) echo "\n</body>\n</html>";
function fix($a)
{
  return str_replace(" ","",str_replace(".","",$a));
}
function ge($url)
{
  $cdir = CLI ? $_SERVER['HOME'] . DIRECTORY_SEPARATOR . ".cache" : "cache";
  if(!is_dir($cdir)) mkdir($cdir);
  $cachename = $cdir . DIRECTORY_SEPARATOR . str_replace("/","-",$url);
  if(file_exists($cachename)) return file_get_contents($cachename);
  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_HEADER, false );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true);
  $restext = curl_exec( $ch );
  curl_close( $ch );
  file_put_contents($cachename,$restext);
  return $restext;
}
if(CLI) echo "\n";
