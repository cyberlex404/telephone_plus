<?php

namespace Drupal\telephone_plus\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'telephone_plus_widget' widget.
 *
 * @FieldWidget(
 *   id = "telephone_plus_widget",
 *   label = @Translation("Telephone plus widget"),
 *   field_types = {
 *     "telephone_plus"
 *   }
 * )
 */
class TelephonePlusWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'size' => 60,
      'placeholder' => '',
      'default_count' => 6,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = array(
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    );
    $elements['placeholder'] = array(
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    );

    $elements['default_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Default count'),
      '#default_value' => $this->getSetting('default_count'),
      '#empty_value' => '',
      '#min' => 1
    ];


    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: !size', array('!size' => $this->getSetting('size')));
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', array('@placeholder' => $this->getSetting('placeholder')));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = [];

    $element['telephone'] = $element + array(
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
    );

    $element['operator'] = array(
      '#type' => 'select',
      '#title' => t('Operator'),
      '#options' => $this->getOperators(),
      '#empty_option' => 'no-operator',
      '#empty_value' => t('Operator not set'),
      '#default_value' => isset($items[$delta]->operator) ? $items[$delta]->operator : NULL,
    );

    $element['whatsapp'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Whatsapp'),
      '#default_value' => isset($items[$delta]->whatsapp) ? $items[$delta]->whatsapp : FALSE,
    );
    $element['viber'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Viber'),
      '#default_value' => isset($items[$delta]->viber) ? $items[$delta]->viber : FALSE,
    );
    $element['telegram'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Telegram'),
      '#default_value' => isset($items[$delta]->telegram) ? $items[$delta]->telegram : FALSE,
    );

    return $element;
  }


  /**
   * Returns the array of options for the widget.

   * @return array
   *   The array of options for the widget.
   */
  protected function getOperators() {
    $operators = [
      '_none' => t('None'),
    ];
    $string_operators = $this->getFieldSetting('allowed_operators');
    $lines = explode("\n", $string_operators);
    foreach ($lines as $line) {
      list($key, $value) = explode("|", $line);
      $operators[$key] = $value;
    }
    return $operators;
  }

  /**
   * Determines selected options from the incoming field values.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values.
   *
   * @return array
   *   The array of corresponding selected options.
   */
  /*
  protected function getSelectedOptions(FieldItemListInterface $items) {
    // We need to check against a flat list of options.
    $flat_options = OptGroup::flattenOptions($this->getOptions($items->getEntity()));

    $selected_options = array();
    foreach ($items as $item) {
      $value = $item->{$this->column};
      // Keep the value if it actually is in the list of options (needs to be
      // checked against the flat list).
      if (isset($flat_options[$value])) {
        $selected_options[] = $value;
      }
    }

    return $selected_options;
  }
*/

}
