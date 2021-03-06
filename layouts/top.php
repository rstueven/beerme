<?php
echo "<?xml version='1.0' encoding='utf-8'?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Beer Me! &mdash; <?php

echo $documentTitle;
?></title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Bootstrap -->
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
	integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
	crossorigin="anonymous" />
<script
	src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
	integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
	crossorigin="anonymous" type=""></script>

<!-- Beer Me! -->
<!-- link type='text/css' rel='stylesheet' href='/css/beerme.css' /-->


<link rel="stylesheet"
	href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
<script src="https://openlayers.org/en/v4.6.5/build/ol.js"
	type="text/javascript"></script>

<script type="text/javascript">
/* OSM & OL example code provided by https://mediarealm.com.au/ */
var map;
var mapLat = 41.25;
var mapLng = -95.9;
var mapDefaultZoom = 10;

function initialize_map() {
	map = new ol.Map({
		target: "map",
		layers: [
			new ol.layer.Tile({
				source: new ol.source.OSM({
					url: "https://a.tile.openstreetmap.org/{z}/{x}/{y}.png"
				})
			})
		],
		view: new ol.View({
			center: ol.proj.fromLonLat([mapLng, mapLat]),
			zoom: mapDefaultZoom
		})
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
		const MAX_ITEMS = 5;
		var pixel = event.pixel;

		if (map.hasFeatureAtPixel(pixel) === true) {
			var coordinate = event.coordinate;
			var features = map.getFeaturesAtPixel(pixel);

			var label = [];

			$.each(features, function(i, feature) {
				label.push(feature.get("name"));
				if (i == MAX_ITEMS) {
					return false;
				}
			});

			if (features.length > MAX_ITEMS) {
				label.push("+ " + (features.length - MAX_ITEMS) + " more");
			}

			content.innerHTML = label.join("<br>");
			overlay.setPosition(coordinate);
		} else {
			overlay.setPosition(undefined);
			closer.blur();
		}
	});
}

function add_map_point(lat, lng, name) {
	var vectorLayer = new ol.layer.Vector({
		source:new ol.source.Vector({
			features: [new ol.Feature({
				name: name,
				geometry: new ol.geom.Point(ol.proj.transform([parseFloat(lng), parseFloat(lat)], 'EPSG:4326', 'EPSG:3857')),
			})]
		}),
		style: new ol.style.Style({
			image: new ol.style.Icon({
				anchor: [0.5, 0.5],
				anchorXUnits: "fraction",
				anchorYUnits: "fraction",
				src: "https://upload.wikimedia.org/wikipedia/commons/e/ec/RedDot.svg"
			})
		})
	});

	map.addLayer(vectorLayer);
}
</script>

</head>

<body>
<?php
include 'inc/dbconnect.php';
?>