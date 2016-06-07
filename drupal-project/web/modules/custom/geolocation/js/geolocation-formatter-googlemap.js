/**
 * @file
 * Javascript for the geocoder Google map formatter.
 */
(function ($, Drupal) {
  // Ensure and use shortcut for gwolocation.
  var geolocation = Drupal.geolocation = Drupal.geolocation || {};

  Drupal.behaviors.geolocationGoogleMaps = {
    attach: function (context, settings) {
      // Ensure itterables.
      settings.geolocation = settings.geolocation || {maps: []};
      // Make sure the lazy loader is available.
      if (typeof Drupal.geolocation.load_google === 'function') {
        // First load the library from google.
        Drupal.geolocation.load_google(function(){

          initialize(settings.geolocation.maps);
        });
      }
    }
  };

  function initialize(maps) {
    // Loop though all objects and add maps to the page.
    $.each(maps, function(delta, map) {
      // Get the container object.
      map.container = document.getElementById(map.id);

      if ($(map.container).length >= 1
        && !$(map.container).hasClass('geolocation-processed')
        && typeof google !== 'undefined'
        && typeof google.maps !== 'undefined'
      ) {
        // Add any missing settings.
        map.settings = $.extend(geolocation.default_settings(), map.settings);
        // Add the map by ID with settings.
        geolocation.add_map(map);
        // Set the already processed flag.
        $(map.container).addClass('geolocation-processed');
      }
    });
  }
})(jQuery, Drupal);
