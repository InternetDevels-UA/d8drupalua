<?php

/**
 * @file
 * Contains \Drupal\Tests\Core\Form\ConfirmFormHelperTest.
 */

namespace Drupal\Tests\Core\Form;

use Drupal\Core\Form\ConfirmFormHelper;
use Drupal\Core\Url;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Drupal\Core\Form\ConfirmFormHelper
 * @group Form
 */
class ConfirmFormHelperTest extends UnitTestCase {

  /**
   * @covers ::buildCancelLink
   *
   * Tests the cancel link title.
   */
  public function testCancelLinkTitle() {
    $cancel_text = 'Cancel text';
    $form = $this->getMock('Drupal\Core\Form\ConfirmFormInterface');
    $form->expects($this->any())
      ->method('getCancelText')
      ->will($this->returnValue($cancel_text));

    $link = ConfirmFormHelper::buildCancelLink($form, new Request());
    $this->assertSame($cancel_text, $link['#title']);
  }

  /**
   * @covers ::buildCancelLink
   *
   * Tests a cancel link route.
   */
  public function testCancelLinkRoute() {
    $route_name = 'foo_bar';
    $cancel_route = new Url($route_name);
    $form = $this->getMock('Drupal\Core\Form\ConfirmFormInterface');
    $form->expects($this->any())
      ->method('getCancelUrl')
      ->will($this->returnValue($cancel_route));
    $link = ConfirmFormHelper::buildCancelLink($form, new Request());
    $this->assertEquals(Url::fromRoute($route_name), $link['#url']);
  }

  /**
   * @covers ::buildCancelLink
   *
   * Tests a cancel link route with parameters.
   */
  public function testCancelLinkRouteWithParams() {
    $expected = Url::fromRoute('foo_bar.baz', ['baz' => 'banana'], ['absolute' => TRUE]);
    $form = $this->getMock('Drupal\Core\Form\ConfirmFormInterface');
    $form->expects($this->any())
      ->method('getCancelUrl')
      ->will($this->returnValue($expected));
    $link = ConfirmFormHelper::buildCancelLink($form, new Request());
    $this->assertEquals($expected, $link['#url']);
  }

  /**
   * @covers ::buildCancelLink
   *
   * Tests a cancel link route with a URL object.
   */
  public function testCancelLinkRouteWithUrl() {
    $cancel_route = new Url(
      'foo_bar.baz', array(
        'baz' => 'banana',
      ),
      array(
        'absolute' => TRUE,
      )
    );
    $form = $this->getMock('Drupal\Core\Form\ConfirmFormInterface');
    $form->expects($this->any())
      ->method('getCancelUrl')
      ->will($this->returnValue($cancel_route));
    $link = ConfirmFormHelper::buildCancelLink($form, new Request());
    $this->assertSame($cancel_route, $link['#url']);
  }

  /**
   * @covers ::buildCancelLink
   *
   * Tests a cancel link provided by the destination.
   */
  public function testCancelLinkDestination() {
    $query = array('destination' => 'baz');
    $form = $this->getMock('Drupal\Core\Form\ConfirmFormInterface');
    $link = ConfirmFormHelper::buildCancelLink($form, new Request($query));
    $this->assertSame('base://' . $query['destination'], $link['#url']->getUri());
  }

}
