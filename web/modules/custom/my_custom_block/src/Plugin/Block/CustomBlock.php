<?php
/**
 * @file
 * Contains \Drupal\hello\Plugin\Block\HelloBlock.
 */

namespace Drupal\my_custom_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Provides a 'Hello' Block
 *
 * @Block(
 *   id = "hello",
 *   admin_label = @Translation("Hello block"),
 * )
 */
class CustomBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {


//        if (\Drupal::routeMatch()->getRouteName() !=  'entity.node.canonical')
//        {
//            return;
//        }

        if (\Drupal::currentUser()->isAnonymous()) {
            return array(
                '#markup' => t('Hello Anonymous!'),
            );
        }
        else {
            $name = \Drupal::currentUser()->getAccountName();
            $node = \Drupal::routeMatch()->getParameter('node');

            if ($node) {
                // View node statistics.
                // 1. Extrage din node, $nid.
                $nid = $node->id();
                // 2. Database select query: extrage valori de statistica pentru $nid dat.

                // 3. Afisheaza valori.
                $query = \Drupal::database()->select('my_custom_block_data', 'mcb');
                $query->fields('mcb', ['count_all', 'count_day', 'timestamp','uid']);
                $query->condition('mcb.nid', $nid);
                $values = $query->execute()->fetchAssoc();


                $count_day = $values['count_day'];
                $timestamp = $values['timestamp'];
                $count_all = $values['count_all'];
                $uid = $values['uid'];
                $user = User::load($uid);
                $username = $user->getAccountName();

                return array(
                    '#cache' => [
                        'max-age' => 0
                    ],
                    'greetings' => [
                        '#markup' => t('Hello @name !', ['@name' => $name]),
                        '#suffix' => '<br/>'
                    ],
                    'count_all' => [
                        '#prefix' => '<br/>',
                        '#markup' => t('All hits: <strong>@count_all</strong>.', ['@count_all' => $count_all]),
                    ],
                    'count_day' => [
                        '#prefix' => '<br/>',
                        '#markup' => t('Today hits: <strong>@count_day</strong>.', ['@count_day' => $count_day]),
                    ],
                    'uid' => [
                        '#prefix' => '<br/>',
                        '#markup' => t('Last user viewed: <strong>@uid</strong>.', ['@uid' => $username]),
                    ],'timestemp' => [
                        '#prefix' => '<br/>',
                        '#markup' => t('Last view: <strong>@timestamp</strong>.', ['@timestamp' => date('H:i:s', $timestamp)]),
                    ]
                );
            }
        }


        return [];
    }
}


