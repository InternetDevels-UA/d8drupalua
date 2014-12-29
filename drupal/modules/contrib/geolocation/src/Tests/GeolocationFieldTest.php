<?php

/**
 * @file
 * Contains \Drupal\geolocation\GeolocationFieldTest.
 */

namespace Drupal\geolocation\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the creation of geolocation fields.
 *
 * @group geolocation
 */
class GeolocationFieldTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array(
    'field',
    'node',
    'geolocation'
  );

  protected $field;
  protected $web_user;

  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(array('type' => 'article'));
    $this->article_creator = $this->drupalCreateUser(array('create article content', 'edit own article content'));
    $this->drupalLogin($this->article_creator);
  }

  // Test fields.

  /**
   * Helper function for testGeolocationField().
   */
  function testGeolocationField() {

    // Add the geolocation field to the article content type.
    entity_create('field_storage_config', array(
      'field_name' => 'field_geolocation',
      'entity_type' => 'node',
      'type' => 'geolocation',
    ))->save();
    entity_create('field_config', array(
      'field_name' => 'field_geolocation',
      'label' => 'Geolocation',
      'entity_type' => 'node',
      'bundle' => 'article',
    ))->save();

    entity_get_form_display('node', 'article', 'default')
      ->setComponent('field_geolocation', array(
        'type' => 'geolocation_latlng',
      ))
      ->save();

    entity_get_display('node', 'article', 'default')
      ->setComponent('field_geolocation', array(
        'type' => 'geolocation_latlng',
        'weight' => 1,
      ))
      ->save();

    // Display creation form.
    $this->drupalGet('node/add/article');
    $this->assertFieldByName("field_geolocation[0][lat]", '', 'Geolocation lat input field found.');
    $this->assertFieldByName("field_geolocation[0][lng]", '', 'Geolocation lng input field found.');

    // Test basic entery of geolocation field.
    $lat = '49.880657';
    $lng = '10.869212';
    $edit = array(
      'title[0][value]' => $this->randomMachineName(),
      'field_geolocation[0][lat]' => $lat,
      'field_geolocation[0][lng]' => $lng,
    );

    // Test if the raw lat, lng values are found on the page.
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $expected_lat = $lat;
    $this->assertRaw($expected_lat, 'Latitude value found on the article node page.');
    $expected_lng = $lng;
    $this->assertRaw($expected_lng, 'Longitude value found on the article node page.');
  }
}
