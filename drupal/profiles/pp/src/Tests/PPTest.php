<?php

/**
 * @file
 * Contains Drupal\pp\Tests\PPTest.
 */

namespace Drupal\pp\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests PP installation profile expectations.
 *
 * @group pp
 */
class PPTest extends WebTestBase {

  protected $profile = 'pp';

  /**
   * Tests PP installation profile.
   */
  function testPP() {
    $this->drupalGet('');
    // Check the login block is present.
    $this->assertLink(t('Create new account'));
    $this->assertResponse(200);

    // Create a user to test tools and navigation blocks for logged in users
    // with appropriate permissions.
    $user = $this->drupalCreateUser(array('access administration pages', 'administer content types'));
    $this->drupalLogin($user);
    $this->drupalGet('');
    $this->assertText(t('Tools'));
    $this->assertText(t('Administration'));
  }
}
