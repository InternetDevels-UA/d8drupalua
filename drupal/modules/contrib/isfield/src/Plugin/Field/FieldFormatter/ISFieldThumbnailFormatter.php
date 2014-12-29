<?php

/**
 * @file
 *
 * Contains \Drupal\isfield\Plugin\Field\FieldFormatter\ISFieldThumbnailFormatter.
 */
namespace Drupal\isfield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'isfield_thumbnail' formatter
 *
 * @FieldFormatter(
 *   id = "isfield_thumbnail",
 *   label = @Translation("ISField thumbnail"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "link"
 *   }
 * )
 */
class ISFieldThumbnailFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'dimensions' => '120x90',
      'style' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $dimensions = \Drupal::config('isfield.settings')->get('thumbnail_dimensions');
    foreach ($dimensions as $key => $value) {
      if (!preg_match('/^\d+x\d+$/', $value)) {
        unset($dimensions[$key]);
      }
    }

    $element['dimensions'] = array(
      '#title' => t('Dimensions'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('dimensions'),
      '#options' => array_combine($dimensions, $dimensions),
      '#description' => t('The above list of predefined dimensions could be modified in the <a href="@url">settings page</a>.', array(
        '@url' => Url::fromRoute('isfield.settings')->toString(),
      )),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = t('Dimensions: @dimensions', array('@dimensions' => $this->getSetting('dimensions')));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    $options = array();

    $dimensions = $this->getSetting('dimensions');
    if ($dimensions) {
      list($options['width'], $options['height']) = explode('x', $dimensions);
    }

    foreach ($items as $delta => $item) {
      $field_key = $item->mainPropertyName();
      if (!$wrapper = isfield_get_media_wrapper($item->$field_key)) {
        continue;
      }
      $elements[$delta] = array(
        '#theme' => 'image',
        '#uri' => $wrapper->thumbnail(FALSE),
        '#attributes' => array(
          'width' => $options['width'],
          'height' => $options['height'],
        ),
      );
    }

    return $elements;
  }
}
