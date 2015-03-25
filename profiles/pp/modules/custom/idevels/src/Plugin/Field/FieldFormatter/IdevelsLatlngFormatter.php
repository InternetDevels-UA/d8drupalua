<?php

/**
 * @file
 * Contains \Drupal\idevels\Plugin\Field\FieldFormatter\IdevelsLatlngFormatter.
 */

namespace Drupal\idevels\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'idevels_latlng' formatter.
 *
 * @FieldFormatter(
 *   id = "idevels_latlng",
 *   module = "idevels",
 *   label = @Translation("Idevels map"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 */
class IdevelsLatlngFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $element = array();

    foreach ($items as $delta => $item) {
      $element[$delta] = array(
        '#theme' => 'idevels_formatter',
        '#lat' => $item->lat,
        '#lng' => $item->lng,
      );
    }
    return $element;
  }

}
