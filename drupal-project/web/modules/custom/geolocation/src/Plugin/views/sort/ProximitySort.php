<?php
/**
 * @file
 *   Definition of Drupal\geolocation\Plugin\views\sort\ProximitySort.
 */

namespace Drupal\geolocation\Plugin\views\sort;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Sort handler for geolocaiton field.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("geolocation_sort_proximity")
 */
class ProximitySort extends SortPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    // Add source, lat, lng and filter.
    return [
      'proximity_field' => ['default' => ''],
    ] + parent::defineOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // Buffer available  filters.
    $valid_feilds = ['--none--' => $this->t('-- None --')];

    // Check for valid filters.
    foreach ($this->view->getHandlers('field', $this->view->current_display) as $delta => $field) {
      if ($field['plugin_id'] === 'geolocation_field_proximity') {
        $valid_feilds[$delta] = $field['id'];
      }
    }
    // Add the Filter selector.
    $form['proximity_field'] = empty($valid_feilds)
      ? ['#markup' => $this->t('There are no proximity fields available in this display.')]
      : [
        '#type' => 'select',
        '#title' => $this->t('Select field.'),
        '#description' => $this->t('Select the field to use for sorting.'),
        '#options' => $valid_feilds,
        '#default_value' => $this->options['proximity_field'],
      ];

    // Add the Drupal\views\Plugin\views\field\Numeric settings to the form.
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Get the field for sorting.
    $field = isset($this->view->field[$this->options['proximity_field']]) ? $this->view->field[$this->options['proximity_field']] : NULL;
    if (!empty($field->field_alias)) {
      $this->query->addOrderBy(NULL, NULL, $this->options['order'], $field->field_alias);
      $this->tableAlias = $field->tableAlias;
    }
  }
}
