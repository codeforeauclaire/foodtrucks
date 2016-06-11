<?php

/**
 * @file
 * Contains Drupal\foodtrucksmodule\Controller\VendorApiController.
 */

namespace Drupal\foodtrucksmodule\Controller;

use Drupal\Core\Entity\Entity as CEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\foodtrucksmodule\JsonApiProcessor;

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
      $dataArray[] = $data;
      $data = '';
    }
    return new JsonResponse($dataArray);
  }

}

