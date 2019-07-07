<?php
header("Content-type: application/json");

require_once 'beerme.php';

require_once '../inc/dbconnect.php';
// require_once '../inc/sqlInjectionDefense.php';

$t = "1970-01-01";

if ($argc == 2) {
  $_GET["t"] = $argv[1];
}

$getKeys = array_keys($_GET);
foreach ($getKeys as $k => $v) {
  // echo "{$k}:{$v}:{$_GET[$v]}<br/>";
  switch ($v) {
    case "t":
      $t = $_GET[$v];
      break;
  }
}

$select = "SELECT b._id, trim(replace(b.name, '\\r\\n', ' ')), trim(replace(b.address1, '\\r\\n', ' ')), trim(replace(b.city, '\\r\\n', ' ')), trim(replace(a.nom, '\\r\\n', ' ')), trim(replace(c.nom, '\\r\\n', ' ')), b.latitude, b.longitude, pow(2,find_in_set(b.status, 'Open,Planned,No Longer Brewing,Closed,Deleted') - 1), trim(replace(b.hours, '\\r\\n', ' ')), trim(replace(c.telcode, '\\r\\n', ' ')), trim(replace(b.phone, '\\r\\n', ' ')), b.public, b.bar, b.beergarden, b.food, b.giftshop, b.hotel, b.internet, b.retail, b.tours, b.dateupdated";

$select .= " FROM country c INNER JOIN administrativearea a ON c.country = a.country INNER JOIN brewery b ON a._id = b.region";

$select .= " WHERE status != 'Deleted'";

$select .= " AND dateupdated > '{$t}'";

$select .= " ORDER BY b._id DESC";

$query = $db->query($select) or die(mysql_error() . "<br/>{$select}");

$breweries = [];

while ($record = $query->fetch_array()) {
  $brewery = [];
  list ($breweryid, $name, $address1, $city, $state, $country, $latitude, $longitude, $status, $hours, $telcode, $phone, $public, $bar, $beergarden, $food, $giftshop, $hotel, $internet, $retail, $tours, $updated) = $record;

  $brewery["id"] = $breweryid;
  $brewery["name"] = $name;
  $brewery["address"] = address($address1, $city, $state, $country);
  $brewery["latitude"] = $latitude;
  $brewery["longitude"] = $longitude;
  $brewery["status"] = $status;

  $svc = 0;
  if ($public)
    $svc += OPEN;
  if ($bar)
    $svc += BAR;
  if ($beergarden)
    $svc += BEERGARDEN;
  if ($food)
    $svc += FOOD;
  if ($giftshop)
    $svc += GIFTSHOP;
  if ($hotel)
    $svc += HOTEL;
  if ($internet)
    $svc += INTERNET;
  if ($retail)
    $svc += RETAIL;
  if ($tours)
    $svc += TOURS;

  $brewery["services"] = $svc;

  $brewery["updated"] = $updated;

  if (! is_null($phone))
    $brewery["phone"] = "+{$telcode} {$phone}";

  if (! is_null($hours))
    $brewery["hours"] = $hours;

  $webSelect = "SELECT trim(replace(url, '\\r\\n', ' ')) FROM web WHERE breweryid = {$breweryid} AND url NOT LIKE '%facebook.com%' LIMIT 1";
  $webQuery = $db->query($webSelect) or die(mysql_error() . "<br/>{$webSelect}");
  if ($webQuery->num_rows == 1) {
    $webRecord = $webQuery->fetch_array();
    $brewery["url"] = $webRecord[0];
  }

  $webQuery->free_result();

  $docroot = "/var/www/vhosts/beerme.com/httpdocs";
  // $docroot = "/u/httpd/docs/beerme";
  $beermatBase = $docroot . "/graphics/brewery/" . floor($breweryid / 1000) . "/{$breweryid}";
  $beermatPNG = "{$beermatBase}/premises.png";
  $beermatFile = "";
  if (is_file($beermatPNG)) {
    $beermatFile = $beermatPNG;
  }
  if ($beermatFile != "") {
    $beermatFile = str_replace($docroot, "https://beerme.com", $beermatFile);
    $brewery["graphics"] = $beermatFile;
  }

  $breweries[] = $brewery;
}

$query->free_result();
require_once '../inc/dbdisconnect.php';

echo json_encode($breweries);
?>