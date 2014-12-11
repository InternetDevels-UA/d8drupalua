(function($){$(function() {

  if ($('#events-node-form').length || $('#events-node-edit-form').length || $('#node-events-form').length) {
    var $show_on_map = $('<input type="checkbox" id="show_on_map"><span>'+Drupal.t('Event on map')+'</span></input>');

    $("#edit-field-address-wrapper").after($show_on_map);
    if ($('#edit-field-geolocation-wrapper .geolocation-hidden-lat').val() == '') {  
      setTimeout(function() {
        $('#edit-field-geolocation-wrapper').hide();
      }, 200);
    }
    else {
      $show_on_map.attr('checked','checked');
      setTimeout(function() {
        $('#edit-field-geolocation-wrapper .geocode-controlls-wrapper').hide();
      }, 1000);
    }
    
    $show_on_map.change(function() {
      if(this.checked) {
        $city_input = $('#edit-field-city');
        $address_input = $('#edit-field-address-0-value');
        var address = $city_input.val() + ' ' + $address_input.val();
        $('#edit-field-geolocation-wrapper').show();
        $('#edit-field-geolocation-wrapper .geocode-controlls-wrapper').hide();
        $('#edit-field-geolocation-wrapper .geocode-controlls-wrapper input').val(address);
        $('#edit-field-geolocation-wrapper .geocode-controlls-wrapper button.submit').trigger('click');
      }
      else {
        $('#edit-field-geolocation-wrapper .geolocation-hidden-lat').val('');
        $('#edit-field-geolocation-wrapper .geolocation-hidden-lng').val('');
        $('#edit-field-geolocation-wrapper').hide();
      }
    });

  }

})})(jQuery);
