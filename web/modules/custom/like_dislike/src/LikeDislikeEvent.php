<?php

namespace Drupal\like_dislike;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class LikeDislikeEvent extends Event {
    const LIKE = 'event.like';
    const DISLIKE = 'event.dislike';

    public $node;
    public $user;
    public $value;

    public function __construct(Node $node, $user, $value)
    {
        $this->node = $node;
        $this->user = $user;
        $this->value = $value;
    }
}