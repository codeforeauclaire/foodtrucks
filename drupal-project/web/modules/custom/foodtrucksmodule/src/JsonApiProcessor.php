<?php

/**
 * @file
 * Contains \Drupal\foodtrucksmodule\JsonApiProcessor.
 */

namespace Drupal\foodtrucksmodule;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Entity as CEntity;
use Drupal\Core\Field;
use Drupal\File\Entity\File;

/**
 * Class JsonApiProcessor.
 *
 * @package Drupal\foodtrucksmodule\JsonApiProcessor
 */
class JsonApiProcessor extends ControllerBase {

  static public function processNode (&$data, $nid) {
    $nodeObject = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if ($nodeObject == null) { return; }
    /** @var \Drupal\taxonomy\Entity\Term $nodeObject */
    $bundle = $nodeObject->bundle();
    switch ($bundle) {
      case "food_trucks":
        JsonApiProcessor::processVendor($data, $nid);
        break;
      case "food_truck_event_scheduled":
        JsonApiProcessor::processEvent($data, $nid);
        break;
    }
  }

  static public function processVendor(&$data, $nid) {
    $nodeObject = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if ($nodeObject == null) { return; }
    /** @var \Drupal\taxonomy\Entity\Term $nodeObject */
    $fields = $nodeObject->getFields();
    foreach ($fields as $field) {
      $name = JsonApiProcessor::getFieldName($field);
      if (substr($name, 0, strlen('field_')) === 'field_') {
        JsonApiProcessor::processField($data, $field);
      }
      elseif ($name == "uuid") {
        JsonApiProcessor::processField($data, $field);
      }
      elseif ($name == "title") {
        JsonApiProcessor::processField($data, $field);
      }
    }
  }

  static public function processEvent(&$data, $nid) {
    $nodeObject = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if ($nodeObject == null) { return; }
    /** @var \Drupal\taxonomy\Entity\Term $nodeObject */
    $fields = $nodeObject->getFields();
    foreach ($fields as $field) {
      $name = JsonApiProcessor::getFieldName($field);
      if (substr($name, 0, strlen('field_')) === 'field_') {
        JsonApiProcessor::processField($data, $field);
      }
      elseif ($name == "uid") {
//        JsonApiProcessor::processUID($data, $field);
      }
      elseif ($name == "title") {
        JsonApiProcessor::processField($data, $field);
      }
      elseif ($name == "uuid") {
        JsonApiProcessor::processField($data, $field);
      }
    }
  }

  public static function processUUID(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $data[$name] = $value['value'];
    }
  }

  public static function processUID(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $userId = $value['target_id'];
      if ($userId != 1) {
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'food_trucks')
          ->condition('uid', $userId);
        $results = $query->execute();
        foreach ($results as $result) {
          JsonApiProcessor::processNode($data, $result);
        }
      }
    }
  }

  /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $field */
  public static function getFieldName($field) {
    return $field->getFieldDefinition()->getName();
  }

  /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $field **/
  /** @var \Drupal\Core\Field\FieldItemList $field **/
  public static function processField(&$data, $field) {
    $type = $field->getFieldDefinition()->getType();

      switch ($type) {
        case "uuid":
        case "datetime":
        case "integer":
        case "list_integer":
        case "string":
        case "list_string":
        case "string_long":
        case "telephone":
          JsonApiProcessor::processCommon($data, $field);
          break;
        case "link":
          JsonApiProcessor::processLink($data, $field);
          break;
        case "image":
          JsonApiProcessor::processImage($data, $field);
          break;
        case "entity_reference":
          JsonApiProcessor::processEntityReference($data, $field);
          break;
//        case "entity_reference_revisions":
//          JsonApiProcessor::processEntityReferenceRevision($data, $field);
//          break;
        case "geolocation":
          JsonApiProcessor::processGeolocation($data, $field);
          break;
    }
  }

  public static function processKeyName(&$name) {
    $strings = [
      'food_truck_' => '',
      'event_' => '',
      '' => '',
      'field_' => '',
      'workout_' => '',
      'set_' => '',
      'accelerator_' => '',
      'sets' => 'set'
    ];
    foreach ($strings as $key => $value) {
      $name = str_replace($key, $value, $name);
    }
  }

  public static function processGeolocation (&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $data['lat'] = $value['lat'];
      $data['lng'] = $value['lng'];
    }
  }

  public static function processCommon(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $data[$name] = $value['value'];
    }
  }

  public static function processLink(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $data[$name] = $value['uri'];
    }
  }

  public static function processImage(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    foreach ($values as $value) {
      $data[$name] = $GLOBALS['base_url'].'/sites/default/files/'.str_replace('public://','',File::load($value['target_id'])->getFileUri());
    }
  }

  public static function processEntityReference(&$data, $field) {
    $name = JsonApiProcessor::getName($field);
    JsonApiProcessor::processKeyName($name);
    $values = JsonApiProcessor::getValues($field);
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $field */
    $entityType = $field->getEntity()->getEntityType()->id();
    switch ($entityType) {
      case 'node':
        foreach ($values as $value) {
          $nid = $value['target_id'];
          JsonApiProcessor::processVendor($data['foodtruck'], $nid);
        }
        break;
    }
//      default:
//        foreach ($values as $value) {
//          foreach (Term::load($value['target_id'])->getFields() as $termField) {
//            /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $termField */
//            switch ($termField->getName()) {
//              case "field_time_in_seconds":
//                $data[$name] = $termField->getValue()[0]['value'];
//                break;
//              default:
//                $data[$name] = Term::load($value['target_id'])->label();
//                break;
//            }
//          }
//        }
//        break;
//    }
  }
//  public static function processEntityReferenceRevision(&$data, $field) {
//    $name = JsonApiProcessor::getName($field);
//    JsonApiProcessor::processKeyName($name);
//    $values = JsonApiProcessor::getValues($field);
//    $i = 0;
//    foreach ($values as $value) {
//      $i++;
//      foreach (Paragraph::load($value['target_id'])->getFields() as $field) {
//        JsonApiProcessor::processField($data[$name.$i], $field);
//      }
//    }
//  }

//  /** @var \Drupal\node\Entity\Node $object */
//  public static function getNodeFieldValues($field) {
////    $fields['title'] = $object->getTitle(); // drupal node title
//    $fields['type'] = $field->getType(); // content type
//    $fields['uuid'] = $field->uuid();
//    $fields['updated'] = $field->getChangedTime();
//    return $fields;
//  }
//
//
//  /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $object */
//  public static function getFieldItemList($field) {
//    $list['name'] = $field->getFieldDefinition()->getName();
//    $list['type'] = $field->getFieldDefinition()->getType();
//    $list['values'] = $field->getValue();
//    return $list;
//  }

//  public static function getNidArrays($conditions) {
//    $nidArray = [];
//    foreach ($conditions as $condition) {
//      foreach ($condition as $key => $value) {
//        $query = \Drupal::entityQuery('node')
//          ->condition($key, $value);
//        $result = $query->execute();
//        $resultArray = [];
//        foreach ($result as $resultKey => $resultValue) {
//          $resultArray[] = $resultValue;
//        }
//        $label = '';
//        if ($value == '0') {
//          $label = 'unpublished';
//        }
//        if ($value == '1') {
//          $label = 'published';
//        }
//        $nidArray[$label] = $resultArray;
//      }
//    }
//    return $nidArray;
//  }

  /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $field */
  public static function getName($field) {
    return $field->getFieldDefinition()->getName();
  }

  /**
   * TODO: return multiple values if they exist, as with the links field
   */
  /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $field */
  public static function getValues($field) {
    return $field->getValue();
  }

}

