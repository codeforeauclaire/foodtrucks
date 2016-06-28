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

  static public function processFoodtruckArray(&$data) {
    if (isset($data['website_url'])) {
      if (VendorApiController::str_contains($data['website_url'], 'http') != 1) {
        $data['website_url'] = 'http://' . $data['website_url'];
      }
      if (!UrlHelper::isValid($data['website_url'], TRUE )) {
        unset($data['website_url']);
      }
    }
    unset($data['website_url_scheme']);
    if (isset($data['facebook_url'])) {
      $data['facebook_url'] = str_replace($data['facebook_url'], 'http:', 'https:');
      if (VendorApiController::str_contains($data['facebook_url'], 'https:') != 1) {
        $data['facebook_url'] = 'https://' . $data['facebook_url'];
      }
      if (!UrlHelper::isValid($data['facebook_url'], TRUE )) {
        unset($data['facebook_url']);
      }
    }
    unset($data['facebook_url_scheme']);
    if (isset($data['twitter_name']) && !strstr($data['twitter_name'], '@' )) {
      $data['twitter_name'] = '@' . $data['twitter_name'];
    }
    if (isset($data['foodtruck']) || $data['foodtruck'] == null) {
      unset($data['foodtruck']);
    }
  }

  static public function str_contains($haystack, $needle, $ignoreCase = true) {
    if ($ignoreCase) {
      $haystack = strtolower($haystack);
      $needle   = strtolower($needle);
    }
    $needlePos = strpos($haystack, $needle);
    return ($needlePos === false ? false : ($needlePos+1));
  }
}

