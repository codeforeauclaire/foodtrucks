<?php

/**
 * @file
 * Contains \Drupal\geolocation\Plugin\views\style\CommonMap.
 */

namespace Drupal\geolocation\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SortArray;


/**
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "maps_common",
 *   title = @Translation("Geolocation - CommonMap"),
 *   help = @Translation("Display geolocations on a common map."),
 *   theme = "views_view_list",
 *   display_types = {"normal"},
 * )
 */
class CommonMap extends StylePluginBase {

  protected $usesFields = TRUE;
  protected $usesRowPlugin = TRUE;
  protected $usesRowClass = FALSE;
  protected $usesGrouping = FALSE;

  /**
   * Overrides \Drupal\views\Plugin\views\display\PathPluginBase::render().
   */
  public function render() {

    if (!empty($this->options['geolocation_field'])) {
      $geo_field = $this->options['geolocation_field'];
      $this->view->field[$geo_field]->options['exclude'] = TRUE;
    }
    else {
      // TODO: Throw some exception here, we're done.
      return [];
    }

    if (!empty($this->options['title_field'])) {
      $title_field = $this->options['title_field'];
      $this->view->field[$title_field]->options['exclude'] = TRUE;
    }

    $id = \Drupal\Component\Utility\Html::getUniqueId($this->pluginId);
    $build = [
      '#theme' => 'geolocation_common_map_display',
      '#id' => $id,
      '#attached' => [
        'library' => [
          'geolocation/geolocation.commonmap',
        ],
        'drupalSettings' => [
          'geolocation' => [
            'commonMap' => [
              'id' => $id,
            ],
          ],
        ],
      ],
    ];

    foreach ($this->view->result as $row) {
      if (!empty($title_field)) {
        $title_field_handler = $this->view->field[$title_field];
        $title_build = array(
          '#theme' => $title_field_handler->themeFunctions(),
          '#view' => $title_field_handler->view,
          '#field' => $title_field_handler,
          '#row' => $row,
        );
      }

      $geo_items = $this->view->field[$geo_field]->getItems($row);
      foreach ($geo_items as $delta => $item) {
        $geolocation = $item['raw'];
        $position = [
          'lat' => $geolocation->lat,
          'lng' => $geolocation->lng,
        ];

        $build['#locations'][] = [
          '#theme' => 'geolocation_common_map_location',
          '#content' => $this->view->rowPlugin->render($row),
          '#title' => empty($title_build) ? '' : $title_build,
          '#position' => $position,
        ];


      }
    }

    $centre = NULL;
    foreach ($this->options['centre'] as $id => $option) {
      if (empty($option['enable'])) {
        continue;
      }

      switch ($id) {
        case 'fixed_value':
          $centre = [
            'lat' => (float)$option['settings']['latitude'],
            'lng' => (float)$option['settings']['longitude'],
          ];
          break;

        case (preg_match('/proximity_filter_*/', $id) ? true : false) :
          $filter_id = substr($id, 17);
          $handler = $this->displayHandler->getHandler('filter', $filter_id);
          if ($handler->value['lat'] && $handler->value['lng']) {
            $centre = [
              'lat' => (float) $handler->value['lat'],
              'lng' => (float) $handler->value['lng'],
            ];
          }
          break;

        case 'first_row':
          if (!empty($build['#locations'][0]['#position'])) {
            $centre = $build['#locations'][0]['#position'];
          }
          break;

      }

      if (!empty($centre['lat']) || !empty($centre['lng']) || !empty($centre['locate'])) {
        // We're done, no need for further options.
        break;
      }
    }

    if (!empty($centre)) {
      $build['#centre'] = $centre;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['geolocation_field'] = ['default' => ''];
    $options['title_field'] = ['default' => ''];
    $options['centre'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $labels = $this->displayHandler->getFieldLabels();
    $fieldMap = \Drupal::entityManager()->getFieldMap();
    $geo_options = [];
    $title_options = [];
    $filters = $this->displayHandler->getOption('filters');
    $fields = $this->displayHandler->getOption('fields');
    foreach ($fields as $field_name => $field) {
      if ($field['plugin_id'] == 'geolocation_field') {
        $geo_options[$field_name] = $labels[$field_name];
      }
      if (
        $field['plugin_id'] == 'field'
        && !empty($field['entity_type'])
        && !empty($field['entity_field'])
      ) {
        if (
          !empty($fieldMap[$field['entity_type']][$field['entity_field']]['type'])
          && $fieldMap[$field['entity_type']][$field['entity_field']]['type'] == 'geolocation'
        ) {
          $geo_options[$field_name] = $labels[$field_name];
        }
      }
      if ($field['type'] == 'string') {
        $title_options[$field_name] = $labels[$field_name];
      }
    }
    $form['geolocation_field'] = [
      '#title' => $this->t('Geolocation source field'),
      '#type' => 'select',
      '#default_value' => $this->options['geolocation_field'],
      '#description' => $this->t("The source of geodata for each entity."),
      '#options' => $geo_options,
    ];

    $form['title_field'] = [
      '#title' => $this->t('Title source field'),
      '#type' => 'select',
      '#default_value' => $this->options['title_field'],
      '#description' => $this->t("The source of the title for each entity. Must be string"),
      '#options' => $title_options,
    ];

    $options = [
      'first_row' => $this->t('Use first row as centre.'),
      'fixed_value' => $this->t('Provide fixed latitude and longitude.'),
    ];

    foreach ($filters as $filter_name => $filter) {
      if (empty($filter['plugin_id']) || $filter['plugin_id'] != 'geolocation_filter_proximity') {
        continue;
      }
      $options['proximity_filter_' . $filter_name] = $this->displayHandler->getHandler('filter', $filter_name)->adminLabel();
    }

    $form['centre'] = [
      '#type' => 'table',
      '#header' => [ t('Enable'), t('Option'), t('settings'), array('data' => t('Settings'), 'colspan' => '1')],
      '#attributes' => ['id' => 'geolocation-centre-options'],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'geolocation-centre-option-weight',
        ],
      ],
    ];

    foreach ($options as $id => $label) {
      $weight = $this->options['centre'][$id]['weight'] ?: 0;
      $form['centre'][$id]['#weight'] = $weight;

      $form['centre'][$id]['enable'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($this->options['centre'][$id]['enable']) ? $this->options['centre'][$id]['enable'] : TRUE,
      ];

      $form['centre'][$id]['option'] = [
        '#markup' => $label,
      ];

      // Optionally, to add tableDrag support:
      $form['centre'][$id]['#attributes']['class'][] = 'draggable';
      $form['centre'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @option', ['@option' => $label]),
        '#title_display' => 'invisible',
        '#size' => 4,
        '#default_value' => $weight,
        '#attributes' => ['class' => ['geolocation-centre-option-weight']],
      ];
    }

    $form['centre']['fixed_value']['settings'] = [
      '#title' => $this->t('Fixed values for centre'),
      '#type' => 'container',
      'latitude' => [
        '#type' => 'textfield',
        '#title' => t('Latitude'),
        '#default_value' => $this->options['centre']['fixed_value']['settings']['latitude'],
        '#size' => 60,
        '#maxlength' => 128,
      ],
      'longitude' => [
        '#type' => 'textfield',
        '#title' => t('Longitude'),
        '#default_value' => $this->options['centre']['fixed_value']['settings']['longitude'],
        '#size' => 60,
        '#maxlength' => 128,
      ],
      '#description' => $this->t("The source of geodata for each entity. Must be string"),
      '#states' => [
        'visible' => [
          ':input[name="style_options[centre][fixed_value][enable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    uasort($form['centre'], 'Drupal\Component\Utility\SortArray::sortByWeightProperty');
  }
}
