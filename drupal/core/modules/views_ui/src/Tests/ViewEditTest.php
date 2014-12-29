<?php

/**
 * @file
 * Contains \Drupal\views_ui\Tests\ViewEditTest.
 */

namespace Drupal\views_ui\Tests;

use Drupal\Component\Utility\String;
use Drupal\views\Entity\View;
use Drupal\views\Views;

/**
 * Tests some general functionality of editing views, like deleting a view.
 *
 * @group views_ui
 */
class ViewEditTest extends UITestBase {

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = array('test_view', 'test_display', 'test_groupwise_term_ui');

  /**
   * Tests the delete link on a views UI.
   */
  public function testDeleteLink() {
    $this->drupalGet('admin/structure/views/view/test_view');
    $this->assertLink(t('Delete view'), 0, 'Ensure that the view delete link appears');

    $view = $this->container->get('entity.manager')->getStorage('view')->load('test_view');
    $this->assertTrue($view instanceof View);
    $this->clickLink(t('Delete view'));
    $this->assertUrl('admin/structure/views/view/test_view/delete');
    $this->drupalPostForm(NULL, array(), t('Delete'));
    $this->assertRaw(t('View %name deleted', array('%name' => $view->label())));

    $this->assertUrl('admin/structure/views');
    $view = $this->container->get('entity.manager')->getStorage('view')->load('test_view');
    $this->assertFalse($view instanceof View);
  }

  /**
   * Tests the machine name form.
   */
  public function testMachineNameOption() {
    $this->drupalGet('admin/structure/views/view/test_view');
    // Add a new attachment display.
    $this->drupalPostForm(NULL, array(), 'Add Attachment');

    // Change the machine name for the display from page_1 to test_1.
    $edit = array('display_id' => 'test_1');
    $this->drupalPostForm('admin/structure/views/nojs/display/test_view/attachment_1/display_id', $edit, 'Apply');
    $this->assertLink(t('test_1'));

    // Save the view, and test the new ID has been saved.
    $this->drupalPostForm(NULL, array(), 'Save');
    $view = \Drupal::entityManager()->getStorage('view')->load('test_view');
    $displays = $view->get('display');
    $this->assertTrue(!empty($displays['test_1']), 'Display data found for new display ID key.');
    $this->assertIdentical($displays['test_1']['id'], 'test_1', 'New display ID matches the display ID key.');
    $this->assertFalse(array_key_exists('attachment_1', $displays), 'Old display ID not found.');

    // Test the form validation with invalid IDs.
    $machine_name_edit_url = 'admin/structure/views/nojs/display/test_view/test_1/display_id';
    $error_text = t('Display name must be letters, numbers, or underscores only.');

    $edit = array('display_id' => 'test 1');
    $this->drupalPostForm($machine_name_edit_url, $edit, 'Apply');
    $this->assertText($error_text);

    $edit = array('display_id' => 'test_1#');
    $this->drupalPostForm($machine_name_edit_url, $edit, 'Apply');
    $this->assertText($error_text);

    // Test using an existing display ID.
    $edit = array('display_id' => 'default');
    $this->drupalPostForm($machine_name_edit_url, $edit, 'Apply');
    $this->assertText(t('Display id should be unique.'));

    // Test that the display ID has not been changed.
    $this->drupalGet('admin/structure/views/view/test_view/edit/test_1');
    $this->assertLink(t('test_1'));
  }

  /**
   * Tests the 'Other' options category on the views edit form.
   */
  public function testEditFormOtherOptions() {
    // Test the Field language form.
    $this->drupalGet('admin/structure/views/view/test_view');
    $langcode_url = 'admin/structure/views/nojs/display/test_view/default/field_langcode';
    $this->assertLinkByHref($langcode_url);
    $this->assertLink(t('Language selected for !type', array('!type' => t('Content'))));
    // Click the link and check the form before language is added.
    $this->drupalGet($langcode_url);
    $this->assertResponse(200);
    $this->assertText(t("You don't have translatable entity types."));
    // A node view should have language options.
    $this->container->get('module_installer')->install(array('node', 'language'));
    $this->resetAll();
    $this->rebuildContainer();

    $this->drupalGet('admin/structure/views/nojs/display/test_display/page_1/field_langcode');
    $this->assertResponse(200);
    $this->assertFieldByName('field_langcode', '***LANGUAGE_language_content***');
    $this->assertFieldByName('field_langcode_add_to_query', TRUE);
  }

  /**
   * Tests Representative Node for a Taxonomy Term.
   */
  public function testRelationRepresentativeNode() {
    // Populate and submit the form.
    $edit["name[taxonomy_term_data.tid_representative]"] = TRUE;
    $this->drupalPostForm('admin/structure/views/nojs/add-handler/test_groupwise_term_ui/default/relationship', $edit, 'Add and configure relationships');
    // Apply changes.
    $edit = array();
    $this->drupalPostForm('admin/structure/views/nojs/handler/test_groupwise_term_ui/default/relationship/tid_representative', $edit, 'Apply');
  }

}
