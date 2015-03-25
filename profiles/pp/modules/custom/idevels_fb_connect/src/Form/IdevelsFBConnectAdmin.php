<?php

/**
 * @file
 */

namespace Drupal\idevels_fb_connect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Facebook user login administration settings form
 */
class IdevelsFBConnectAdmin extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'idevels_fb_connect_api_keys_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // dpm(\Drupal::config('idevels_fb_connect.settings')->get('idevels_fb_connect_appid'));
    $form['idevels_fb_connect_appid'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Application ID'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_appid'),
      '#description' => t('Also called the <em>OAuth client_id</em> value on Facebook App settings pages. <a href="https://www.facebook.com/developers/createapp.php">Facebook Apps must first be created</a> before they can be added here.'),
    );

    $form['idevels_fb_connect_skey'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Application Secret'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_skey'),
      '#description' => t('Also called the <em>OAuth client_secret</em> value on Facebook App settings pages.'),
    );

    $form['idevels_fb_connect_connect_url'] = array(
      '#type' => 'textfield',
      '#attributes' => array('readonly' => 'readonly'),
      '#title' => t('Connect url'),
      '#description' => t('Copy this value into Facebook Applications on Connect settings tab'),
      '#default_value' => $GLOBALS['base_url'],
    );

    $form['idevels_fb_connect_login_only'] = array(
      '#type' => 'checkbox',
      '#title' => t('Login Only (No Registration)'),
      '#description' => t('Allow only existing users to login with FB. New users can not signup using FB Connect.'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_login_only'),
    );

    $form['idevels_fb_connect_user_pictures'] = array(
      '#type' => 'checkbox',
      '#title' => t('Fetch User Profile Pic on Registration'),
      '#description' => t('Gets the profile pic from facebook when user registers on the site with FB Connect.'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_user_pictures'),
    );

    $form['idevels_fb_connect_post_login_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Post Login url'),
      '#description' => t('Drupal URL to which the user should be redirected to after successful login.'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_post_login_url'),
    );

    $form['idevels_fb_connect_picture_dimensions'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Picture Dimensions'),
      '#default_value' => $this->config('idevels_fb_connect.settings')->get('idevels_fb_connect_picture_dimensions'),
      '#description' => t('The imported profile pic dimensions in widthxheight format. Ex: 200x100'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_appid', $form_state->getValue('idevels_fb_connect_appid'));
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_skey', $form_state->getValue('idevels_fb_connect_skey'));
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_login_only', $form_state->getValue('idevels_fb_connect_login_only'));
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_post_login_url', $form_state->getValue('idevels_fb_connect_post_login_url'));
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_user_pictures', $form_state->getValue('idevels_fb_connect_user_pictures'));
    $this->config('idevels_fb_connect.settings')->set('idevels_fb_connect_picture_dimensions', $form_state->getValue('idevels_fb_connect_picture_dimensions'));
    $this->config('idevels_fb_connect.settings')->save();
    drupal_set_message($this->t('The configuration options have been saved.'));
  }

}
