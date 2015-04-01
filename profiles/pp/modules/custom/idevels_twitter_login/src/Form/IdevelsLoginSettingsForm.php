<?php

/**
 * @file
 * Contains IdevelsLoginSettingsForm.
 */

namespace Drupal\idevels_twitter_login\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Twitter Login settings form.
 */
class IdevelsLoginSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'system_idevels_twitter_login_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $idevels_twitter_login_config = $this->config('idevels_twitter.system');
    $form['idevels_twitter_consumer_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Consumer Key (API Key)'),
      '#required' => TRUE,
      '#description' => t('Consumer Key (API Key) of Twitter application.'),
      '#default_value' => $idevels_twitter_login_config->get('idevels_twitter_consumer_key'),
    );
    $form['idevels_twitter_consumer_secret'] = array(
      '#type' => 'textfield',
      '#title' => t('Consumer Secret (API Secret)'),
      '#required' => TRUE,
      '#description' => t('Consumer Secret (API Secret) of Twitter application.'),
      '#default_value' => $idevels_twitter_login_config->get('idevels_twitter_consumer_secret'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('idevels_twitter.system')
      ->set('idevels_twitter_consumer_key', $form_state->getValue('idevels_twitter_consumer_key'))
      ->set('idevels_twitter_consumer_secret', $form_state->getValue('idevels_twitter_consumer_secret'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['idevels_twitter.system'];
  }

}
