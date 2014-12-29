<?php

/**
 * @file
 * Contains \Drupal\Core\Asset\LibraryDiscovery.
 */

namespace Drupal\Core\Asset;

use Drupal\Core\Cache\CacheCollectorInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Discovers available asset libraries in Drupal.
 */
class LibraryDiscovery implements LibraryDiscoveryInterface {

  /**
   * The library discovery cache collector.
   *
   * @var \Drupal\Core\Cache\CacheCollectorInterface
   */
  protected $collector;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The final library definitions, statically cached.
   *
   * hook_library_alter() allows modules and themes to dynamically alter a
   * library definition (once per request).
   *
   * @var array
   */
  protected $libraryDefinitions = [];

  /**
   * Constructs a new LibraryDiscovery instance.
   *
   * @param \Drupal\Core\Cache\CacheCollectorInterface $library_discovery_collector
   *   The library discovery cache collector.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(CacheCollectorInterface $library_discovery_collector, ModuleHandlerInterface $module_handler) {
    $this->collector = $library_discovery_collector;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrariesByExtension($extension) {
    if (!isset($this->libraryDefinitions[$extension])) {
      $libraries = $this->collector->get($extension);
      $this->libraryDefinitions[$extension] = [];
      foreach ($libraries as $name => $definition) {
        // Allow modules and themes to dynamically attach request and context
        // specific data for this library; e.g., localization.
        $library_name = "$extension/$name";
        $this->moduleHandler->alter('library', $definition, $library_name);
        $this->libraryDefinitions[$extension][$name] = $definition;
      }
    }

    return $this->libraryDefinitions[$extension];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraryByName($extension, $name) {
    $extension = $this->getLibrariesByExtension($extension);
    return isset($extension[$name]) ? $extension[$name] : FALSE;
  }

}
