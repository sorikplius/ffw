<?php
/**
 * @file
 * Contains \Drupal\my_new_page\Form\ContributePageForm.
 */
namespace Drupal\my_new_page\Form;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;

class ContributePageForm implements FormInterface {
    use StringTranslationTrait;

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        // TODO: Implement getFormId() method.
        return 'book_update_form';
    }

    /**
     * Gets all the node titles for a specific CT.
     */
    private static function getAllNodeTitles($node_type) {
        $nids = \Drupal::entityQuery('node')
            ->condition('type', $node_type)
            ->execute();
        $nodes = Node::loadMultiple($nids);
        $node_titles = [];
        foreach ($nodes as $nid => $node) {
            $node_titles[$nid] = $node->getTitle() . ' (' . $nid . ')';
        }
        return $node_titles;
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
        // TODO: Implement buildForm() method.
        $form['book_title'] = [
            '#type' => 'select',
            '#title' => 'Book Title',
            '#options' => self::getAllNodeTitles('book')
        ];
        $form['book_status'] = [
            '#type' => 'checkbox',
            '#title' => 'Publish',
            '#description' => 'Check this to publish the node.'
        ];
        $form['book_sticky'] = [
            '#type' => 'checkbox',
            '#title' => 'Sticky',
            '#description' => 'Check this to make the node sticky.'
        ];

        $form['actions'] = [
            '#type' => 'actions'
        ];
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => 'Update Node',
        );
        $form['actions']['delete'] = array(
            '#type' => 'submit',
            '#value' => 'Delete',
        );

        return $form;
    }

    /**
     * Form validation handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement validateForm() method.
        // ...
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $nid = $form_state->getValue('book_title');
        $node = Node::load($nid);
        if ($form_state->getValue('op') == 'Update Node') {
            $status = $form_state->getValue('book_status');
            $sticky = $form_state->getValue('book_sticky');
            self::updateNodeStatus($node, $status);
            self::updateNodeSticky($node, $sticky);
            $node->save();

            // kint($var);
            // die();
        }
        else {
            // Normal, drupal way.
            $form_state->setRedirect('my.new.page.content_delete', ['node' => $nid]);
            // URGENT

        }
    }

    /**
     * @param $node Node
     * @param $status bool
     */
    private static function updateNodeStatus($node, $status) {
        $node->set('status', $status);
    }

    /**
     * @param $node Node
     * @param $sticky bool
     */
    private static function updateNodeSticky($node, $sticky) {
        $node->setSticky($sticky);
    }
}
