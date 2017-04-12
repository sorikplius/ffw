<?php

namespace Drupal\my_new_page\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

class ContributePageFormConfirm extends ConfirmFormBase
{
    protected $title;

    /** @var  Node */
    protected $node;

    /**
     * Returns the question to ask the user.
     *
     * @return string
     *   The form question. The page title will be set to this value.
     */
    public function getQuestion()
    {
        // TODO: Implement getQuestion() method.
        return t('Do you want to delete %title?', array('%title' => $this->title));
    }

    /**
     * Returns the route to go to if the user cancels the action.
     *
     * @return \Drupal\Core\Url
     *   A URL object.
     */
    public function getCancelUrl()
    {
        // TODO: Implement getCancelUrl() method.
        return new Url('my.new.page.content');
    }

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        // TODO: Implement getFormId() method.
        return 'book_update_form_delete';
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    /**
     * {@inheritdoc}
     */
    /**
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->node->delete();
//        $node = \Drupal::routeMatch()->getParameter('node');
//        if ($node) {
////             You can get nid and anything else you need from the node object.
//            $nid = $node->nid->value;
//            Node::load($nid)->delete();
//
//
//        }

        drupal_set_message($this->t('The book "@rostik" (@nid) was successfully deleted.', array(
            '@rostik' => $this->node->getTitle(),
            '@nid' => $this->node->nid->value,
        )));
        $form_state->setRedirect('my.new.page.content');
    }


    /**
     * {@inheritdoc}
     *
     * @param int $id
     *   (optional) The ID of the item to be deleted.
     */
    public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
        $this->title = $node->getTitle();
        $this->node = $node;
        return parent::buildForm($form, $form_state);

    }
}