<?php

/**
 * @file
 * Contains \Drupal\isfield\Form\ISFieldSettingsForm.
 */

namespace Drupal\isfield\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure maintenance settings for this site.
 */
class ISFieldSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'isfield_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('isfield.settings');
    $form['video_dimensions'] = array(
      '#title' => t('Predefined video dimensions'),
      '#type' => 'textarea',
      '#default_value' => implode("\n", $config->get('video_dimensions')),
    );
    $form['thumbnail_dimensions'] = array(
      '#title' => t('Predefined thumbnail dimensions'),
      '#type' => 'textarea',
      '#default_value' => implode("\n", $config->get('thumbnail_dimensions')),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::config('isfield.settings')
      ->set('video_dimensions', explode("\n", str_replace("\r", "", $form_state->getValue('video_dimensions'))))
      ->set('thumbnail_dimensions', explode("\n", str_replace("\r", "", $form_state->getValue('thumbnail_dimensions'))))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
