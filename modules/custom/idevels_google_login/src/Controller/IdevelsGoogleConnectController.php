<?php

/**
 * @file
 * Contains \Drupal\idevels_google_login\Controller\IdevelsGoogleConnectController.
 */

namespace Drupal\idevels_google_login\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Utility\String;
use Drupal\idevels_google_login;

class IdevelsGoogleConnectController extends ControllerBase {

  public function unified_login_register() {

    if (isset($_GET['error'])) {
      drupal_set_message(t('There was a problem in logging in with Google Account. Contact site administrator.'), 'error');
      $response = new RedirectResponse(\Drupal::url('<front>'));
      $response->send();
      return $response;
    }
    if (isset($_GET['state'])) {
      $state = $_GET['state'];
      session_start();
      if ($state != $_SESSION['idevels_google_login_state'] && isset($_SESSION['idevels_google_login_state'])) {
        drupal_set_message(t('Invalid state parameter'), 'error');
        $response = new RedirectResponse(\Drupal::url('<front>'));
        $response->send();
        return $response;
      }
      if (isset($_GET['code'])) {
        libraries_load('google-api-php-client');
        $client_id = \Drupal::config('idevels_google.system')
          ->get('idevels_google_login_client_id');
        $client_secret = \Drupal::config('idevels_google.system')
          ->get('idevels_google_login_client_secret');
        $api_key = \Drupal::config('idevels_google.system')
          ->get('idevels_google_login_developer_key');
        $client = new \Google_Client();

        $client->setApplicationName("Google OAuth2");
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);

        $client->setRedirectUri(\Drupal::url('idevels_google_connect_login', array(), array(
          'https' => TRUE,
          'absolute' => TRUE
        )));
        $client->setDeveloperKey($api_key);
        $scopes = array(
          'https://www.googleapis.com/auth/plus.login',
          'https://www.googleapis.com/auth/plus.me',
          'https://www.googleapis.com/auth/userinfo.email',
          'https://www.googleapis.com/auth/userinfo.profile',
        );

        $client->addScope($scopes);
        $client->authenticate($_GET['code']);
        $account['access_token'] = $client->getAccessToken();

        $client = new \Google_Client();
        $client->setApplicationName("Google OAuth2");
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri(\Drupal::url('idevels_google_connect_login', array(), array(
          'https' => TRUE,
          'absolute' => TRUE
        )));
        $client->setDeveloperKey($api_key);
        $client->setAccessToken($account['access_token']);
        $scopes = array(
          'https://www.googleapis.com/auth/plus.login',
          'https://www.googleapis.com/auth/plus.me',
          'https://www.googleapis.com/auth/userinfo.email',
          'https://www.googleapis.com/auth/userinfo.profile',
        );

        $client->addScope($scopes);
        $oauth = new \Google_Service_Oauth2($client);
        $info = $oauth->userinfo->get();

        $user = user_load_by_mail($info['email']);
        if (is_object($user)) {
          user_login_finalize($user);
          $response = new RedirectResponse(\Drupal::url('<front>'));
          $response->send();
          return $response;
        }
        else {
          $account['client_id'] = \Drupal::config('idevels_google.system')
            ->get('idevels_google_login_client_id');
          $account['client_secret'] = \Drupal::config('idevels_google.system')
            ->get('idevels_google_login_client_secret');
          $account['developer_key'] = \Drupal::config('idevels_google.system')
            ->get('idevels_google_login_developer_key');
          $account['services'] = 'oauth2';
          $account['is_authenticated'] = TRUE;
          if (!$new_user = idevels_google_login_find_existing_user($info)) {
            $name = user_load_by_name($info['name']) ? $info['name'] . time() : $info['name'];
            $drupal_username_generated = idevels_google_login_unique_user_name(String::checkPlain($name));
            $password = user_password(8);
            $fields = array(
              'name' => $drupal_username_generated,
              'mail' => $info['email'],
              'pass' => $password,
              'status' => 1,
              'init' => 'email address',
              'roles' => array(
                DRUPAL_AUTHENTICATED_RID => 'authenticated user',
              ),
              'field_first_name' => String::checkPlain($info['given_name']),
              'field_last_name' => String::checkPlain($info['family_name']),
              'field_gender' => ($info['gender'] == 'male') ? 1 : 0,
            );
            $user = entity_create('user', $fields);
            $user->save();
          }
          $user = user_load_by_mail($info['email']);
          user_login_finalize($user);
          $response = new RedirectResponse(\Drupal::url('<front>'));
          $response->send();
          return $response;
        }
      }
    }
    $response = new RedirectResponse(\Drupal::url('<front>'));
    $response->send();
    return $response;
  }
}
