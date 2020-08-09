<!doctype html>
<html lang="en">
<head>
    <title>Beer Me! With a Map!</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/beerme.css" type="text/css">
    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/ol-geocoder@latest/dist/ol-geocoder.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/ol-geocoder"></script>
    <script src="js/map.js" type="text/javascript"></script>
</head>
<body>
<?php
  require_once 'inc/beerme.php';
  require_once 'db/dbconnect.php';
?>

<!-- https://bootstrap-menu.com/detail-multilevel.html -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="/">Beer Me!</a>
    <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main_nav">

        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="#"> Latest Brewery Updates </a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"> Beers </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"> Beer List </a></li>
                    <li><a class="dropdown-item" href="#"> Hall of Fame </a></li>
                    <li><a class="dropdown-item" href="#"> "What's the Best Brewery?" </a>
                    <li><a class="dropdown-item" href="#"> Beermats, Labels, &amp; Pictures </a>
                </ul>
            </li>
        </ul>
    </div> <!-- navbar-collapse.// -->
</nav>

<main role="main" class="container">
    <div class="row">
        <!-- TODO: My location -->
        <!-- TODO: Zoom controls -->
        <div id="map"></div>
        <!-- TODO: Styling -->
        <div id="popup" class="ol-popup"></div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <h1>New Breweries</h1>
            <ul>
              <?php
                // TODO: Scrollable list, load new items at scroll end
                $s = "SELECT MAX(dateEntered) FROM brewery";
                $result = $dbConnection->query($s);
                $r = $result->fetch(PDO::FETCH_NUM);
                $date = $r[0];
                $result->closeCursor();

                $s = "SELECT b._id, b.name, b.city, b.dateEntered, a.nom AS state, c.nom AS country FROM brewery b INNER JOIN administrativearea a ON b.region=a._id INNER JOIN country c ON a.country=c.country WHERE b.status != 'Deleted' AND b.dateEntered = '{$date}' ORDER BY b.dateEntered DESC";
                $result = $dbConnection->query($s);
                while ($r = $result->fetch(PDO::FETCH_OBJ)) {
                  echo "<li><a href='/brewery.php?{$r->_id}'>{$r->name}</a><br>{$r->city}, ";
                  if ($r->state != "") echo "{$r->state}, ";
                  echo "{$r->country}</li>\n";
                }
                $result->closeCursor();
              ?>
            </ul>

        </div>
        <div class="col-12 col-md-6">
            <h1>Brewery Updates</h1>
            <ul>
              <?php
                // TODO: Scrollable list, load new items at scroll end
                $s = "SELECT MAX(dateUpdated) FROM brewery";
                $result = $dbConnection->query($s);
                $r = $result->fetch(PDO::FETCH_NUM);
                $lastDate = $r[0];
                $result->closeCursor();

                $s = "SELECT brewery._id AS breweryid, brewery.name AS name, brewery.city AS city, administrativearea.nom AS statename, country.nom AS countryname, DATE_FORMAT(brewery.dateupdated, '%M %e, %Y') AS updated, DATE_FORMAT(brewery.dateentered, '%M %e, %Y') AS entered, brewery.status AS status FROM brewery, administrativearea, country WHERE brewery.status != 'Deleted' AND brewery.region=administrativearea._id AND administrativearea.country=country.country AND brewery.dateupdated >= DATE_ADD('{$lastDate}', INTERVAL -1 DAY) ORDER BY brewery.dateupdated DESC, brewery.name";
                $result = $dbConnection->query($s);
                while ($r = $result->fetch(PDO::FETCH_OBJ)) {
                  if ($lastDate != $r->updated) {
                    $lastDate = $r->updated;
                    echo "</ul>\n";
                    echo "<h2><i>{$lastDate}</i></h2>\n";
                    echo "<ul>\n";
                  }
                  echo "<li>";
                  if ($r->entered == $r->updated) echo "Added ";
                  else if ($r->status == "Deleted") echo "Deleted ";
                  else echo "Updated ";
                  echo "<a href='/brewery.php?{$r->breweryid}'>{$r->name}</a>, {$r->city}, ";
                  if (!is_null($r->statename)) echo "{$r->statename}, ";
                  echo "{$r->countryname}";
                  if ($r->status != "Open" && $r->status != "Deleted") echo " ({$r->status})";
                  echo "</li>\n";
                }
                $result->closeCursor();
              ?>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <h1>Latest Tasting Notes</h1>
            <ul>
              <?php
                // TODO: Scrollable list, load new items at scroll end
                $s = "SELECT b._id AS breweryid, b.nameshort AS breweryname, c._id AS beerid, c.name AS beername, d._id AS pagenumber, d.sampled AS sampled FROM brewery b INNER JOIN beer c on b._id=c.breweryid INNER JOIN beerdetail d on c._id=d.beerid ORDER BY d._id DESC LIMIT 20";
                $result = $dbConnection->query($s);
                while ($r = $result->fetch(PDO::FETCH_OBJ)) {
                  echo "<li>#{$r->pagenumber}: <a href='/brewery.php?{$r->breweryid}#{$r->beerid}'>{$r->breweryname} {$r->beername}</a></li>\n";
                }
                $result->closeCursor();
              ?>
            </ul>
        </div>
        <div class="col-12 col-md-6">
            <h1>Brewery News</h1>
            <ul>
              <?php
                // TODO: Scrollable list, load new items at scroll end
                $s = "SELECT source, url, DATE_FORMAT(newsdate, '%M %d, %Y') AS newsdate, title FROM news ORDER BY newsdate DESC LIMIT 10";
                $result = $dbConnection->query($s);
                while ($r = $result->fetch(PDO::FETCH_OBJ)) {
                  echo "<li><a href='{$r->url}'>{$r->title}</a><br/>({$r->source} &mdash; {$r->newsdate})</li>\n";
                }
                $result->closeCursor();
              ?>
            </ul>
        </div>
    </div>
</main>
</body>
</html>