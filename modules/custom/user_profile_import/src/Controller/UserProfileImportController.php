<?php

/**
 * @file
 * Contains \Drupal\user_profile_import\Controller\UserProfileImportController.
 */

namespace Drupal\user_profile_import\Controller;

use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

define("AVATARS_FOLDER_D6", "avatars_d6"); // Folder with all avatars from drupal.ua (d6)

/**
 * Controller routines for page example routes.
 */
class UserProfileImportController {

  /**
   * Importt profiles for users.
   */
  public function import_profiles($page) {
    // Make sure you don't trust the URL to be safe! Always check for exploits.
    if (!is_numeric($page)) {
      // We will just show a standard "access denied" page in this case.
      throw new AccessDeniedHttpException();
    }
    // Import of profiles can take long time.
    ini_set('max_execution_time', 1800);

    $result = db_select('content_type_profile_d6', 'c')
              ->fields('c')
              ->range(($page - 1), $page)
              ->execute();

    while ($row = $result->fetchAssoc()) {
      $uid = (int) $row['uid'];
      $account = user_load($uid);
      if ($account) {
        $account->set('field_bio', $row['field_bio_value']);
        $account->set('field_country', strtoupper($row['country']));
        $account->set('field_city', $row['city']);
        $account->set('field_first_name', $row['field_first_name_value']);
        $account->set('field_last_name', $row['field_last_name_value']);
        if ($row['field_gender_value'] == "I'm a boy") {
          $account->set('field_gender', 1);
        }
        elseif ($row['field_gender_value'] == "I'm a girl") {
          $account->set('field_gender', 0);
        }
        $account->set('field_skype', $row['field_skype_value']);
        $account->set('field_phone', $row['field_phone_value']);
        $account->set('field_drupal_org', $row['field_drupal_org_value']);
        $account->set('field_drupal_ru', $row['field_drupal_ru_value']);
        $account->set('field_birthday', date('Y-m-d', strtotime($row['field_birthday_value'])));
        $account->set('field_company', $row['field_profile_company_value']);
        $account->set('field_job_title', $row['field_job_title_value']);
        $account->set('field_skills', $row['field_skills_value']);

        $file_path = AVATARS_FOLDER_D6 . '/' . $row['filename'];
        if (isset($row['filename']) && is_file($file_path)) {
          $fid = (int) file_save_data(file_get_contents($file_path), 'public://pictures/' . $row['filename'], FILE_EXISTS_REPLACE)->fid->value;
          $account->set('user_picture', $fid);
        }
        else {
          
        }

        $account->save();
      }
      else {
        return new JsonResponse(array('result' => 'unsuccessful', 'id' => $uid));
      }
    }

    return new JsonResponse(array('result' => 'success', 'id' => $uid));
  }

  /**
   * Page for start imoprt profi.
   */
  public function import_about() {
    $arguments_url = Url::fromRoute('user_profile_import_arguments', array('page' => '1'));
    $stop_url = Url::fromRoute('user_profile_import_about', array('page' => '1'));
    $import_profiles_link = \Drupal::l(t('Start import'), $arguments_url);
    $stop_import_link = \Drupal::l(t('Stop import'), $stop_url);

    if (db_table_exists('content_type_profile_d6')) {
      $result = db_select('content_type_profile_d6', 'c')
            ->fields('c')
            ->execute()
            ->fetchAll();

      $build['#markup'] = t('<p>There are !row_count profiles. <span id="start_import" data-count="' . count($result) . '">!arguments_link</span> / <span>!stop_link</span></p>',
          array(
            '!arguments_link' => $import_profiles_link,
            '!stop_link' => $stop_import_link,
            '!row_count' => count($result),
          )
        );
      $build['#markup'] .= t('<p>Progress</p>') . '<div id="progressBar"><div></div></div>';
      $build['#markup'] .= t('<p>Logs</p>') . '<textarea id="showlogs"></textarea>';

      $build['#attached']['js'] = array(
        drupal_get_path('module', 'user_profile_import') . '/js/user_import.js' => array(),
      );

      $build['#attached']['css'] = array(
        drupal_get_path('module', 'user_profile_import') . '/css/user_import.css' => array(),
      );

    }
    else {
      $build['#markup'] = t('<p>Please import table "content_type_profile_d6" first</p>');
    }
    return $build;
  }


}
