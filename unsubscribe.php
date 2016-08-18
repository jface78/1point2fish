<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../fish_credentials.php');
try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
if (!isset($_GET['email']) || empty($_GET['email'])) {
  http_response_code(400);
  exit();
}
$query = 'SELECT userID FROM users WHERE email=:email';
$sth = $dbh -> prepare($query);
$sth -> execute([':email' => $_GET['email']]);

$message = "Unable to locate your account.";
if ($sth -> rowCount() > 0) {
  $userID = $sth -> fetch()[0];
  $query = 'DELETE FROM user_libraries WHERE userID=:id';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':id' => $userID]);
  $query = 'DELETE FROM users WHERE userID=:id';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':id' => $userID]);
  
  $message = "Your account has been deactivated.<br><br>";
}
?>

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author" content="1.2.Fish">
    <meta name="Description" content="Web tools to keep developers up-to-date with the latest software library releases.">
    <meta name="Keywords" content="software libraries, version, versioning, tracker, releases, notifier, notifications">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <title>1.2 Fish - Red Fish, Blue Fish</title>

    <link rel="apple-touch-icon" sizes="57x57" href="img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script type="text/javascript">
      setTimeout(function() {
        window.location.href = 'http://1point2.fish';
      }, 30000);
    </script>
  </head>
  <body>
    <div>
      <img src="img/logo_large.png" alt="1.2 Fish" title="1.2 Fish">
    </div>
    <div style="margin-top:20px;">
      <h2>1Point2.Fish</h2>
      software version tracking<br><br>
    </div>
    <div style="padding:20px;">
      <?php echo $message;?>
    </div>
  </body>
</html>