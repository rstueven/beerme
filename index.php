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

<script type="text/javascript">
$(function() {
  initialize_map();

  $.get({
    url: 'http://beerme/ajax/breweries.php',
    success: function(data, textStatus, jqXHR) {
      $.each(data, function(i, brewery) {
        add_map_point(brewery.latitude, brewery.longitude, brewery.name);
      });
    }
  });
});
</script>

<?php
include 'layouts/bottom.php';
?>