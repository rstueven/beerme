<!doctype html>
<html lang="en">
<head>
  <title>Beer Me! — Beer List</title>
  <?php require_once 'inc/headers.php'; ?>
  <link rel="stylesheet"
        href="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.css">
  <script src="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.js"></script>
  <script src="/js/sortable-table.js"></script>
</head>
<body>
<?php
require_once 'inc/beerme.php';
require_once 'db/dbconnect.php';
?>

<main role="main" class="container">
  <?php require_once 'inc/navbar.php' ?>
  <div class="content">
    <h1>Beer List</h1>

    <!-- TRY THIS? https://stackoverflow.com/a/37260016 -->

    <div class="table-responsive-scroll">
      <table>
        <tr>
          <th class="table-sortable">Brewery / Beer</th>
          <th class="table-sortable">Style</th>
          <th class="table-sortable">Location</th>
          <th class="table-sortable">Catalog</th>
          <th class="table-sortable">Score</th>
          <th class="table-sortable">Date</th>
        </tr>
        <?php
        $select
          = "SELECT beerdetail._id AS pagenumber, beer._id, beerdetail.score AS score, beerdetail.appearancescore, beerdetail.aromascore, beerdetail.mouthfeelscore, beerdetail.overallscore, beerdetail.sampled AS sampled, beer.name AS beername, style.name AS style, brewery._id, brewery.nameshort AS breweryname, country.nom AS countryname, administrativearea.nom AS statename FROM beerdetail, beer, style, brewery, country, administrativearea WHERE country.country = administrativearea.country AND administrativearea._id = brewery.region AND brewery._id = beer.breweryid AND beer._id = beerdetail.beerid AND beer.style = style._id ORDER BY beerdetail._id DESC";
        $result = $dbConnection->query($select);
        while ($row = $result->fetch(PDO::FETCH_OBJ)) {
          ?>
          <tr class="table-sortable">
            <td><?php echo "{$row->breweryname} {$row->beername}"; ?></td>
            <td><?php echo "{$row->style}"; ?></td>
            <td><?php echo "{$row->countryname}" . (is_null(
                  $row->statename
                ) ? "" : " - {$row->statename}"); ?></td>
            <td><?php echo "{$row->pagenumber}"; ?></td>
            <td><?php echo $row->score; ?></td>
            <td><?php echo "{$row->sampled}"; ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
    </div>
  </div>
</main>
<?php require_once 'db/dbdisconnect.php'; ?>
<script>
  $(function () {
    TableSortable.init();
  });
</script>
</body>
</html>