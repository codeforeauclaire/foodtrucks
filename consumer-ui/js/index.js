var API = "http://foodtrucks1.version-three.com/default-endpoint";
//API = "js/test-data.json"; //uncomment to use local test data

$(document).on('pageshow', function() {
  var map = new L.Map('map', {
    center: new L.LatLng(44.799, -91.464),
    zoom: 12,
    layers: [new L.StamenTileLayer("toner-lite")]
  });

  var markerCluster = L.markerClusterGroup();
  map.addLayer(markerCluster);
  var markers = new Array();

  var dateFormat = window.location.hash.toLowerCase() === '#all' ? "M/D/YY h:mm a" : "h:mm a"

 $.getJSON({
    dataType: "json",
    url: API
  })
  .done(function(data){
    $.each(data, function(key, value) {
      if (value.lat) {
        var marker = new L.Marker(new L.LatLng(value.lat, value.lng))
          .bindPopup('<a href="' + value.links + '">' + value.name + '</a><br/>'
            + '<img src="' +value.logo+'" alt="logo"/>'
            + '<p><strong>Hours:</strong> ' + moment(value.start_time).format(dateFormat) + ' - ' + moment(value.end_time).format(dateFormat) + '</p>');

        marker.start_time = value.start_time
        marker.end_time = value.end_time
        markers.push(marker)
      }
    });
    if(window.location.hash.toLowerCase() === '#all'){
      $( "#dateFilter" ).val("All")
      markerCluster.addLayers(markers);
    }else {
      $( "#dateFilter" ).trigger( "change" );
    }

  })
  .fail(function(err) {
    console.log( err );
  })

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
    var filterDate = $(this).datepicker('getDate').setHours(0,0,0,0);
    markerCluster.clearLayers();
    for (var i=0;i<markers.length;i++){
      var open  = new Date (markers[i].start_time.split('T')[0]).setHours(0,0,0,0)
      var close = new Date (markers[i].end_time.split('T')[0]).setHours(0,0,0,0)
      if(open <= filterDate && close >= filterDate ){
          markerCluster.addLayer(markers[i]);
      }
    }
  });

});

