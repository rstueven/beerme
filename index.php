<?php
$documentTitle = "The most complete source of brewery information worldwide."?>
<?php

include 'layouts/top.php';
?>

<h1>BEER ME!</h1>

<div id="map" style="width: 100vw; height: 100vh;"></div>
<div id="popup" class="ol-popup">
     <a href="#" id="popup-closer" class="ol-popup-closer"></a>
     <div id="popup-content"></div>
 </div>

<!-- AJAX will work a whole lot better here -->
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

	$.each(breweries, function(i, brewery) {
  	add_map_point(brewery.latitude, brewery.longitude, brewery.name);
	});

	 var container = document.getElementById('popup');
	 var content = document.getElementById('popup-content');
	 var closer = document.getElementById('popup-closer');

	 var overlay = new ol.Overlay({
	     element: container,
	     autoPan: true,
	     autoPanAnimation: {
	         duration: 250
	     }
	 });
	 map.addOverlay(overlay);

	 closer.onclick = function() {
	     overlay.setPosition(undefined);
	     closer.blur();
	     return false;
	 };

	 map.on('singleclick', function (event) {
	 	var pixel = event.pixel;
		if (map.hasFeatureAtPixel(pixel) === true) {
			var coordinate = event.coordinate;
			map.forEachFeatureAtPixel(pixel, function(feature, layer) {
				content.innerHTML = feature.get("name");
				overlay.setPosition(coordinate);
			});
		} else {
			overlay.setPosition(undefined);
			closer.blur();
		}
	});
});
</script>

<?php
} else {
  echo "<li>query failed: " . $db->error . "</li>";
}
?>

<?php

include 'layouts/bottom.php';
?>