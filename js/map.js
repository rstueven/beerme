const Map = {};

Map.init = function (lat, lng, zoom) {
  const map = new ol.Map({
    target: 'map',
    layers: [
      new ol.layer.Tile({
        source: new ol.source.OSM()
      })
    ],
    controls: ol.control.defaults({
      attributionOptions: {
        collapsed: true
      }
    }).extend([
      new ol.control.ScaleLine(),
      new ol.control.ZoomSlider()
    ]),
    view: new ol.View({
      center: ol.proj.fromLonLat([lng, lat]),
      zoom: zoom
    })
  });

  // https://openlayers.org/workshop/en/mobile/geolocation.html
  const source = new ol.source.Vector();
  const layer = new ol.layer.Vector({
    source: source
  });
  map.addLayer(layer);

  navigator.geolocation.watchPosition(function (pos) {
    console.log(pos.coords.longitude + " : " + pos.coords.latitude + " : " + pos.coords.accuracy);
    // map.getView().setCenter(ol.proj.fromLonLat([pos.coords.longitude, pos.coords.latitude]));
    const coords = [pos.coords.longitude, pos.coords.latitude];
    const accuracy = ol.geom.Polygon.circular(new ol.Sphere(6378137), coords, pos.coords.accuracy);
    source.clear(true);
    source.addFeatures([
      new ol.Feature(accuracy.transform('EPSG:4326', map.getView().getProjection())),
      new ol.Feature(new ol.geom.Point(ol.proj.fromLonLat(coords)))
    ]);
  }, function (error) {
    alert("matchPosition(): " + error.message);
  }, {
    enableHighAccuracy: true
  });

// https://github.com/jonataswalker/ol-geocoder
  const geocoder = new Geocoder('nominatim', {
    provider: 'osm',
    lang: 'en',
    placeholder: 'Search for ...',
    limit: 5,
    debug: false,
    autoComplete: true,
    keepOpen: false
  });

  map.addControl(geocoder);

  geocoder.on('addresschosen', function (evt) {
    // const feature = evt.feature;
    // const coord = evt.coordinate;
    // const address = evt.address;
    // console.log("Feature: ");
    // console.log(feature);
    // console.log("Coord: " + coord);
    // console.log("Address: ");
    // console.log(address);
    // some popup solution
    // content.innerHTML = '<p>'+ address.formatted +'</p>';
    // overlay.setPosition(coord);
  });

  return map;
}
;

// https://medium.com/attentive-ai/working-with-openlayers-4-part-1-creating-the-first-application-9ab27bbd7a62
$(function () {
  // const map = Map.init(45.490972, -93.648045, 10);
  const map = Map.init(0, 0, 10);

  const markerVectorLayer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: '/ajax/breweryGeoJson.php',
      format: new ol.format.GeoJSON()
    }),
    style: new ol.style.Style({
      image: new ol.style.Icon({
        // anchor: [0.5, 46],
        // anchorXUnits: 'fraction',
        // anchorYUnits: 'pixels',
        src: '/graphics/icon_blue.png'
      })
    })
  });

  map.addLayer(markerVectorLayer);

  // Overlay to manage popup on the top of the map
  const popup = document.getElementById('popup');
  const overLay = new ol.Overlay({
    element: popup
  });
  map.addOverlay(overLay);
  // Manage click on the map to display/hide popup
  map.on('click', function (e) {
    const info = [];
    const coordinate = e.coordinate;
    map.forEachFeatureAtPixel(e.pixel, function (feature) {
      info.push('<table class="marker-properties"><tbody>' + feature.getKeys().slice(1).map((k, _) => {
        return '<tr><th>' + k + '</th>'
          + '<td>' + feature.get(k) + '</td>' + '</tr>';
      }).join('') + '</tbody></table>');
    });
    if (info.length > 0) {
      popup.innerHTML = info.join('').replace(/<img[^>]*>/g, "");
      popup.style.display = 'inline';
      overLay.setPosition(coordinate);
    } else {
      popup.innerHTML = '&nbsp;';
      popup.style.display = 'none';
    }
  });
});