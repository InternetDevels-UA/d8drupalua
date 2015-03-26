<?php

/**
 * @file
 * Contains \Drupal\idevels_google_login\Form\IdevelsLoginSettingsForm.
 */

namespace Drupal\idevels_google_login\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure RSS settings for this site.
 */
class IdevelsLoginSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'system_idevels_google_login_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $idevels_google_login_config = $this->config('idevels_google.system');
    $form['idevels_google_login_client_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Client Id'),
      '#required' => TRUE,
      '#description' => t('The client id of google account.'),
      '#default_value' => $idevels_google_login_config->get('idevels_google_login_client_id'),
    );
    $form['idevels_google_login_client_secret'] = array(
      '#type' => 'textfield',
      '#title' => t('Client Secret'),
      '#required' => TRUE,
      '#description' => t('The client secret of google account.'),
      '#default_value' => $idevels_google_login_config->get('idevels_google_login_client_secret'),
    );
    $form['idevels_google_login_developer_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Api Key'),
      '#required' => TRUE,
      '#description' => t('The api key of google account.'),
      '#default_value' => $idevels_google_login_config->get('idevels_google_login_developer_key'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('idevels_google.system')
      ->set('idevels_google_login_client_id', $form_state->getValue('idevels_google_login_client_id'))
      ->set('idevels_google_login_client_secret', $form_state->getValue('idevels_google_login_client_secret'))
      ->set('idevels_google_login_developer_key', $form_state->getValue('idevels_google_login_developer_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['idevels_google.system'];
  }

}
