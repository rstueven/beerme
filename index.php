<!doctype html>
<html lang='en'></html>
<head>
  <title></title>
  <?php require_once 'inc/headers.php'; ?>
  <?php require_once 'inc/mapHeaders.php'; ?>
</head>
<body>
  <?php
  require_once 'inc/beerme.php';
  require_once 'db/dbconnect.php';
  ?>
  <main class='container' role='main'>
    <?php require_once 'inc/navbar.php'; ?>
    <div class='content'>
      <div class='row'>
        <div id='map'></div>
        <div class='ol-popup' id='popup'></div>
      </div>
      <div class='row'>
        <div class='col-12 col-md-6'>
          <h1>New Brewery Listings</h1>
          <ul>
            <?php
            $select = "SELECT b._id, b.name, b.city, b.dateEntered, a.nom AS state, c.nom AS country FROM brewery b INNER JOIN administrativearea a ON b.region=a._id INNER JOIN country c ON a.country=c.country WHERE b.status != 'Deleted' ORDER BY b.dateEntered DESC, b._id DESC LIMIT 10";
            $result = $dbConnection->query($select);
            while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            ?>
            <li>
              <?php
              echo "<a href='/brewery.php?{$row->_id}'>{$row->name}</a>";
              ?>
              <br>
              <?php
              echo "{$row->city}, ";
              if ($row->state != "") {
              echo "{$row->state}, ";
              }
              echo "{$row->country}";
              ?>
            </li>
            <?php
            }
            $result->closeCursor();
            ?>
          </ul>
          <p class='text-right'>
            <a href='/newbrewerylistings.php'>
              more new brewery listings...
            </a>
          </p>
        </div>
        <div class='col-12 col-md-6'>
          <h1>Brewery Updates</h1>
          <ul>
            <?php
            $select = "SELECT MAX(dateUpdated) FROM brewery";
            $result = $dbConnection->query($select);
            $row = $result->fetch(PDO::FETCH_NUM);
            $lastDate = $row[0];
            $result->closeCursor();
            $select = "SELECT brewery._id AS breweryid, brewery.name AS name, brewery.city AS city, administrativearea.nom AS statename, country.nom AS countryname, DATE_FORMAT(brewery.dateupdated, '%M %e, %Y') AS updated, DATE_FORMAT(brewery.dateentered, '%M %e, %Y') AS entered, brewery.status AS status FROM brewery, administrativearea, country WHERE brewery.status != 'Deleted' AND brewery.region=administrativearea._id AND administrativearea.country=country.country AND brewery.dateupdated >= DATE_ADD('{$lastDate}', INTERVAL -1 DAY) ORDER BY brewery.dateupdated DESC, brewery.name";
            $result = $dbConnection->query($select);
            while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            if ($lastDate != $row->updated) {
            $lastDate = $row->updated;
            echo "</ul>\n";
            ?>
            <h2>
              <i>
                <?php echo "{$lastDate}"; ?>
              </i>
            </h2>
            <?php
            echo "<ul>\n";
            }
            ?>
            <li>
              <?php
              if ($row->entered == $row->updated) {
              echo "Added ";
              } else {
              if ($row->status == "Deleted") {
              echo "Deleted ";
              } else {
              echo "Updated ";
              }
              }
              echo "<a href='/brewery.php?{$row->breweryid}'>{$row->name}</a>, {$row->city}, ";
              if (!is_null($row->statename)) {
              echo "{$row->statename}, ";
              }
              echo "{$row->countryname}";
              if ($row->status != "Open" && $row->status != "Deleted") {
              echo " ({$row->status})";
              }
              ?>
            </li>
            <?php
            }
            $result->closeCursor();
            ?>
          </ul>
        </div>
        <div class='col-12 col-md-6'>
          <h1>Latest Tasting Notes</h1>
          <ul>
            <?php
            $select = "SELECT b._id AS breweryid, b.nameshort AS breweryname, c._id AS beerid, c.name AS beername, d._id AS pagenumber, d.sampled AS sampled FROM brewery b INNER JOIN beer c on b._id=c.breweryid INNER JOIN beerdetail d on c._id=d.beerid ORDER BY d._id DESC LIMIT 20";
            $result = $dbConnection->query($select);
            while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            ?>
            <li>
              <?php
              echo "#" . $row->pagenumber . ": ";
              echo "<a href='/brewery.php?" . $row->breweryid . "#" . $row->beerid . "'>" . $row->breweryname . " " . $row->beername. "</a>";
              ?>
            </li>
            <?php
            }
            $result->closeCursor();
            ?>
          </ul>
        </div>
        <div class='col-12 col-md-6'>
          <h1>Brewery News</h1>
          <ul>
            <?php
            $select = "SELECT source, url, DATE_FORMAT(newsdate, '%M %d, %Y') AS newsdate_fmt, title FROM news ORDER BY newsdate DESC LIMIT 10";
            $result = $dbConnection->query($select);
            while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            ?>
            <li>
              <?php
              echo "<a href='{$row->url}'>{$row->title}</a>";
              ?>
              <br>
              <?php
              echo "({$row->source} &mdash; {$row->newsdate_fmt})";
              ?>
            </li>
            <?php
            }
            $result->closeCursor();
            ?>
          </ul>
        </div>
      </div>
    </div>
  </main>
  <?php require_once 'db/dbdisconnect.php'; ?>
</body>
