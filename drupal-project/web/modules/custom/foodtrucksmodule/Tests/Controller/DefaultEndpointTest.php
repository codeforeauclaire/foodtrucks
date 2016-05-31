<?php

/**
 * @file
 * Contains \Drupal\foodtrucksmodule\Tests\EventApiController.
 */

namespace Drupal\foodtrucksmodule\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use Drupal\Core\EventSubscriber\ResponseGeneratorSubscriber;

/**
 * Provides automated tests for the foodtrucksmodule module.
 */
class EventApiControllerTest extends WebTestBase {

  /**
   * Drupal\Component\Serialization\Json definition.
   *
   * @var Drupal\Component\Serialization\Json
   */
  protected $serialization_json;

  /**
   * GuzzleHttp\Client definition.
   *
   * @var GuzzleHttp\Client
   */
  protected $http_client;

  /**
   * Drupal\Core\EventSubscriber\ResponseGeneratorSubscriber definition.
   *
   * @var Drupal\Core\EventSubscriber\ResponseGeneratorSubscriber
   */
  protected $response_generator_subscriber;
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "foodtrucksmodule EventApiController's controller functionality",
      'description' => 'Test Unit for module foodtrucksmodule and controller EventApiController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests transform_json functionality.
   */
  public function testDefaultEndpoint() {
    // Check that the basic functions of module transform_json.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
