<?php

/**
* @file
* Contains \Drupal\loan_amortization\Form\LoanAmortizationValuesSettingsForm.
*/

namespace Drupal\loan_amortization\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure example settings for this site.
 */
class LoanAmortizationValuesSettingsForm extends ConfigFormBase {
  /** @var string Config settings */
  const SETTINGS = 'loan_amortization_values.settings';

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new LoanAmortizationValuesSettingsForm object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'loan_amortization_values_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $languages  = $this->languageManager->getLanguages();
    foreach ($languages as $language){
      $lang_id = $language->getId();
      $lang_name = $language->getName();

      $form['loan_amount' . $lang_id] = [
        '#type' => 'number',
        '#title' => $this->t('Loan Amount'),
        '#default_value' => $config->get($lang_id)['loan_amount'],
        '#prefix' => '<h1>' . $lang_name . '</h1>',
      ];

      $form['annual_rate' . $lang_id] = [
        '#type' => 'number',
        '#title' => $this->t('Annual Interest Rate'),
        '#default_value' => $config->get($lang_id)['annual_rate'],
      ];

      $form['loan_period' . $lang_id] = [
        '#type' => 'number',
        '#title' => $this->t('Loan Period in Year'),
        '#default_value' => $config->get($lang_id)['loan_period'],
      ];

      $form['annual_payments' . $lang_id] = [
        '#type' => 'number',
        '#title' => $this->t('Number of Payments Per Year'),
        '#default_value' => $config->get($lang_id)['annual_payments'],
      ];

      $form['start_of_loan' . $lang_id] = [
        '#type' => 'date',
        '#title' => $this->t('Start Date of Loan'),
        '#default_value' => $config->get($lang_id)['start_of_loan'],
      ];

      $form['extra_payment' . $lang_id] = [
        '#type' => 'number',
        '#title' => $this->t('Optional Extra Payments'),
        '#default_value' => $config->get($lang_id)['extra_payment']
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $languages  = $this->languageManager->getLanguages();
    foreach ($languages as $language){
      $lang = $language->getId();
      $config_data[$lang] = [
        'loan_amount' => $form_state->getValue('loan_amount' . $lang),
        'annual_rate' => $form_state->getValue('annual_rate' . $lang),
        'loan_period' => $form_state->getValue('loan_period' . $lang),
        'annual_payments' => $form_state->getValue('annual_payments' . $lang),
        'start_of_loan' => $form_state->getValue('start_of_loan' . $lang),
        'extra_payment' => $form_state->getValue('extra_payment' . $lang),
      ];
    }

    $this->configFactory->getEditable(static::SETTINGS)->setData($config_data)
      ->save();
    parent::submitForm($form, $form_state);
  }
}