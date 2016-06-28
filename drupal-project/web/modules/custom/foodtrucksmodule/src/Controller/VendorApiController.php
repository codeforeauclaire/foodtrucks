<?php

/**
 * @file
 * Contains Drupal\foodtrucksmodule\Controller\VendorApiController.
 */

namespace Drupal\foodtrucksmodule\Controller;

use Drupal\Core\Entity\Entity as CEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\foodtrucksmodule\JsonApiProcessor;
use Drupal\Component\Utility\UrlHelper;

/**
 * Class VendorApiController.
 *
 * @package Drupal\foodtrucksmodule\Controller
 */
class VendorApiController {
  /**
   * Action.
   *
   * @return json
   *   Return json.
   */
  public function handleRequest($date = null) {
    $dataArray = [];
    $data = [];
    $key = 'type';
    $value = 'food_trucks';
    $query = \Drupal::entityQuery('node')
      ->condition($key, $value);
    $nids = $query->execute();
    foreach ($nids as $nid) {
      JsonApiProcessor::processVendor($data, $nid);
      $this->processFoodtruckArray($data);

      $dataArray[] = $data;
      $data = '';
    }
    return new JsonResponse($dataArray);
  }

  public static function processFoodtruckArray(&$data) {
    if (isset($data['website_url']) && isset($data['website_url_scheme'])) {
      ($data['website_url_scheme']) ? $scheme = 'https://' : $scheme = 'http://';
      $data['website_url'] = $scheme . $data['website_url'];
      if (!UrlHelper::isValid($data['website_url'], TRUE )) {
        unset($data['website_url']);
      }
    }
    unset($data['website_url_scheme']);
    if (isset($data['facebook_url']) && isset($data['facebook_url_scheme'])) {
      ($data['facebook_url_scheme']) ? $scheme = 'https://' : $scheme = 'http://';
      $data['facebook_url'] = $scheme . $data['facebook_url'];
      if (!UrlHelper::isValid($data['facebook_url'], TRUE )) {
        unset($data['facebook_url']);
      }
    }
    unset($data['facebook_url_scheme']);
    if (isset($data['twitter_name']) && !strstr($data['twitter_name'], '@' )) {
      $data['twitter_name'] = '@' . $data['twitter_name'];
    }
    if (isset($data['foodtruck'])) {
      unset($data['foodtruck']);
    }
  }
}

