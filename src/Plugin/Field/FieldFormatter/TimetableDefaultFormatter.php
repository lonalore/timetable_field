<?php

/**
 * @file
 * Contains \Drupal\timetable_field\Plugin\Field\FieldFormatter\TimetableDefaultFormatter.
 */

namespace Drupal\timetable_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'timetable_field_default_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "timetable_field_default_formatter",
 *   module = "timetable_field",
 *   label = @Translation("Timetable"),
 *   field_types = {
 *     "timetable_field_timetable"
 *   }
 * )
 */
class TimetableDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'timeformat' => '24',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['timeformat'] = array(
      '#type'          => 'select',
      '#title'         => $this->t('Time format'),
      '#options'       => [
        '12' => $this->t('12h format'),
        '24' => $this->t('24h format'),
      ],
      '#default_value' => $this->getSetting('timeformat'),
      '#required'      => TRUE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $timeformat = $this->getSetting('timeformat');

    $summary = array();
    $summary[] = $this->t('Time format: @timeformat', array('@timeformat' => $timeformat));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [
      '#type'       => 'table',
      '#header'     => [
        [
          'data'  => $this->t('Time'),
          'class' => [
            'timetable-header',
          ],
        ]
      ],
      '#rows'       => [],
      '#attributes' => [
        'class' => [
          'timetable-table',
        ],
      ],
    ];

    $first_day = \Drupal::config('system.date')->get('first_day');
    $timezone = $this->getTimezone();

    date_default_timezone_set($timezone);

    $days = array(
      0 => 'Sunday',
      1 => 'Monday',
      2 => 'Tuesday',
      3 => 'Wednesday',
      4 => 'Thursday',
      5 => 'Friday',
      6 => 'Saturday',
    );

    // Re-order days according to system.date.first_day.
    $sorted_days = array_merge(array_slice($days, $first_day), array_slice($days, 0, $first_day));

    // Add header cols.
    foreach ($sorted_days as $sorted_day) {
      $element['#header'][] = [
        'data'  => $this->t($sorted_day),
        'class' => [
          'timetable-header',
        ],
      ];
    }

    // Get selected time-format.
    $format = $this->getSetting('timeformat');

    foreach ($items as $delta => $item) {
      $start = $format == '24' ? date("H:i", $item->start) : date("g:ia", $item->start);
      $end = $format == '24' ? date("H:i", $item->end) : date("g:ia", $item->end);

      $cols = [
        [
          'data' => $start . ' - ' . $end,
          'class' => [
            'timetable-col',
            'timetable-col-time',
          ],
        ],
      ];

      foreach ($sorted_days as $sorted_key => $sorted_day) {
        $lowercase = strtolower($sorted_day);

        $col = [
          'data'  => $item->{$lowercase},
          'class' => [
            'timetable-col',
            'timetable-day-' . $sorted_key,
          ],
        ];

        if (empty($item->{$lowercase})) {
          $col['class'][] = 'timetable-col-empty';
        }

        $cols[] = $col;
      }

      $element['#rows'][] = [
        'data'  => $cols,
        'class' => [
          'timetable-row',
        ],
      ];
    }

    return $element;
  }

  /**
   * Returns a timezone to use as a default.
   *
   * @param bool|TRUE $check_user
   *  Whether or not to check for a user-configured timezone. Defaults to TRUE.
   *
   * @return array|mixed|null|string
   *  The default timezone for a user, if available, otherwise the site.
   */
  public function getTimezone($check_user = TRUE) {
    global $user;

    $config = \Drupal::config('system.date');

    if ($check_user && $config->get('configurable_timezones') && !empty($user->timezone)
    ) {
      return $user->timezone;
    }
    else {
      $default = $config->get('date_default_timezone');
      return empty($default) ? 'UTC' : $default;
    }
  }

}
