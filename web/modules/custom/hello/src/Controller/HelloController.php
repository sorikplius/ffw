<?php
/**
 * Created by PhpStorm.
 * User: sorik
 * Date: 3/26/2017
 * Time: 5:49 PM
 */
/**
 * @file
 * Contains \Drupal\hello\Controller\HelloController.
*/

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelloController extends ControllerBase {
    public function content($name) {
        return array(
            '#type' => 'markup',
            '#markup' => $this->t('Hello, mister @name!', ['@name' => $name]),
        );
    }
}