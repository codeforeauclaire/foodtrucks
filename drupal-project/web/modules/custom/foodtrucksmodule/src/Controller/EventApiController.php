<?php

/**
 * @file
 * Contains Drupal\foodtrucksmodule\Controller\EventApiController.
 */

namespace Drupal\foodtrucksmodule\Controller;

use Drupal\Core\Entity\Entity as CEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\foodtrucksmodule\JsonApiProcessor;

/**
 * Class EventApiController.
 *
 * @package Drupal\foodtrucksmodule\Controller
 */
class EventApiController {
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
    $value = 'food_truck_event_scheduled';
    if ($date) {
      $query = \Drupal::entityQuery('node')
        ->condition($key, $value)
        ->condition('field_event_date.value', $date);
    }else{
      $query = \Drupal::entityQuery('node')
        ->condition($key, $value);
    }
    $results = $query->execute();
    foreach ($results as $result) {
      JsonApiProcessor::processEvent($data, $result);
      if (isset($data[0])) {
        $dataArray[] = $data;
        $data = '';
      }
    }
    return new JsonResponse($dataArray);
  }
}
