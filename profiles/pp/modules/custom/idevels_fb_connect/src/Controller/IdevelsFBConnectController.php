<?php

/**
 * @file
 * Contains \Drupal\idevels_fb_connect\Controller\IdevelsFBConnectController.
 */

namespace Drupal\idevels_fb_connect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use \Drupal\user\Entity\User;
use \Drupal\user\UserInterface;
use Drupal\Component\Utility\String;

class IdevelsFBConnectController extends ControllerBase {

  //Define constructor
  public function unified_login_register() {
    $facebook = facebook_client();
    $fb_user = $facebook->getUser();
    if ($fb_user) {
      $fb_user_profile = $facebook->api('/me');
      if (isset($fb_user_profile['email'])) {
        $query = db_select('users_field_data', 'u');
        // @TODO Use $this->connection() instead as suggested by Adam 
        $query->condition('u.mail', String::checkPlain($fb_user_profile['email']));
        $query->fields('u', array('uid'));
        $query->range(0, 1);

        $drupal_user_id = 0;
        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if (count($result)) {
          $drupal_user_id = $result[0]['uid'];
        }


        if ($drupal_user_id) {
          $user_obj = User::load($drupal_user_id);
          if ($user_obj->isActive()) {
            user_login_finalize($user_obj);
            drupal_set_message(t('You have been logged in with the username !username', array('!username' => $user_obj->getUsername())));
            //@TODO Replace the reidrection with idevels_fb_connect_post_login_url
            //return $this->redirect(\Drupal::config('idevels_fb_connect.settings')->get('idevels_fb_connect_post_login_url'));
            return $this->redirect('user.page');
          }
          else {
            drupal_set_message($this->t('You could not be logged in as your account is blocked. Contact site administrator.'), 'error');
            return $this->redirect('user.page');
          }
        }
        else {
          if (!(\Drupal::config('idevels_fb_connect.settings')->get('idevels_fb_connect_login_only'))) {
            //create the drupal user
            //This will generate a random password, you could set your own here
            $fb_username = (isset($fb_user_profile['username']) ? $fb_user_profile['username'] : $fb_user_profile['name']);
            $drupal_username_generated = idevels_fb_connect_unique_user_name(String::checkPlain($fb_username));
            $password = user_password(8);
            //set up the user fields
            $fields = array(
              'name' => $drupal_username_generated,
              'mail' => String::checkPlain($fb_user_profile['email']),
              'pass' => $password,
              'status' => 1,
              'init' => 'email address',
              'roles' => array(
                DRUPAL_AUTHENTICATED_RID => 'authenticated user',
              ),
              'field_first_name' => String::checkPlain($fb_user_profile['first_name']),
              'field_last_name' => String::checkPlain($fb_user_profile['last_name']),
              'field_gender' => (int) $fb_user_profile['gender'] == 'male',
              'field_bio' => String::checkPlain($fb_user_profile['bio']),
              //'field_personal_website' => String::checkPlain($fb_user_profile['website']),
              'field_city' => String::checkPlain(explode(', ', $fb_user_profile['location']['name'])[0]),
            );
            if (!empty($fb_user_profile['location'])) {
              $country = explode(', ', $fb_user_profile['location']['name'])[2];
              $countries = \Drupal::service('country_manager')->getList();
              $iso2 = array_search($country, $countries);
              if (!empty($iso2)) {
                $fields['field_country'] = String::checkPlain($iso2);
              }
            }
            if (!empty($fb_user_profile['birthday'])) {
              $fields['field_birthday'] = date("Y-m-d", strtotime($fb_user_profile['birthday']));
            }
            if (\Drupal::config('idevels_fb_connect.settings')->get('idevels_fb_connect_user_pictures')) {
              //@TODO default it to IDEVELS_FB_CONNECT_DEFAULT_DIMENSIONS_STRING
              $dimensions_in_text = \Drupal::config('idevels_fb_connect.settings')->get('user_picture_dimensions');
              $dimensions = explode('x', $dimensions_in_text);
              if (count($dimensions) == 2) {
                $width = $dimensions[0];
                $height = $dimensions[1];
              }
              else {
                $width = IDEVELS_FB_CONNECT_DEFAULT_WIDTH;
                $height = IDEVELS_FB_CONNECT_DEFAULT_HEIGHT;
              }
              $pic_url = "https://graph.facebook.com/" . String::checkPlain($fb_user_profile['id']) . "/picture?width=$width&height=$height";
              $result = \Drupal::httpClient()->get($pic_url);
              $file = 0;
              if ($result->getStatusCode() == 200) {
                //@TODO: get default path
                $picture_directory = file_default_scheme() . '://' . 'pictures/';
                file_prepare_directory($picture_directory, FILE_CREATE_DIRECTORY);
                $file = file_save_data($result->getBody(), $picture_directory . '/' . String::checkPlain($fb_user_profile['id']) . '.jpg', FILE_EXISTS_RENAME);
              }
              else {
                // Error handling.
              }
              if (is_object($file)) {
                $fields['user_picture'] = $file->id();
              }
            }


            //the first parameter is left blank so a new user is created
            $account = entity_create('user', $fields);
            $account->save();
            // If you want to send the welcome email, use the following code
            // Manually set the password so it appears in the e-mail.
            $account->password = $fields['pass'];
            // Send the e-mail through the user module.
            //@TODO
            //drupal_mail('user', 'register_no_approval_required', $account->mail, NULL, array('account' => $account), variable_get('site_mail', 'admin@drupalsite.com'));
            drupal_set_message(t('You have been registered with the username !username', array('!username' => $account->getUsername())));
            return $this->redirect('idevels_fb_connect_login');
          }
          else {
            drupal_set_message(t('There was no account with the email addresse !email found. Please register before trying to login.', array('!email' => String::checkPlain($fb_user_profile['email']))), 'error');
            return $this->redirect('user.page');
          }
        }
      }
      else {
        drupal_set_message(t('Though you have authorised the Facebook app to access your profile, you have revoked the permission to access email address. Please contact site administrator.'), 'error');
        return $this->redirect('user.page');
      }
    }
    else {
      if (!isset($_REQUEST['error'])) {
        if (\Drupal::config('idevels_fb_connect.settings')->get('idevels_fb_connect_appid')) {
          $scope_string = '';
//                    // Make sure at least one module implements our hook
//                    @TODO
//                    if (sizeof(module_implements('idevels_fb_scope_info')) > 0) {
//                        // Call modules that implement the hook, and let them change scope.
//                        $scopes = module_invoke_all('idevels_fb_scope_info', array());
//                        $scope_string = implode(',', $scopes);
//                    }
          $scope_string .= ',email,user_about_me,user_website,user_birthday,user_location,user_work_history';

          $login_url_params = array(
            'scope' => $scope_string,
            'fbconnect' => 1,
            'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . request_uri(),
          );
          $login_url = $facebook->getLoginUrl($login_url_params);
          //@TODO
          //drupal_goto($login_url);
          //return $this->redirect($login_url);
          return new RedirectResponse($login_url);
        }
        else {
          drupal_set_message(t('Facebook App ID Missing. Can not perform Login now. Contact Site administrator.'), 'error');
          return $this->redirect('user.page');
        }
      }
      else {
        if ($_REQUEST['error'] == IDEVELS_FB_CONNECT_PERMISSION_DENIED_PARAMETER) {
          drupal_set_message(t('Could not login with facebook. You did not grant permission for this app on facebook to access your email address.'), 'error');
        }
        else {
          drupal_set_message(t('There was a problem in logging in with facebook. Contact site administrator.'), 'error');
        }
        return $this->redirect('user.page');
      }
    }
  }

}
