<?php
$documentTitle = "The most complete source of brewery information worldwide."?>
<?php

include 'layouts/top.php';
?>

<h1>BEER ME!</h1>

<div id="map" style="width: 100vw; height: 100vh;"></div>

<?php
$breweries = $db->query("SELECT _id, name, latitude, longitude FROM brewery WHERE status NOT IN ('Deleted') ORDER BY name");
if ($breweries) {
  $breweries->data_seek(0);
  $breweryList = [];

  while ($brewery = $breweries->fetch_assoc()) {
    $breweryList[] = [
      "id" => $brewery["_id"],
      "name" => $brewery["name"],
      "latitude" => $brewery["latitude"],
      "longitude" => $brewery["longitude"]
    ];
  }

  $breweries->close();
  ?>
<script type="text/javascript">
$(function() {
  initialize_map();
  var breweries = <?php
  echo json_encode($breweryList);
  ?>;
	console.log(breweries);
	$.each(breweries, function(i, brewery) {
  	add_map_point(brewery.latitude, brewery.longitude);
	});
});
</script>

<?php
} else {
  echo "<li>query failed: " . $mysqli->error . "</li>";
}
?>

<?php

include 'layouts/bottom.php';
?>