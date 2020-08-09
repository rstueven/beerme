<?php
// https://phpbestpractices.org/#mysql
try {
    $dbConnection = new PDO("mysql:host=localhost:3306;dbname=beerme_db;charset=utf8",
        'beerme_db',
        'kE19s#t5',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        )
    );
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}