/* globals L, moment */
var API_EVENTS = 'http://vendors.foodtrucks.codeforeauclaire.org/api/events'
// API_EVENTS = "js/test/event.json"; // uncomment to use local test data
var API_VENDORS = 'http://vendors.foodtrucks.codeforeauclaire.org/api/vendors'
// API_VENDORS = "js/test/vendor.json"; // uncomment to use local test data

$(document).on('pageshow', function () {
  var map = new L.Map('map', {
    center: new L.LatLng(44.799, -91.464),
    zoom: 12,
    layers: [new L.StamenTileLayer('terrain')]
  })

  var markerCluster = L.markerClusterGroup()
  map.addLayer(markerCluster)
  var markers = []

  var timeFormat = 'h:mma'
  var dateFormat = 'M/D/YY'

  var pastEventIcon = new L.Icon({
    iconUrl: 'deps/leaflet/images/marker-icon-gray-2x.png',
    shadowUrl: 'deps/leaflet/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  })
  // current events use default marker

  // get vendor list
  $.getJSON({
    dataType: 'json',
    url: API_VENDORS
  })
  .done(function (data) {
    $.each(data, function (key, value) {
      $('#truck-list').append("<div><p><b><a href='" + (value.website_url || '#') + "' target='_blank'>" +
        value.title + '</a></b> ' + (value.description || '') + '</p></div>')
    })
  })
  .fail(function (err) {
    console.log(err)
  })

  // get event list
  $.getJSON({
    dataType: 'json',
    url: API_EVENTS
  })
  .done(function (data) {
    $.each(data, function (key, value) {
      if (value.lat) {
        // Adjust API which look like UTC but are actually CDT
        var expired = isExpired(value.end_time)
        var marker = new L.Marker(new L.LatLng(value.lat, value.lng))
          .bindPopup(getPopupHtml(value, dateFormat, timeFormat, expired))

        if (expired)marker.setIcon(pastEventIcon)
        marker.start_time = value.start_time
        marker.end_time = value.end_time
        markers.push(marker)
      }
    })
    if (window.location.hash.toLowerCase() === '#all') {
      $('#dateFilter').val('All')
      markerCluster.addLayers(markers)
    } else {
      $('#dateFilter').trigger('change')
    }
  })
  .fail(function (err) {
    console.log(err)
  })

  $('#datepicker a').click(function (e) {
    e.preventDefault()
    var dateField = $(this).siblings('div').children('input')
    var date = new Date(dateField.datepicker('getDate'))
    if ($(this).children('.left-arrow').size() > 0) {
      dateField.datepicker('setDate', new Date(date.setDate(date.getDate() - 1)))
    } else {
      dateField.datepicker('setDate', new Date(date.setDate(date.getDate() + 1)))
    }
    $('#dateFilter').trigger('change')
  })

  // temporary client side date filter. To be replaced with ajax call to server action.
  $('#dateFilter').change(function (e) {
    var filterDate = $(this).datepicker('getDate')
    // convert to UTC to match api dates
    filterDate = new Date(filterDate.getUTCFullYear(), filterDate.getUTCMonth(), filterDate.getUTCDate(),
      filterDate.getUTCHours(), filterDate.getUTCMinutes(), filterDate.getUTCSeconds())

    markerCluster.clearLayers()
    for (var i = 0; i < markers.length; i++) {
      var open = new Date(markers[i].start_time)
      var close = new Date(markers[i].end_time)
      if (isBefore(open, filterDate) && isAfter(close, filterDate)) {
        markerCluster.addLayer(markers[i])
      }
    }
  })
})

// compare dates without time, return true if date1 is on or before date2
function isBefore (date1, date2) {
  if (date1.getFullYear() < date2.getFullYear()) return true
  else if (date1.getFullYear() <= date2.getFullYear() && date1.getMonth() < date2.getMonth()) return true
  else if (date1.getFullYear() <= date2.getFullYear() && date1.getMonth() <= date2.getMonth() && date1.getDate() <= date2.getDate()) return true
  else return false
}
// compare dates without time, return true if date1 is on or after date2
function isAfter (date1, date2) {
  if (date1.getFullYear() > date2.getFullYear()) return true
  else if (date1.getFullYear() >= date2.getFullYear() && date1.getMonth() > date2.getMonth()) return true
  else if (date1.getFullYear() >= date2.getFullYear() && date1.getMonth() >= date2.getMonth() && date1.getDate() >= date2.getDate()) return true
  else return false
}

function getPopupHtml (value, dateFormat, timeFormat, expired) {
  var content = '<a href="' + (value[0].website_url ? value[0].website_url : '#') + '">' + value[0].title + '</a><br/>'
  if (value[0].logo) content += '<img src="' + value[0].logo + '" alt="logo"/>'
  content += '<p>' + value[0].description + '</p>'

  content += '<p class="details">Here on ' +
    moment(value.start_time).format(dateFormat) + ' <br><strong>' +
    moment(value.start_time).format(timeFormat) + ' - ' +
    moment(value.end_time).format(timeFormat) + ' ' +
        (moment().isDST() ? '' : '') +
        '</strong></p>'
  if (expired) content += '<strong class="ended">(Ended)</strong>'
  return content
}

// Takes end_time in format given by api
function isExpired (endtime) {
  return new Date() > new Date(endtime + (moment().isDST() ? '-05:00' : '-06:00'))
}
