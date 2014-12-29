<?php

/**
 * @file
 * Contains \Drupal\geolocation\Plugin\Field\FieldWidget\GeolocationHTML5Widget.
 */

namespace Drupal\geolocation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geolocation_html5' widget.
 *
 * @FieldWidget(
 *   id = "geolocation_html5",
 *   label = @Translation("Geoloaction HTML5"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 */
class GeolocationHTML5Widget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $lat = $items[$delta]->lat;
    $lng = $items[$delta]->lng;

    // Get the default values for existing field.
    $lat_default_value = isset($lat) ? $lat : NULL;
    $lng_default_value = isset($lng) ? $lng : NULL;

    // The 'Get my location' button.
    $button_html = '<div class="geolocation-html5-button">';
    $button_html .= '<span class="default">' . $this->t('Get browser location') . '</span>';
    $button_html .= '<span class="location"></span>';
    $button_html .= '<div class="search"></div>';
    $button_html .= '<div class="clear"></div>';
    $button_html .= '</div>';

    $element['get_location'] = array(
      '#markup' => $button_html,
    );

    // Hidden lat,lng input fields.
    $element['lat'] = array(
      '#type' => 'hidden',
      '#default_value' => $lat_default_value,
      '#attributes' => array('class' => array('geolocation-hidden-lat')),
    );
    $element['lng'] = array(
      '#type' => 'hidden',
      '#default_value' => $lng_default_value,
      '#attributes' => array('class' => array('geolocation-hidden-lng')),
    );

    // Attach css
    $element['#attached']['css'][] = drupal_get_path('module', 'geolocation') . '/css/geolocation-html5-widget.css';

    // Attach js
    $element['#attached']['js'][] = array(
      'data' => drupal_get_path('module', 'geolocation') . '/js/geolocation-html5-widget.js',
      'type' => 'file',
      'scope' => 'footer',
    );

    // Wrap the whole form in a container.
    $element += array(
      '#type' => 'item',
      '#title' => $element['#title'],
    );

    return $element;
  }

}
