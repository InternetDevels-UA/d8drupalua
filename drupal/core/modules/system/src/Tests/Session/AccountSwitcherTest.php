<?php

/**
 * @file
 * Contains Drupal\system\Tests\Session\AccountSwitcherTest.
 */

namespace Drupal\system\Tests\Session;

use Drupal\Core\Session\UserSession;
use Drupal\simpletest\KernelTestBase;

/**
 * Test case for account switching.
 *
 * @group Session
 */
class AccountSwitcherTest extends KernelTestBase {

  public function testAccountSwitching() {
    $session_manager = $this->container->get('session_manager');
    $user = $this->container->get('current_user');
    $switcher = $this->container->get('account_switcher');
    $original_user = $user->getAccount();
    $original_session_saving = $session_manager->isEnabled();

    // Switch to user with uid 2.
    $switcher->switchTo(new UserSession(array('uid' => 2)));

    // Verify that the active user has changed, and that session saving is
    // disabled.
    $this->assertEqual($user->id(), 2, 'Switched to user 2.');
    $this->assertFalse($session_manager->isEnabled(), 'Session saving is disabled.');

    // Perform a second (nested) user account switch.
    $switcher->switchTo(new UserSession(array('uid' => 3)));
    $this->assertEqual($user->id(), 3, 'Switched to user 3.');

    // Revert to the user session that was active between the first and second
    // switch.
    $switcher->switchBack();

    // Since we are still in the account from the first switch, session handling
    // still needs to be disabled.
    $this->assertEqual($user->id(), 2, 'Reverted back to user 2.');
    $this->assertFalse($session_manager->isEnabled(), 'Session saving still disabled.');

    // Revert to the original account which was active before the first switch.
    $switcher->switchBack();

    // Assert that the original account is active again, and that session saving
    // has been re-enabled.
    $this->assertEqual($user->id(), $original_user->id(), 'Original user correctly restored.');
    $this->assertEqual($session_manager->isEnabled(), $original_session_saving, 'Original session saving correctly restored.');

    // Verify that AccountSwitcherInterface::switchBack() will throw
    // an exception if there are no accounts left in the stack.
    try {
      $switcher->switchBack();
      $this->fail('::switchBack() throws exception if called without previous switch.');
    }
    catch (\RuntimeException $e) {
      if ($e->getMessage() == 'No more accounts to revert to.') {
        $this->pass('::switchBack() throws exception if called without previous switch.');
      }
      else {
        $this->fail($e->getMessage());
      }
    }
  }

}
