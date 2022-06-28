<?php

namespace Drupal\student_pdf\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StudentSettingsForm.
 *
 * @ingroup student_pdf
 */
class StudentSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'students_pdf.settings';

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'student_settings';
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
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the debug setting.
      ->set('debug', $form_state->getValue('debug'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Defines the settings form for Student entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
    $form['student_settings']['#markup'] = $this->t('Settings form for Student entities. Manage field settings here.');
    $form['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable debug mode.'),
      '#description' => $this->t('Includes html document instead pdf file.'),
      '#default_value' => $config->get('debug'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save')
    ];
    return $form;
  }

}
