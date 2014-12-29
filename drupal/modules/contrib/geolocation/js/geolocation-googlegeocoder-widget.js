/**
 * @file
 * Javascript for the Google geocoder widget.
 */

(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.geolocation = Drupal.geolocation || {};
  Drupal.geolocation.maps = Drupal.geolocation.maps || {};
  Drupal.geolocation.markers = Drupal.geolocation.markers || {};
  Drupal.geolocation.geocoders = Drupal.geolocation.geocoder || {};
  Drupal.geolocation.geocodeControls = Drupal.geolocation.geocodeControls || {};

  Drupal.behaviors.geolocationGooglemaps = {
    attach: function(context, settings) {

      var lat;
      var lng;
      var latLng;
      var mapOptions;
      var singleClick;

      /**
       * Process drupalSettings for every Google map present on the current page.
       */
      $.each(drupalSettings.geolocation.defaults, function(canvasId, mapDefaults) {

        // Process every map canvas once. This will also add 'map-canvas-processed' class.
        $("#" + canvasId).once('map-canvas', function() {

          var $mapCanvas = $(this);
          var $hiddenLat = $mapCanvas.siblings(".geolocation-hidden-lat");
          var $hiddenLng = $mapCanvas.siblings(".geolocation-hidden-lng");

          var hiddenLatValue = $hiddenLat.attr('value');
          var hiddenLngValue = $hiddenLng.attr('value');
          lat = hiddenLatValue == false ? mapDefaults.lat : hiddenLatValue;
          lng = hiddenLngValue == false ? mapDefaults.lng : hiddenLngValue;
          latLng = new google.maps.LatLng(lat, lng);

          // Set map options
          mapOptions = {
            scrollwheel: false,
            panControl: false,
            mapTypeControl: true,
            scaleControl: false,
            streetViewControl: false,
            overviewMapControl: false,
            zoomControl: true,
            zoomControlOptions: {
              style: google.maps.ZoomControlStyle.SMALL,
              position: google.maps.ControlPosition.LEFT_BOTTOM
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: latLng,
            zoom: 2,
          }

          // Create map
          Drupal.geolocation.maps[canvasId] = new google.maps.Map(document.getElementById(canvasId), mapOptions);

          // Create geocoder
          Drupal.geolocation.geocoders[canvasId] = new google.maps.Geocoder();

          // Create control elements
          Drupal.geolocation.geocodeControls[canvasId] = new GeocodeControl(Drupal.geolocation.maps[canvasId]);
          Drupal.geolocation.geocodeControls[canvasId].index = 1;
          Drupal.geolocation.maps[canvasId].controls[google.maps.ControlPosition.LEFT_TOP].push(Drupal.geolocation.geocodeControls[canvasId]);

          // Set initial marker
          if (lat && lng) {
            Drupal.geolocation.codeLatLng(latLng, $mapCanvas, 'initialize');
          }

          // Listener to set marker
          google.maps.event.addListener(Drupal.geolocation.maps[canvasId], 'click', function(marker) {
            var $mapCanvas = $('#' + canvasId);
            // Create 300ms timeout to wait for double click.
            singleClick = setTimeout(function() {
              Drupal.geolocation.codeLatLng(marker.latLng, $mapCanvas, 'marker');
              Drupal.geolocation.setMapMarker(marker.latLng, $mapCanvas);
            }, 300);
          });

          // Detect double click to avoid setting marker
          google.maps.event.addListener(Drupal.geolocation.maps[canvasId], 'dblclick', function(me) {
            clearTimeout(singleClick);
          });
        })

      });

      /**
       * Click event handler to the 'Get location' button.
       */
      $('.geolocation-map-canvas').on('click', '.geocode-controlls-wrapper .submit', function(event) {
        var $mapCanvas = $(this).closest('.geolocation-map-canvas');
        event.preventDefault();
        Drupal.geolocation.codeAddress($mapCanvas);
      });

      /**
       * Keypress event handler for the ENTER key to the geocoder input field.
       */
      $('.geolocation-map-canvas').on('keypress', '.geocode-controlls-wrapper .input', function(event){
        if (event.which == 13) {
          var $mapCanvas = $(this).closest('.geolocation-map-canvas');
          event.preventDefault();
          Drupal.geolocation.codeAddress($mapCanvas);
        }
      });

      /**
       * Click event handler to the 'Get location' button.
       */
      $('.geolocation-map-canvas').on('click', '.geocode-controlls-wrapper .clear', function(event) {
        var $mapCanvas = $(this).closest('.geolocation-map-canvas');
        event.preventDefault();
        Drupal.geolocation.clearLocation($mapCanvas);
      });

    }
  };

  /**
   * Geocode location from address field input.
   *
   * @param $mapCanvas
   *   jQuery object containing the parent map canvas
   */
  Drupal.geolocation.codeAddress = function($mapCanvas) {
    var canvasId = $mapCanvas.attr('id');
    var $addressInput = $('.geocode-controlls-wrapper .input', $mapCanvas);
    var address = $addressInput.val();

    // Return if the input field is empty.
    if (address.length < 1) {
      return;
    }

    Drupal.geolocation.geocoders[canvasId].geocode( { 'address': address }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        Drupal.geolocation.maps[canvasId].fitBounds(results[0].geometry.viewport);
        Drupal.geolocation.codeLatLng(results[0].geometry.location, $mapCanvas, 'textinput');
      }
      else {
        alert(Drupal.t('Geocode was not successful for the following reason: ') + status);
      }
    });
  }

  /**
   * Set the latitude and longitude values to the input fields
   * And optionally update the address field
   *
   * @param latLng
   *   a location (latLng) object from google maps api
   * @param $mapCanvas
   *   jQuery object containing the parent map canvas
   * @param op
   *   the op that was performed
   */
  Drupal.geolocation.codeLatLng = function(latLng, $mapCanvas, op) {
    var canvasId = $mapCanvas.attr('id');
    var $hiddenLat = $mapCanvas.siblings(".geolocation-hidden-lat");
    var $hiddenLng = $mapCanvas.siblings(".geolocation-hidden-lng");
    var $addressInput = $('.geocode-controlls-wrapper .input', $mapCanvas);
    var lat = latLng.lat();
    var lng = latLng.lng();

    // Update the lat and lng input fields
    $hiddenLat.attr('value', lat);
    $hiddenLng.attr('value', lng);

    // Return if there is no geocoder registered for the current map canvasId.
    if (!Drupal.geolocation.geocoders[canvasId]) {
      return;
    }

    // Update the address field
    if (op == 'marker' || op == 'initialize') {
      Drupal.geolocation.geocoders[canvasId].geocode({'latLng': latLng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          $addressInput.val(results[0].formatted_address);
          // When initializing the map also set a marker and zoom in.
          if (op == 'initialize') {
            Drupal.geolocation.setMapMarker(results[0].geometry.location, $mapCanvas);
            Drupal.geolocation.maps[canvasId].fitBounds(results[0].geometry.viewport);
          }
        }
        else {
          $addressInput.val('');
          // Why throw an error, the geocoder just didnt return an adress
          //if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
          //  alert(Drupal.t('Geocoder failed due to: ') + status);
          //}
        }
      });
    }
    else if (op == 'textinput') {
      // Set a marker on textinput
      Drupal.geolocation.setMapMarker(latLng, $mapCanvas);
    }

    return false;
  }

  /**
   * Set/Update a marker on a map
   *
   * @param latLng
   *   a location (latLng) object from google maps api
   * @param $mapCanvas
   *   jQuery object containing the parent map canvas
   */
  Drupal.geolocation.setMapMarker = function(latLng, $mapCanvas) {
    var canvasId = $mapCanvas.attr('id');

    // Remove old marker
    if (Drupal.geolocation.markers[canvasId]) {
      Drupal.geolocation.markers[canvasId].setMap(null);
    }
    Drupal.geolocation.markers[canvasId] = new google.maps.Marker({
      map: Drupal.geolocation.maps[canvasId],
      // draggable: true,
      position: latLng
    });
    Drupal.geolocation.maps[canvasId].panTo(latLng);

    google.maps.event.addListener(Drupal.geolocation.markers[canvasId], 'dragend', function(marker) {
      Drupal.geolocation.codeLatLng(marker.latLng, $parent, 'marker');
    });
  }

  /**
   * Clear/Remove the values and the marker
   *
   * @param $mapCanvas
   *   jQuery object containing the parent map canvas
   */
  Drupal.geolocation.clearLocation = function($mapCanvas) {
    var canvasId = $mapCanvas.attr('id');

    $mapCanvas.siblings(".geolocation-hidden-lat").val('');
    $mapCanvas.siblings(".geolocation-hidden-lng").val('');
    $('.geocode-controlls-wrapper .input', $mapCanvas).val('');

    Drupal.geolocation.markers[canvasId].setMap();
    var latLng = new google.maps.LatLng(0, 0);
    Drupal.geolocation.maps[canvasId].setCenter(latLng);
    Drupal.geolocation.maps[canvasId].setZoom(2);
  }

  /**
   * GeocodeControl is a constructor that adds custom geocoder controls to the map.
   * @constructor
   */
  function GeocodeControl() {

    // Create wrapper DIV
    var controlDiv = document.createElement('div');
    controlDiv.className = 'geocode-controlls-wrapper';

    // Create location input
    var controlInput = document.createElement('input');
    controlInput.setAttribute('type', 'text');
    controlInput.className = 'input';
    controlInput.placeholder = 'Enter a location';
    controlDiv.appendChild(controlInput);

    // Create submit button
    var controlSubmit = document.createElement('button');
    controlSubmit.className = 'submit';
    controlDiv.appendChild(controlSubmit);

    // Create clear button
    var controlClear = document.createElement('button');
    controlClear.className = 'clear';
    controlDiv.appendChild(controlClear);

    return controlDiv;
  }

})(jQuery, Drupal, drupalSettings);
