<!doctype html>
<html lang="en">
<head>
  <title>Beer Me! — New Brewery Listings</title>
  <?php require_once 'inc/headers.php'; ?>
</head>
<body>
<?php
require_once 'inc/beerme.php';
require_once 'db/dbconnect.php';
include('inc/paginator.class.php');
?>

<main role="main" class="container">
  <?php require_once 'inc/navbar.php' ?>
  <div class="content">
    <h1>New Brewery Listings</h1>

    <?php
    $pages = new Paginator();
    $pages->default_ipp = 15;
    $select
      = "SELECT COUNT(*) FROM brewery b INNER JOIN administrativearea a ON b.region=a._id INNER JOIN country c ON a.country=c.country WHERE b.status != 'Deleted'";
    $sql_forms = $dbConnection->query($select);
    $pages->items_total = $sql_forms->fetchColumn();
    $pages->mid_range = 9;
    $pages->paginate();

    $select
      = "SELECT b._id, b.name, b.city, b.dateEntered, a.nom AS state, c.nom AS country FROM brewery b INNER JOIN administrativearea a ON b.region=a._id INNER JOIN country c ON a.country=c.country WHERE b.status != 'Deleted' ORDER BY b.dateEntered DESC, b._id DESC "
      . $pages->limit;
    $result = $dbConnection->query($select);
    ?>

    <div class="clearfix"></div>

    <div class="row marginTop">
      <div class="col-sm-12 paddingLeft pagerfwt">
        <?php if ($pages->items_total > 0) { ?>
          <?php echo $pages->display_pages(); ?>
          <?php echo $pages->display_items_per_page(); ?>
          <?php echo $pages->display_jump_menu(); ?>
        <?php } ?>
      </div>
      <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <ul>
      <?php
      if ($pages->items_total > 0) {
        $n = 1;
        while ($row = $result->fetch(PDO::FETCH_OBJ)) {
          ?>
          <li>
            <a href='/brewery.php?<?php echo $row->_id; ?>'><?php echo $row->name; ?></a>
            &mdash; <?php echo $row->city . ", "; ?>
            <?php
            if ($row->state != "") {
              echo "{$row->state}, ";
            }
            echo $row->country;
            ?>
          </li>
          <?php
        }
      } else {
        ?>
        No records found
        <?php
      }
      $result->closeCursor();
      ?>
    </ul>

    <div class="clearfix"></div>

    <div class="row marginTop">
      <div class="col-sm-12 paddingLeft pagerfwt">
        <?php if ($pages->items_total > 0) { ?>
          <?php echo $pages->display_pages(); ?>
          <?php echo $pages->display_items_per_page(); ?>
          <?php echo $pages->display_jump_menu(); ?>
        <?php } ?>
      </div>
      <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

  </div>
</main>
<?php require_once 'db/dbdisconnect.php'; ?>
</body>
</html>