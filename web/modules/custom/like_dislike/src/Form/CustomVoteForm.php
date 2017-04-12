<?php
/**
 * @file
 */
namespace Drupal\like_dislike\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\like_dislike\LikeDislikeEvent;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CustomVoteForm extends FormBase {
    /** @var  Node */
    protected $node;
    /** @var  User */
    protected $user;

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'like_dislike_buttons';
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
    public function buildForm(array $form, FormStateInterface $form_state, $node = NULL)
    {
        $this->node = $node;
        $this->user = \Drupal::currentUser();
        $like_value = $this->getLike();

        $form['#prefix'] = '<div id="like-dislike-buttons-wrapper">';
        $form['#suffix'] = '</div>';
        $form['like_value'] = [
            '#type' => 'submit',
            '#value' => $this->t('Like'),
            '#ajax' => array(
                'event' => 'click',
                'callback' => '::submitForm',
                'method' => 'replace',
                'effect' => 'fade',
                'wrapper' => 'like-dislike-buttons-wrapper',
            ),
        ];
        $form['dislike_value'] = [
            '#type' => 'submit',
            '#value' => $this->t('Dislike'),
            '#ajax' => array(
                'callback' => '::submitForm',
                'event' => 'click',
                'wrapper' => 'like-dislike-buttons-wrapper',
                'method' => 'replace',
                'effect' => 'fade',
            ),
        ];

        if ($like_value['vote_value'] == 1) {
            $form['like_value']['#disabled'] = TRUE;
            $form['message'] = [
                '#markup' => 'This node was liked :)<br/>',
                '#weight' => '-10'
            ];

        }
        elseif ($like_value['vote_value'] == -1) {
            $form['dislike_value']['#disabled'] = TRUE;
            $form['message'] = [
                '#markup' => 'This node was disliked :(<br/>',
                '#weight' => '-10'
            ];
        }
        return $form;
    }

    public function getLike() {
        $query = \Drupal::database()->select('like_dislike', 'ld');
        $query->condition('ld.entity_id', $this->node->id());
        $query->condition('ld.uid', $this->user->id());
        $query->condition('ld.entity_type', 'node');
        $query->addField('ld','vote_value');
        $result = $query->execute()->fetchAssoc();
        return $result;
    }

    public function updateLike($value){
        $result = $this->getLike();
        if ($result === FALSE) {
            $query = \Drupal::database()->insert('like_dislike');
            $query->fields([
                'entity_id' => $this->node->id(),
                'uid' => $this->user->id(),
                'entity_type' => 'node',
                'vote_value' => $value,
            ]);
            $query->execute();
        }
        else {
            $query = \Drupal::database()->update('like_dislike');
            $query->fields([
                'vote_value' => $value,
            ]);
            $query->condition('entity_id', $this->node->id());
            $query->condition('uid', $this->user->id());
            $query->condition('entity_type', 'node');
            $query->execute();
        }
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     * @return array
     *   Ajax renderable array.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $button_value = $form_state->getValue('op');
        /** @var EventDispatcher $dispatcher */
        $dispatcher = \Drupal::service('event_dispatcher');

        if ($button_value == 'Like') {
            $value = 1;
            $this->updateLike($value);

            // LIKE HOOK.
            $event = new LikeDislikeEvent($this->node, $this->user, $value);
            $dispatcher->dispatch(LikeDislikeEvent::LIKE, $event);
        }
        else {
            $value = -1;
            $this->updateLike($value);

            // DISLIKE HOOK.
            $event = new LikeDislikeEvent($this->node, $this->user, $value);
            $dispatcher->dispatch(LikeDislikeEvent::DISLIKE, $event);
        }
        $form_state->setRebuild(TRUE);
        return $form;
    }
}