<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../../fish_credentials.php');

try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$query = 'SELECT libraryID, name, url, currentVersion FROM libraries WHERE isActive=:active ORDER BY libraryID ASC';
$sth = $dbh -> prepare($query);
$sth -> execute([':active' => '1']);
$libs = $sth -> fetchAll(PDO::FETCH_ASSOC);

$dbh = null;
header("Content-Type: application/json");
echo json_encode($libs);
http_response_code(200);
?>