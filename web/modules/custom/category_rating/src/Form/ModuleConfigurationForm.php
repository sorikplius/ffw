<?php

/**
 * @form
 *
 */
namespace Drupal\category_rating\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'category_rating_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            'category_rating.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
        $config = $this->config('category_rating.settings');
        $form['node_rating'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Node Rating Value'),
            '#default_value' => $config->get('node_rating'),
        );
        $form['comment_rating'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Comment Rating Value'),
            '#default_value' => $config->get('comment_rating'),
        );
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $node_rating = $form_state->getValue('node_rating');
        $comment_rating = $form_state->getValue('comment_rating');

        $config = $this->configFactory()->getEditable('category_rating.settings');
        $config->set('node_rating', $node_rating);
        $config->set('comment_rating', $comment_rating);
        $config->save();
    }

}