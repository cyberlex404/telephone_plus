<?php

namespace Drupal\telephone_plus\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'telephone_plus_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "telephone_plus_formatter",
 *   label = @Translation("Telephone plus formatter"),
 *   field_types = {
 *     "telephone_plus"
 *   }
 * )
 */
class TelephonePlusFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      // Implement default settings.
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(
      // Implement settings form.
    ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $line = '<i class="telico telico-op telico-' . $item->operator .'"></i>';
    foreach (['whatsapp', 'viber', 'telegram'] as $param) {
      if((bool)$item->$param == TRUE) {
        $line .= '<i class="telico telico-' . $param .'"></i>';
      }
    }
  //  dpm($line);
    $markup['telephone'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $item->telephone,
      '#prefix' =>'<span class="telephone-icons">',
      '#suffix' => $line . '</span>',
      '#attributes' => [
        'itemprop' => 'telephone',
        'class' => ['telephone'],
        'telephone' => $item->telephone,
      ],
    ];
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return render($markup); // nl2br(Html::escape($item->telephone));
  }

}
