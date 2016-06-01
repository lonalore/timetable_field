<?php

/**
 * @file
 * Contains \Drupal\timetable_field\Plugin\Field\FieldWidget\TimetableDefaultWidget.
 */

namespace Drupal\timetable_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'timetable_field_default_widget' widget.
 *
 * @FieldWidget(
 *   id = "timetable_field_default_widget",
 *   module = "timetable_field",
 *   label = @Translation("Timetable"),
 *   field_types = {
 *     "timetable_field_timetable"
 *   }
 * )
 */
class TimetableDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'timeformat' => '24',
      'timestep'   => '30',
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

    $elements['timestep'] = array(
      '#type'          => 'select',
      '#title'         => $this->t('Timestep'),
      '#options'       => [
        '5'  => $this->t('5 minutes'),
        '10' => $this->t('10 minutes'),
        '15' => $this->t('15 minutes'),
        '30' => $this->t('30 minutes'),
        '60' => $this->t('60 minutes'),
      ],
      '#default_value' => $this->getSetting('timestep'),
      '#required'      => TRUE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $timeformat = $this->getSetting('timeformat');
    $timestep = $this->getSetting('timestep');

    $summary = array();
    $summary[] = $this->t('Time format: @timeformat', array('@timeformat' => $timeformat));
    $summary[] = $this->t('Timestep: @timestep', array('@timestep' => $timestep));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['#theme_wrappers'][] = 'container';
    // $element['#attributes']['class'][] = 'container-inline';

    $element['start'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Start'),
      '#title_display' => 'before',
      '#options'       => [],
      '#default_value' => isset($items[$delta]->start) ? $items[$delta]->start : '',
      '#weight'        => -2,
    ];

    $element['start']['#options'] = $this->generateTimeOptions();

    $element['end'] = [
      '#type'          => 'select',
      '#title'         => $this->t('End'),
      '#title_display' => 'before',
      '#options'       => [],
      '#default_value' => isset($items[$delta]->end) ? $items[$delta]->end : '',
      '#weight'        => -1,
    ];

    $element['end']['#options'] = $this->generateTimeOptions();

    $element['sunday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Sunday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->sunday) ? $items[$delta]->sunday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['monday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Monday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->monday) ? $items[$delta]->monday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['tuesday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Tuesday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->tuesday) ? $items[$delta]->tuesday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['wednesday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Wednesday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->wednesday) ? $items[$delta]->wednesday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['thursday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Thursday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->thursday) ? $items[$delta]->thursday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['friday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Friday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->friday) ? $items[$delta]->friday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    $element['saturday'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Saturday'),
      '#title_display' => 'before',
      '#default_value' => isset($items[$delta]->saturday) ? $items[$delta]->saturday : '',
      '#maxlength'     => 255,
      '#size'          => 20,
    ];

    // Get first day of week.
    $first_day = \Drupal::config('system.date')->get('first_day');

    // Default ordering.
    $days = array(
      0 => 'sunday',
      1 => 'monday',
      2 => 'tuesday',
      3 => 'wednesday',
      4 => 'thursday',
      5 => 'friday',
      6 => 'saturday',
    );

    // Re-order days according to system.date.first_day.
    $sorted_days = array_merge(array_slice($days, $first_day), array_slice($days, 0, $first_day));
    foreach ($sorted_days as $key => $day) {
      $element[$day]['#weight'] = $key;
    }

    return $element;
  }

  /**
   * Helper function to generate options for dropdown time-fields.
   *
   * @return array $options
   */
  public function generateTimeOptions() {
    static $generatedTimeOptions;

    if (empty($generatedTimeOptions)) {
      $format = $this->getSetting('timeformat');
      $timestep = $this->getSetting('timestep');

      $starttime = '00:00';
      $endtime = '00:00';

      $time = new \DateTime($starttime);
      $interval = new \DateInterval('PT' . $timestep . 'M');
      $temptime = $time->format('H:i');

      $generatedTimeOptions[''] = '--:--';

      do {
        $current = $temptime . ':00';
        $parsed = date_parse($current);
        $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
        $generatedTimeOptions[$seconds] = ($format == '24' ? $temptime : date("g:ia", strtotime($temptime)));
        $time->add($interval);
        $temptime = $time->format('H:i');
      } while ($temptime !== $endtime);
    }

    return $generatedTimeOptions;
  }

}
