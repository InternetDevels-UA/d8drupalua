<?php

/**
 * @file
 * Contains \Drupal\Core\Entity\EntityDisplayModeInterface.
 */

namespace Drupal\Core\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for entity types that hold form and view mode settings.
 */
interface EntityDisplayModeInterface extends ConfigEntityInterface {

  /**
   * Returns the entity type this display mode is used for.
   *
   * @return string
   *   The entity type name.
   */
  public function getTargetType();

  /**
   * Set the entity type this display mode is used for.
   *
   * @param string $target_entity_type
   *   The target entity type for this display mode.
   *
   * @return Drupal\Core\Entity\EntityDisplayModeInterface
   *   The display mode object, for fluent interface.
   */
  public function setTargetType($target_entity_type);
}
