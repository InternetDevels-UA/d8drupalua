<?php

/**
 * @file
 * Contains IdevelsTwitterLoginController.
 */

namespace Drupal\idevels_twitter_login\Controller;

use Abraham\TwitterOAuth\TwitterOAuth;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Site signup/login using Twitter.
 */
class IdevelsTwitterLoginController extends ControllerBase {

  /**
   * Log in using Twitter. This is a single action for entire auth flow.
   *
   * @return \Symfony\Component\HttpFoundation\Redirect
   *   The auth page.
   *
   * @throws \Abraham\TwitterOAuth\TwitterOAuthException
   *   Twitter API error.
   */
  public function unifiedLoginRegister() {
    $config = $this->config('idevels_twitter.system');
    $consumer_key = $config->get('idevels_twitter_consumer_key');
    $secret_key = $config->get('idevels_twitter_consumer_secret');
    if (!$consumer_key || strlen($consumer_key) == 0) {
      drupal_set_message(t('Missing Twitter consumer key. Please contact site administrator.'), 'error');
      return (new RedirectResponse(\Drupal::url('<front>')));
    }
    if (!$secret_key|| strlen($consumer_key) == 0) {
      drupal_set_message(t('Missing Twitter secret key. Please contact site administrator.'), 'error');
      return (new RedirectResponse(\Drupal::url('<front>')));
    }
    libraries_load('twitteroauth');

    if (isset($_GET['oauth_token'])) {
      $request_token['oauth_token'] = isset($_SESSION['oauth_token']) ? $_SESSION['oauth_token'] : NULL;
      $request_token['oauth_token_secret'] = isset($_SESSION['oauth_token_secret']) ? $_SESSION['oauth_token_secret'] : NULL;

      // This is very, very horrible.
      if ($request_token['oauth_token'] !== $_GET['oauth_token']) {
        drupal_set_message(t('oauth_token mismatch'), 'error');
        return (new RedirectResponse(\Drupal::url('<front>')));
      }
      $connection = new TwitterOAuth($consumer_key, $secret_key, $request_token['oauth_token'], $request_token['oauth_token_secret']);
      $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier']));

      // TODO: Register or log in user.
    }
    else {
      $connection = new TwitterOAuth($consumer_key, $secret_key);
      $request_token = $connection->oauth(
        'oauth/request_token',
        array(
          'oauth_callback' => \Drupal::url(
            'idevels_twitter_login', array(), array('absolute' => TRUE)
          ),
        )
      );
      $_SESSION['oauth_token'] = $request_token['oauth_token'];
      $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

      return new RedirectResponse(
        $connection->url(
          'oauth/authorize',
          array('oauth_token' => $request_token['oauth_token'])
        )
      );
    }
  }

  /**
   * Load user by Twitter ID.
   *
   * @param int $id
   *   Twitter User ID.
   *
   * @return \Drupal\user\Entity\User
   *   Found user or null if none exists.
   */
  private function loadUserByTwitterId($id) {

    $user_ids = \Drupal::entityQuery('user')
      ->condition('field_twitter_user_id', $id)
      ->execute();
    if (!$user_ids) {
      return NULL;
    }
    return User::load(reset($user_ids));
  }

  /**
   * Load user by Twitter ID.
   *
   * This method also registers new user if none found.
   *
   * @param int $id
   *   Twitter User ID.
   * @param TwitterOAuth $connection
   *   API Connection object.
   *
   * @return \Drupal\user\Entity\User
   *   Found or newly created user.
   */
  private function loadOrRegisterUserByTwitterId($id, TwitterOAuth $connection) {

    $found_user = $this->loadUserByTwitterId($id);
    if ($found_user) {
      return $found_user;
    }

    $user_info = $connection->get("account/verify_credentials");
    // TODO: Register user if none found.
  }

}
