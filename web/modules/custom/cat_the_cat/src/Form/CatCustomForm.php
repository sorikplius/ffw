<?php
/**
 * @file
 */

namespace Drupal\cat_the_cat\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;

class CatCustomForm extends FormBase {

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'cat_custom_form';
    }

    /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $uri = 'http://thecatapi.com/api/images/get';
        $client = \Drupal::httpClient();
        $response = $client->get($uri, [
            'query' => [
                'format' => 'xml',
                'type' => 'gif',
                'results_per_page' => '5',
            ]
        ]);
        $data = (string) $response->getBody();
        $data_php = new SimpleXMLElement($data);
        $form['cats'] = [
            '#type' => 'container',
        ];

        foreach ($data_php->data->images->image as $image) {
            $form['cats']['cat_data']['cat_' . (string) $image->id] = [
                '#markup' => $this->t('<a href="http://thecatapi.com"><img src="@url"></a>', [
                    '@url' => (string) $image->url
                ]),
                '#suffix' => '<br/>'
            ];
        }
        $form['actions'] = [
            '#type' => 'actions'
        ];
        $form['actions']['load'] = [
            '#type' => 'button',
            '#value' => 'Load more cats!',
            '#ajax' => [
                'callback' => '::submitForm',
                'wrapper' => 'edit-cats',
                'method' => 'append',
                'effect' => 'slide',
            ]
        ];
        return $form;
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     * @return array
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        return $form['cats']['cat_data'];
    }
}