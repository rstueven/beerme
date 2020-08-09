<?php
header("Content-type: application/json");

require_once '../inc/beerme.php';
require_once '../db/dbconnect.php';

$breweries = [];

$select = "SELECT b._id, trim(replace(b.name, '\\r\\n', ' ')) AS name, trim(replace(b.address1, '\\r\\n', ' ')) AS address, trim(replace(b.city, '\\r\\n', ' ')) AS city, trim(replace(a.nom, '\\r\\n', ' ')) AS state, trim(replace(c.nom, '\\r\\n', ' ')) AS country, b.latitude, b.longitude, pow(2,find_in_set(b.status, 'Open,Planned,No Longer Brewing,Closed,Deleted') - 1) AS status, trim(replace(b.hours, '\\r\\n', ' ')) AS hours, CONCAT(trim(replace(c.telcode, '\\r\\n', ' ')), trim(replace(b.phone, '\\r\\n', ' '))) AS phone, b.public, b.bar, b.beergarden, b.food, b.giftshop, b.hotel, b.internet, b.retail, b.tours, b.dateupdated";
$select .= " FROM country c INNER JOIN administrativearea a ON c.country = a.country INNER JOIN brewery b ON a._id = b.region";
$select .= " WHERE status != 'Deleted'";
$select .= " ORDER BY b._id DESC";

try {
    $handle = $dbConnection->prepare($select);
    $handle->execute();
    while($row = $handle->fetch(PDO::FETCH_OBJ)) {
        $brewery = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($row->longitude),
                    floatval($row->latitude)
                ]
            ],
            'properties' => [
                'id' => $row->_id,
                'name' => $row->name,
                'address' => address($row->address, $row->city, $row->state, $row->country),
                'status' => $row->status,
                'hours' => $row->hours,
                'phone' => $row->phone
            ]
        ];

        array_push($breweries, $brewery);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$geoJson = [
    "type" => "FeatureCollection",
    "features" => $breweries
];

print json_encode($geoJson);

require_once '../db/dbdisconnect.php';