
$(document).on('pageshow', function(){
  var layer = new L.StamenTileLayer("toner-lite");
  var map = new L.Map('map', {
    center: new L.LatLng(44.799, -91.464),
    zoom: 12
  });
  map.addLayer(layer)

  var trucks = new L.geoJson();
  trucks.addTo(map);

  $.ajax({
    dataType: "json",
    url: "js/data.json",
    success: function(data) {
        $(data.features).each(function(key, data) {
            trucks.addData(data);
        });
    }
  }).error(function() {});
});
