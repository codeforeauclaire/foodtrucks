<?php
/**
 * @file
 *   Contains Drupal\geolocation\GeoCoreInjectionTrait.
 */

namespace Drupal\geolocation;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait GeoCoreInjectionTrait.
 *
 * @package \Drupal\geolocation
 */
trait GeoCoreInjectionTrait {

  /**
   * The geolocaiton core service.
   *
   * @var \Drupal\geolocation\GeolocationCore
   */
  protected $geolocation_core;

  /**
   * Constructs a new Date instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @param \Drupal\geolocation\GeolocationCore
   *   The geolocation core helper.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GeolocationCore $geolocation_core) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->geolocation_core = $geolocation_core;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geolocation.core')
    );
  }

}
