<?php
declare(strict_types = 1);

namespace Drupal\hbkcolissimochrono\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelSize;
use Drupal\hbkcolissimochrono\Services\Api\Ressources\LabelFormat;

/**
 * Configure hbk colissimo chrono settings for this site.
 */
final class SettingsForm extends ConfigFormBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'hbkcolissimochrono_settings';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'hbkcolissimochrono.settings'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('hbkcolissimochrono.settings');
    $form['app_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Clée de l'api"),
      '#default_value' => $config->get('app_key'),
      '#required' => true
    ];
    $form['app_mode'] = [
      '#type' => 'select',
      '#title' => $this->t("Mode"),
      '#default_value' => $config->get('app_mode'),
      '#options' => [
        'sandbox' => 'sandbox',
        'prod' => 'production'
      ],
      '#required' => true
    ];
    $form['login'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Login coliShip"),
      '#default_value' => $config->get('login'),
      '#required' => true
    ];
    $form['password'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Password coliShip"),
      '#default_value' => $config->get('password')
    ];
    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t("Format"),
      '#default_value' => $config->get('format'),
      '#options' => [
        LabelFormat::PDF => LabelFormat::PDF,
        LabelFormat::ZPL => LabelFormat::ZPL,
        LabelFormat::DPL => LabelFormat::DPL
      ]
    ];
    $form['size'] = [
      '#type' => 'select',
      '#title' => $this->t("size"),
      '#default_value' => $config->get('size'),
      '#options' => [
        LabelSize::A4 => LabelSize::A4,
        LabelSize::SIZE_10X15 => LabelSize::SIZE_10X15,
        LabelSize::SIZE_10X10 => LabelSize::SIZE_10X10
      ]
    ];
    $form['delais_in_days'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Delais in days"),
      '#default_value' => $config->get('delais_in_days')
    ];
    $form['commercial_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Commercial name"),
      '#default_value' => $config->get('commercial_name')
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('hbkcolissimochrono.settings');
    $config->set('app_key', $form_state->getValue('app_key'));
    $config->set('app_mode', $form_state->getValue('app_mode'));
    $config->set('login', $form_state->getValue('login'));
    $config->set('password', $form_state->getValue('password'));
    $config->set('format', $form_state->getValue('format'));
    $config->set('size', $form_state->getValue('size'));
    $config->set('delais_in_days', $form_state->getValue('delais_in_days'));
    $config->set('commercial_name', $form_state->getValue('commercial_name'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
  
}
