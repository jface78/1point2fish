<?php
#!/usr/bin/php -q
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../../fish_credentials.php');
include ('phpQuery.php');

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

function sendErrorMessage($library, $url, $path) {
  $headers = 'From: noreply@1point2.fish' . "\r\n" .
      'Reply-To: noreply@1point2.fish' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
  $subject = 'ERROR scraping 1.2Fish page';
  $message = '1.2Fish failed to extract a version number from the following library:' . "\n\n";
  $message .= 'Name: ' . $library . "\n";
  $message .= 'URL: ' . $url . "\n";
  $message .= 'DOM Path: ' . $path . "\n";
  mail(MY_EMAIL_ADDRESS, $subject, $message, $headers);
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

  $headers = 'From: 1point2.fish' . "\r\n" .
      'Reply-To: noreply@1point2.fish' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
  $headers .= "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $subject = 'code library updates from 1point2.fish';
  $message = 'Hello,' . "\n\n";
  $message .= 'You are receiving this email because you wanted to be notified when ' .
              'certain code libraries released updates. The following packages have ' .
              'released new versions:' . "<br><br>";
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
      $message .= $library_info['name'] . ', version ' . $library_info['currentVersion'] . ' released.' . "<br>";
      $message .= 'Download here: ' . $library_info['url'] . "<br><br>";
    }
    $query = 'SELECT email FROM users WHERE userID=:userID AND active=:active';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':userID' => $notify_users[$z], ':active' => '1']);
    $email = $sth -> fetch()[0];
    $message .= "<br><br>" . 'To unsubscribe from these notifications, click <a href="http://1point2.fish/unsubscribe.php?email=' . $email . '">here.</a>';
    mail($email, $subject, $message, $headers);
  }
}

try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$query = 'SELECT libraryID, name, url, currentVersion, path FROM libraries WHERE isActive=:active';
$sth = $dbh -> prepare($query);
$sth -> execute([':active' => '1']);
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
  $doc = phpQuery::newDocumentHTML($output);
  $version = extractVersion(pq($libs[$s]['path']));
  if (empty($version)) {
    sendErrorReport($libs[$s]['name'], $libs[$s]['path'], $libs[$s]['url']);
  } else if ($version != $libs[$s]['currentVersion']) {
    storeVersionNumber($dbh, $libraryID, $version);
    array_push($lib_updates, $libraryID);
  }
  phpQuery::unloadDocuments();
}

if (count($lib_updates)) {
  mailRelevantUsers($dbh, $lib_updates);
}

$dbh = null;

?>