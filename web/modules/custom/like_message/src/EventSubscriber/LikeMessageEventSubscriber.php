<?php
/**
 * @file
 * Contains \Drupal\example_events\ExampleEventSubScriber.
 */
namespace Drupal\like_message\EventSubscriber;
use Drupal\like_dislike\LikeDislikeEvent;
use Drupal\user\UserDataInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * Class ExampleEventSubScriber.
 *
 * @package Drupal\example_events
 */
class LikeMessageEventSubscriber implements EventSubscriberInterface {
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        $events[LikeDislikeEvent::LIKE][] = array('showMessage');
        $events[LikeDislikeEvent::DISLIKE][] = array('showMessage');
        return $events;
    }

    function showMessage(LikeDislikeEvent $event) {
        /** @var UserDataInterface $userData */
        $userData = \Drupal::service('user.data');
        $show = $userData->get('like_message',$event->user->id(), 'show_like_message');

        if ($show) {
            $username = $event->user->getAccountName();
            $action = ($event->value == 1) ? 'liked' : 'disliked' ;
            $content_type = $event->node->bundle();
            $title = $event->node->getTitle();
            drupal_set_message(t('User <strong>@username</strong> has <em>@action</em> the @content_type "@title".', [
                '@username' => $username,
                '@action' => $action,
                '@content_type' => $content_type,
                '@title' => $title,
            ]));
        }
    }
}