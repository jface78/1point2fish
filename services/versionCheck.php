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

function mailRelevantUsers($dbh, $lib_updates) {
  $notify_users = [];
  for ($z=0; $z<count($lib_updates); $z++) {
    $query = 'SELECT userID FROM user_libraries WHERE libraryID=:libID';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':libID' => $lib_updates[$z]]);
    $userID = $sth -> fetch()[0];
    if ($sth -> rowCount() > 0 && !in_array($userID, $notify_users)) {
      array_push($notify_users, $userID);;
    }
  }

  $headers = 'From: noreply@1point2.fish' . "\r\n" .
      'Reply-To: noreply@1point2.fish' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
  $subject = 'code library updates from 1point2.fish';
  $message = 'Hello,' . "\n\n";
  $message .= 'You are receiving this email because you wanted to be notified when ' .
              'certain code libraries released updates. The following packages have ' .
              'released new versions:' . "\n\n";
  for ($z=0; $z<count($notify_users);$z++) {
    $query = 'SELECT libraryID FROM user_libraries WHERE userID=:userID';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':userID' => $notify_users[$z]]);
    $libs = $sth -> fetchAll(PDO::FETCH_ASSOC);
    for ($i=0; $i<count($libs);$i++) {
      $query = 'SELECT name, url, currentVersion FROM libraries WHERE libraryID=:libID';
      $sth = $dbh -> prepare($query);
      $sth -> execute([':libID' => $libs[$i]['libraryID']]);
      $library_info = $sth -> fetch(PDO::FETCH_ASSOC);
      $message .= $library_info['name'] . ', version ' . $library_info['currentVersion'] . ' released.' . "\n";
      $message .= 'Download here: ' . $library_info['url'] . "\n\n";
    }
    $query = 'SELECT email FROM users WHERE userID=:userID';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':userID' => $notify_users[$z]]);
    $email = $sth -> fetch()[0];
    mail($email, $subject, $message, $headers);
  }
}

try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$query = 'SELECT libraryID, name, url, currentVersion FROM libraries';
$sth = $dbh -> prepare($query);
$sth -> execute();
$libs = $sth -> fetchAll(PDO::FETCH_ASSOC);

$lib_updates = [];

for ($s=0; $s < count($libs); $s++) {
  $libraryID = $libs[$s]['libraryID'];
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
        array_push($lib_updates, $libraryID);
        //mailRelevantUsers($libraryID, $version, $libs[$s]['name'], $libs[$s]['url']);
      }
      //echo $version . '<br>';
    }
  }
}

if (count($lib_updates)) {
  mailRelevantUsers($dbh, $lib_updates);
}

$dbh = null;

?>