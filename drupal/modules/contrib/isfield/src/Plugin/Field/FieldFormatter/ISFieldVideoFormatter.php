<?php

/**
 * @file
 *
 * Contains \Drupal\isfield\Plugin\Field\FieldFormatter\ISFieldVideoFormatter.
 */
namespace Drupal\isfield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'isfield_video' formatter
 *
 * @FieldFormatter(
 *   id = "isfield_video",
 *   label = @Translation("ISField video"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "link"
 *   }
 * )
 */
class ISFieldVideoFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'dimensions' => '480x360',
    ) + array_fill_keys(array_keys(self::options()), '-');
  }

  protected static function options() {
    return array(
      'autoplay' => t('Autoplay'),
      'controls' => t('Controls'),
      'loop' => t('Loop'),
      'showinfo' => t('Show info'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $dimensions = \Drupal::config('isfield.settings')->get('video_dimensions');
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

    foreach ($this->options() as $key => $name) {
      $element[$key] = array(
        '#title' => $name,
        '#type' => 'select',
        '#default_value' => $this->getSetting($key),
        '#options' => array(0 => t('No'), 1 => t('Yes'), '-' => t('Default')),
      );
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = t('Dimensions: @dimensions', array('@dimensions' => $this->getSetting('dimensions')));
    $options = array();
    foreach ($this->options() as $key => $name) {
      $setting = $this->getSetting($key);
      if ($setting != '-') {
        $options[] = t('@name: @value', array('@name' => $name, '@value' => $setting ? t('Yes') : t('No')));
      }
    }
    if ($options) {
      $summary[] = implode(', ', $options);
    }

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
    foreach ($this->options() as $key => $name) {
      $setting = $this->getSetting($key);
      if ($setting != '-') {
        $options[$key] = $setting;
      }
    }

    foreach ($items as $delta => $item) {
      $field_key = $item->mainPropertyName();
      if (!$wrapper = isfield_get_media_wrapper($item->$field_key)) {
        continue;
      }
      $elements[$delta] = array('#markup' => $wrapper->player($options));
    }

    return $elements;
  }
}
