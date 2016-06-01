<?php

/**
 * @file
 * Contains Drupal\timetable_field\Plugin\Field\FieldType\TimetableItem.
 */

namespace Drupal\timetable_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'timetable_field_timetable' field type.
 *
 * @FieldType(
 *   id = "timetable_field_timetable",
 *   label = @Translation("Timetable"),
 *   module = "timetable_field",
 *   description = @Translation("Simple timetable field."),
 *   default_widget = "timetable_field_default_widget",
 *   default_formatter = "timetable_field_default_formatter"
 * )
 */
class TimetableItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'start'     => array(
          'type'     => 'int',
          'not null' => TRUE,
          'default'  => 0,
        ),
        'end'       => array(
          'type'     => 'int',
          'not null' => TRUE,
          'default'  => 0,
        ),
        'sunday'    => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'monday'    => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'tuesday'   => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'wednesday' => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'thursday'  => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'friday'    => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
        'saturday'  => array(
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $start = $this->get('start')->getValue();
    $end = $this->get('end')->getValue();

    return $start === NULL || $start === '' || $end === NULL || $end === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['start'] = DataDefinition::create('integer')
      ->setLabel(t('Start time'));
    $properties['end'] = DataDefinition::create('integer')
      ->setLabel(t('End time'));
    $properties['sunday'] = DataDefinition::create('string')
      ->setLabel(t('Sunday'));
    $properties['monday'] = DataDefinition::create('string')
      ->setLabel(t('Monday'));
    $properties['tuesday'] = DataDefinition::create('string')
      ->setLabel(t('Tuesday'));
    $properties['wednesday'] = DataDefinition::create('string')
      ->setLabel(t('Wednesday'));
    $properties['thursday'] = DataDefinition::create('string')
      ->setLabel(t('Thursday'));
    $properties['friday'] = DataDefinition::create('string')
      ->setLabel(t('Friday'));
    $properties['saturday'] = DataDefinition::create('string')
      ->setLabel(t('Saturday'));

    return $properties;
  }
}
