<?php

/**
 * @file
 * Contains \Drupal\geolocation\Tests\GeolocationViewsStyleCommonMap.
 */

namespace Drupal\geolocation\Tests;

use Drupal\views\Tests\ViewTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;


/**
 * Tests the grid style plugin.
 *
 * @group views
 * @see \Drupal\views\Plugin\views\style\Grid
 */
class GeolocationViewsStyleCommonMap extends ViewTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['node', 'field', 'views', 'geolocation', 'geolocation_test_views'];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['geolocation_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));

    // Add the geolocation field to the article content type.
    entity_create('field_storage_config', array(
      'field_name' => 'field_geolocation_test',
      'entity_type' => 'node',
      'type' => 'geolocation',
    ))->save();
    entity_create('field_config', array(
      'field_name' => 'field_geolocation_test',
      'label' => 'Geolocation',
      'entity_type' => 'node',
      'bundle' => 'article',
    ))->save();

    entity_get_form_display('node', 'article', 'default')
      ->setComponent('field_geolocation_test', array(
        'type' => 'geolocation_latlng',
      ))
      ->save();

    entity_get_display('node', 'article', 'default')
      ->setComponent('field_geolocation_test', array(
        'type' => 'geolocation_latlng',
        'weight' => 1,
      ))
      ->save();

    $this->container->get('views.views_data')->clear();

    ViewTestData::createTestViews(get_class($this), ['geolocation_test_views']);
  }

  /**
   * Tests the CommonMap style.
   */
  public function testCommonMapNoLocations() {
    $this->drupalGet('geolocation-test');
    $this->assertResponse(200);
  }

  /**
   * Tests the CommonMap style.
   */
  public function testCommonMapLocationsEmpty() {
    $entity_test_storage = \Drupal::entityManager()->getStorage('node');
    $entity_test_storage->create(array(
      'id' => 1,
      'title' => 'foo bar baz',
      'body' => 'test test',
      'type' => 'article',
    ))->save();
    $entity_test_storage->create(array(
      'id' => 2,
      'title' => 'foo test',
      'body' => 'bar test',
      'type' => 'article',
    ))->save();
    $entity_test_storage->create(array(
      'id' => 3,
      'title' => 'bar',
      'body' => 'test foobar',
      'type' => 'article',
    ))->save();

    $this->drupalGet('geolocation-test');
    $this->assertResponse(200);
  }

  /**
   * Tests the CommonMap style.
   */
  public function testCommonMapLocations() {
    $entity_test_storage = \Drupal::entityManager()->getStorage('node');
    $entity_test_storage->create([
      'id' => 1,
      'title' => 'foo bar baz',
      'body' => 'test test',
      'type' => 'article',
      'field_geolocation_test' => [
        'lat' => 52,
        'lng' => 47,
      ],
    ])->save();
    $entity_test_storage->create([
      'id' => 2,
      'title' => 'foo test',
      'body' => 'bar test',
      'type' => 'article',
      'field_geolocation_test' => [
        'lat' => 53,
        'lng' => 48,
      ],
    ])->save();
    $entity_test_storage->create([
      'id' => 3,
      'title' => 'bar',
      'body' => 'test foobar',
      'type' => 'article',
      'field_geolocation_test' => [
        'lat' => 54,
        'lng' => 49,
      ],
    ])->save();

    $this->drupalGet('geolocation-test');
    $this->assertResponse(200);
  }
}
