<?php

namespace Drupal\telephone_plus\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\Field\AllowedTagsXssTrait;

/**
 * Plugin implementation of the 'telephone_plus' field type.
 *
 * @FieldType(
 *   id = "telephone_plus",
 *   label = @Translation("Telephone plus"),
 *   description = @Translation("My Field Type"),
 *   default_widget = "telephone_plus_widget",
 *   default_formatter = "telephone_plus_formatter"
 * )
 */
class TelephonePlus extends FieldItemBase implements OptionsProviderInterface{
  use AllowedTagsXssTrait;
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return array(
      'allowed_operators' => array(),
      'allowed_operators_function' => '',
    ) + parent::defaultStorageSettings();
  }

  /*
  public static function defaultFieldSettings() {
    return array(
      'operators' => [
        'mtsby' => 'MTS.BY',
      ],
    );
  }
*/
  public static function mainPropertyName() {
    return 'telephone';
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Possible Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getPossibleOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return $this->getSettableOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Settable Options may contain group
    // arrays.

    $flatten_options = OptGroup::flattenOptions($this->getSettableOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $allowed_operators = telephone_plus_allowed_operators($this->getFieldDefinition()->getFieldStorageDefinition(), $this->getEntity());
    return $allowed_operators;
  }

  /**
   * Generates a string representation of an array of 'allowed values'.
   *
   * This string format is suitable for edition in a textarea.
   *
   * @param array $values
   *   An array of values, where array keys are values and array values are
   *   labels.
   *
   * @return string
   *   The string representation of the $values array:
   *    - Values are separated by a carriage return.
   *    - Each value is in the format "value|label" or "value".
   */
  protected function allowedOperatorsString($values) {
    $lines = array();
    foreach ($values as $key => $value) {
      $lines[] = "$key|$value";
    }
    return implode("\n", $lines);
  }

  /**
   * {@inheritdoc}
   */
  protected function allowedOperatorsDescription() {
    $description = '<p>' . t('The possible values this field can contain. Enter one value per line, in the format key|label.');
    $description .= '<br/>' . t('The key is the stored value. The label will be used in displayed values and edit forms.');
    $description .= '<br/>' . t('The label is optional: if a line contains a single string, it will be used as key and label.');
    $description .= '</p>';
    $description .= '<p>' . t('Allowed HTML tags in labels: @tags', array('@tags' => $this->displayAllowedTags())) . '</p>';
    return $description;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $allowed_operators = $this->getSetting('allowed_operators');
    $allowed_operators_function = $this->getSetting('allowed_operators_function');
    //TODO : Значение сохраняются в формате строки. Исправить хранение значений доступных операторов в массив
   // dpm($allowed_operators, 'get set');
   // dpm($this->allowedOperatorsString($allowed_operators) , 'def');
    $element['allowed_operators'] = array(
      '#type' => 'textarea',
      '#title' => t('Allowed values list'),
      '#default_value' => $allowed_operators,//$this->allowedOperatorsString($allowed_operators),
      '#rows' => 10,
      '#access' => empty($allowed_operators_function),
      '#element_validate' => array(array(get_class($this), 'validateAllowedValues')),
      '#field_has_data' => $has_data,
      '#field_name' => $this->getFieldDefinition()->getName(),
      '#entity_type' => $this->getEntity()->getEntityTypeId(),
      '#allowed_values' => $allowed_operators,
    );

    $element['allowed_operators']['#description'] = $this->allowedOperatorsDescription();

    $element['allowed_operators_function'] = array(
      '#type' => 'item',
      '#title' => t('Allowed values list'),
      '#markup' => t('The value of this field is being determined by the %function function and may not be changed.', array('%function' => $allowed_operators_function)),
      '#access' => !empty($allowed_operators_function),
      '#value' => $allowed_operators_function,
    );

    return $element;
  }
  /**
   * {@inheritdoc}
   */
  /*
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $gef = 'mtsby|MTS.BY
valcome|Velcom
life|Life:)';

    $elements['allowed_operators'] = array(
      '#type' => 'textarea',
      '#title' => t('Allowed Operators'),
      '#default_value' => $gef,//$this->getSetting('operators'),
      '#description' => t('Allowed Operators list'),
      '#weight' => 1,
    );

    return $elements;
  }
*/

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['telephone'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Telephone'))
      ->setRequired(TRUE);

    $properties['operator'] = DataDefinition::create('string')
      ->setLabel(t('Operator'));

    $properties['whatsapp'] = DataDefinition::create('integer')
      ->setLabel(t('Whatsapp'))
    ;
    $properties['viber'] = DataDefinition::create('integer')
      ->setLabel(t('Viber'));

    $properties['telegram'] = DataDefinition::create('integer')
      ->setLabel(t('Telegram'));



    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = array(
      'columns' => array(
        'telephone' => array(
          'type' => 'varchar',
          'length' => 64,
        ),
        'operator' => array(
          'type' => 'varchar',
          'length' => 32,
        ),
        'whatsapp' => array(
          'type' => 'int',
        ),
        'viber' => array(
          'type' => 'int',
        ),
        'telegram' => array(
          'type' => 'int',
        ),
      ),
    );

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  /*
  public function getConstraints() {
    $constraints = parent::getConstraints();

    if ($max_length = $this->getSetting('max_length')) {
      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', array(
        'value' => array(
          'Length' => array(
            'max' => $max_length,
            'maxMessage' => t('%name: may not be longer than @max characters.', array(
              '%name' => $this->getFieldDefinition()->getLabel(),
              '@max' => $max_length
            )),
          ),
        ),
      ));
    }

    return $constraints;
  }
*/
  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('telephone')->getValue();
    return $value === NULL || $value === '';
  }

}
