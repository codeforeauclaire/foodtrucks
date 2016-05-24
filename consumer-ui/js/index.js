var API = "http://foodtrucks1.version-three.com/default-endpoint";
API = "js/test/event.json"; //uncomment to use local test data

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

  var pastEventIcon = new L.Icon({
    iconUrl: 'deps/leaflet/images/marker-icon-gray-2x.png',
    shadowUrl: 'deps/leaflet/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  });
  //current events use default marker

 $.getJSON({
    dataType: "json",
    url: API
  })
  .done(function(data){
    $.each(data, function(key, value) {

      if (value.lat) {
        var expired = isExpired(new Date (value.end_time));
        var marker = new L.Marker(new L.LatLng(value.lat, value.lng))
          .bindPopup(getPopupHtml(value, dateFormat, expired));

        if (expired)marker.setIcon(pastEventIcon)
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

  // temporary client side date filter. To be replaced with ajax call to server action.
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

function getPopupHtml(value, dateFormat, expired){
  var content = '<a href="' + value.links + '">' + value.name + '</a><br/>';
  if(value.logo) content += '<img src="' +value.logo+'" alt="logo"/>';
  content += '<p>' +value.description+'</p>';
  if (expired) content += '<p><strong> SERVICE HOURS HAVE PASSED!</strong></p>';
  content += '<p>Hours: '
          + moment(value.start_time).format(dateFormat)
          + ' - ' + moment(value.end_time).format(dateFormat) + '</p>';
  return content;
}

function isExpired(endtime, date){
  var now = date ? date : new Date()
  if (endtime < now) return true
  else return false
}
