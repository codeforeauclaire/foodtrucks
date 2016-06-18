<?php

/**
 * @file
 * Contains Drupal\foodtrucksmodule\Controller\EventApiController.
 */

namespace Drupal\foodtrucksmodule\Controller;

use Drupal\Core\Entity\Entity as CEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\foodtrucksmodule\JsonApiProcessor;
use Drupal\foodtrucksmodule\Controller\VendorApiController;

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
  public function handleRequest($date1 = null, $date2 = null) {
    $dataArray = [];
    $data = [];
    $key = 'type';
    $value = 'food_truck_event_scheduled';
    if ($date1 && $date2) {
      if ($date1 > $date2) {
        $query = \Drupal::entityQuery('node')
          ->condition($key, $value)
          ->condition('field_event_date.value', $date2, '>=')
          ->condition('field_event_date.value', $date1, '<=');
      }else {
        $query = \Drupal::entityQuery('node')
          ->condition($key, $value)
          ->condition('field_event_date.value', $date1, '>=')
          ->condition('field_event_date.value', $date2, '<=');
      }
    }
    elseif ($date1) {
      $query = \Drupal::entityQuery('node')
        ->condition($key, $value)
        ->condition('field_event_date.value', $date1);
    }else{
      $query = \Drupal::entityQuery('node')
        ->condition($key, $value);
    }
    $results = $query->execute();
    foreach ($results as $result) {
      JsonApiProcessor::processEvent($data, $result);
      if (isset($data['foodtruck'])) {
        VendorApiController::processFoodtruckArray($data['foodtruck']);
        $data['start_time'] = $data['date'] .'T'. sprintf("%02d", $data['start_hour']) .':'. sprintf("%02d", $data['start_minute']) .':00';
        $data['end_time'] = $data['date'] .'T'. sprintf("%02d", $data['end_hour']) .':'. sprintf("%02d", $data['end_minute']) .':00';
        unset($data['title']);
        unset($data['date']);
        unset($data['start_hour']);
        unset($data['start_minute']);
        unset($data['end_hour']);
        unset($data['end_minute']);
        $dataArray[] = $data;
        $data = '';
      }
    }
    return new JsonResponse($dataArray);
  }
}
