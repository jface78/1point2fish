<?php
#!/usr/bin/php -q
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../../fish_credentials.php');

function extractVersion($scriptTag) {
  if (preg_match('/\d+(\.\d+)+/', $scriptTag, $matches)) { 
    return $matches[0];
  } else if (preg_match('/r\d+/', $scriptTag, $matches)) { 
    return $matches[0];
  } else {
    return false;
  }
}

function storeVersionNumber($dbh, $libID, $version) {
  $query = 'UPDATE libraries SET currentVersion=:version, lastUpdated=now() WHERE libraryID=:libID';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':version' => $version, ':libID' => $libID]);
}

try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$query = 'SELECT userID, libraryID FROM user_libraries';
$sth = $dbh -> prepare($query);
$sth -> execute();
$libsPerUser = $sth -> fetchAll(PDO::FETCH_ASSOC);
for ($i=0; $i < count($libsPerUser); $i++) {
  $userID = $libsPerUser[$i]['userID'];
  $libraryID = $libsPerUser[$i]['libraryID'];
  $query = 'SELECT name, url, currentVersion FROM libraries WHERE libraryID=:lib';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':lib' => $libraryID]);
  $libs = $sth -> fetchAll(PDO::FETCH_ASSOC);
  
  for ($s=0; $s < count($libs); $s++) {
    $url = $libs[$s]['url'];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
    curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    //disable when live
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    //$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $output = curl_exec($curl);
    curl_close($curl);
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->preserveWhiteSpace = false;
    $dom->validateOnParse = true;
    $dom->loadHTML($output);
    $items = $dom->getElementsByTagName('h3');

    for ($z=0; $z < $items->length; $z++) {
      if (strtolower($items->item($z)->nodeValue) == strtolower($libs[$s]['name'])) {
        //echo $items->item($z)->nodeValue . ': ';
        $parent = $items->item($z)->nextSibling;
        while ($parent -> nodeName == '#text') {
          $parent = $parent->nextSibling;
        }
        $version = extractVersion($parent -> getElementsByTagName('code')[0] -> nodeValue);
        if ($version != $libs[$s]['currentVersion']) {
          storeVersionNumber($dbh, $libraryID, $version);
        }
        //echo $version . '<br>';
      }
    }
  }
}

$dbh = null;

?>