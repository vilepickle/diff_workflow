<?php

/**
 * @file
 * Contains \Drupal\diff_workflow\Plugin\Diff\WorkflowFieldBuilder
 */

namespace Drupal\diff_workflow\Plugin\Diff;

use Drupal\diff\FieldDiffBuilderBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

const COMPARE_WORKFLOW_ID = 0;
const COMPARE_WORKFLOW_LABEL = 1;

/**
 * @FieldDiffBuilder(
 *   id = "workflow_field_diff_builder",
 *   label = @Translation("Workflow Field Diff"),
 *   field_types = {
 *     "workflow"
 *   },
 * )
 */
class WorkflowFieldBuilder extends FieldDiffBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(FieldItemListInterface $field_items) {
    $result = array();

    // Every item from $field_items is of type FieldItemInterface.
    foreach ($field_items as $field_key => $field_item) {

      if (!$field_item->isEmpty()) {
        $possible_options = $field_item->getPossibleOptions();
        $values = $field_item->getValue();
        // Compare entity ids.
        if (isset($values['value'])) {
          if ($this->configuration['compare_workflow'] == COMPARE_WORKFLOW_LABEL) {
            $result[$field_key][] = $possible_options[$values['value']];
          }
          else {
            $result[$field_key][] = $this->t('Entity ID: ') . $values['value'];
          }
        }
      }
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['compare_workflow'] = array(
      '#type' => 'select',
      '#title' => $this->t('Compare'),
      '#options' => array(COMPARE_WORKFLOW_ID => t('ID'), COMPARE_WORKFLOW_LABEL => t('Label')),
      '#default_value' => $this->configuration['compare_workflow'],
    );

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['compare_workflow'] = $form_state->getValue('compare_workflow');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $default_configuration = array(
      'compare_workflow' => COMPARE_WORKFLOW_LABEL,
    );
    $default_configuration += parent::defaultConfiguration();

    return $default_configuration;
  }

}
