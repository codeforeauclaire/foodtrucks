var API = "http://foodtrucks1.version-three.com/default-endpoint";
//API = "js/test-data.json"; //uncomment to use local test data

$(document).on('pageshow', function() {
  var map = new L.Map('map', {
    center: new L.LatLng(44.799, -91.464),
    zoom: 12,
    layers: [new L.StamenTileLayer("toner-lite")]
  });

  var markerCluster = L.markerClusterGroup();
  var markers = new Array();

 $.getJSON({
    dataType: "json",
    url: API
  })
  .done(function(data){
    $.each(data, function(key, value) {
      if (value.lat) {
        var marker = new L.Marker(new L.LatLng(value.lat, value.lng))
          .bindPopup('<a href="' + value.links + '">' + value.name + '</a>'+
              '<p>Hours: ' + value.start_time + ' - ' + value.end_time + '</p>');
        markerCluster.addLayer(marker);
        marker.start_time = value.start_time
        marker.end_time = value.end_time
        markers.push(marker)
      }
    });
  })
  .fail(function(err) {
    console.log( err );
  })

  map.addLayer(markerCluster);

  $('#datepicker a').click(function (e) {
    e.preventDefault();
    var dateField = $(this).siblings('div').children('input');
    var date = new Date(dateField.datepicker('getDate'));
    if ($(this).children('.left-arrow').size() > 0) {
      dateField.datepicker('setDate', new Date(date.setDate(date.getDate() - 1)));
    } else {
      dateField.datepicker('setDate', new Date(date.setDate(date.getDate() + 1)));
    }
    $( "#dateFilter" ).trigger( "change" );
  });

  $('#dateFilter').change(function (e) {
    var filterByDate = $(this).datepicker('getDate');
    var filterByStart = new Date(filterByDate)
    var filterByEnd = new Date(filterByDate)
    filterByEnd.setDate (filterByEnd.getDate()+1);

    markerCluster.clearLayers();
    for (var i=0;i<markers.length;i++){
      //console.log(filterByStart, new Date(markers[i].start_time), new Date(markers[i].end_time), filterByEnd)
      // bug in date format, timezone, or logic
      if(new Date(markers[i].start_time ) >= filterByStart
        && new Date (markers[i].end_time) < filterByEnd  ){
          markerCluster.addLayer(markers[i]);
      }
    }
  });

});

