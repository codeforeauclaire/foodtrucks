/**
 * @file
 * Handle the common map.
 */

(function ($) {
    "use strict";

    Drupal.behaviors.geolocationCommonMap = {
        attach: function (context, settings) {
            var map = $('#' + drupalSettings.geolocation.commonMap.id, context);
            map.children('.geolocation-common-map-locations').hide();
            var bounds = new google.maps.LatLngBounds();
            var container = map.children('.geolocation-common-map-container');
            container.show();
            if (map.data('centre-lat'), map.data('centre-lng')) {
                var center = new google.maps.LatLng(map.data('centre-lat'), map.data('centre-lng'));
                bounds.extend(center);
            }
            else {
                var center = new google.maps.LatLng(0, 0);
            }

            var googleMap = new google.maps.Map(container[0], {
                center: center,
                zoom: 12
            });

            map.find('.geolocation-common-map-locations .geolocation').each(function(index, item) {
                item = $(item);
                var position = new google.maps.LatLng(item.data('lat'), item.data('lng'));

                bounds.extend(position);

                var marker = new google.maps.Marker({
                    position: position,
                    map: googleMap,
                    title: item.children('h2').text(),
                    content: item.html()
                });

                marker.addListener('click', function() {
                    var bubble = new google.maps.InfoWindow({
                        content: marker.content,
                        maxWidth: 200
                    });
                    bubble.open(googleMap, marker);
                });
            });
            googleMap.fitBounds(bounds);
        }
    };

})(jQuery);
