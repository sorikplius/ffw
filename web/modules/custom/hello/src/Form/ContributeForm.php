<?php
/**
 * @file
 * Contains \Drupal\hello\Form\CustomForm.
 */
namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a singe text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */

class ContributeForm extends FormBase {
    /**
     * The state keyvalue collection.
     *
     * @var \Drupal\Core\State\StateInterface
     */
    protected $state;

    /**
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $config_factory;

    /**
     * Constructs a new SiteMaintenanceModeForm.
     *
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The factory for configuration objects.
     */
    public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state) {
        $this->config_factory = $config_factory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory'),
            $container->get('state')
        );
    }

    /**
     * Build the simple form.
     *
     * A build form method constructs an array that defines how markup and
     * other form elements are included in an HTML form.
     *
     * @param array $form
     *   Default form array structure.
     * @param FormStateInterface $form_state
     *   Object containing current form state.
     *
     * @return array
     *   The render array defining the elements of the form.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $curent_site_name = $this->config_factory->get('system.site')->get('name');
        $curent_slogan = $this->config_factory->get('system.site')->get('slogan');
        $curent_maintenance = $this->state->get('system.maintenance_mode');

        // Get the current user
        $user = \Drupal::currentUser();

        // Check for permission
        $current_permision = $user->hasPermission('administer mainainance mode');

//        dsm($current_permision);

        $form['familia_rostik'] = [
            '#type' => 'details',
            '#title' => 'Familia Rostik',
            '#description' => 'lalala',
            '#open' => TRUE,
        ];
        $form['familia_rostik']['rostik'] = [
            '#type' => 'textfield',
            '#title' => 'Rostik',
        ];
        $form['familia_rostik']['rostik2'] = [
            '#type' => 'select',
            '#title' => $this->t('Rostik2'),
            '#options' => [1, 2, 3]
        ];

        $form['site_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Site Name'),
            '#description' => $this->t('Site Name must be at puti 4 characters and not more than 20 in length.'),
            '#required' => TRUE,
            '#default_value' => !empty($curent_site_name) ? $curent_site_name : NULL,
        ];

        $form['slogan'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Slogan'),
            '#description' => $this->t('Slogan must be at least 10 characters in length.'),
            '#required' => TRUE,
            '#default_value' => !empty($curent_slogan) ? $curent_slogan : NULL,
        ];

        if (!$current_permision) {
            $form['maintenance'] = [
                '#type' => 'checkbox',
                '#title' => t('Maintenance want to be at this site ?'),
                '#default_value' => $curent_maintenance,
                '#disabled' => TRUE,
            ];
        }
        else {
            $form['maintenance'] = [
                '#type' => 'checkbox',
                '#title' => t('Maintenance want to be at this site ?'),
                '#default_value' => $curent_maintenance,
            ];
        }

        // Group submit handlers in an actions element with a key of "actions" so
        // that it gets styled correctly, and so that other modules may add actions
        // to the form. This is not required, but is convention.
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save'),
        ];

        return $form;
    }

    /**
     * Getter method for Form ID.
     *
     * The form ID is used in implementations of hook_form_alter() to allow other
     * modules to alter the render array built by this form controller.  it must
     * be unique site wide. It normally starts with the providing module's name.
     *
     * @return string
     *   The unique ID of the form defined by this class.
     */
    public function getFormId() {
        return 'hello_form';
    }

    /**
     * Implements form validation.
     *
     * The validateForm method is the default method called to validate input on
     * a form.
     *
     * @param array $form
     *   The render array of the currently built form.
     * @param FormStateInterface $form_state
     *   Object describing the current state of the form.
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $site_name = $form_state->getValue('site_name');
        $slogan = $form_state->getValue('slogan');
        $curent_site_name = \Drupal::config('system.site')->get('name');
        if ((strlen($site_name) < 4)||(strlen($site_name) > 20)) {
            // Set an error for the form element with a key of "title".
            $form_state->setErrorByName('site_name', $this->t('Site Name must be between 4 and 20 characters.'));
        }
        if (strlen($slogan) > 10) {
            $form_state->setErrorByName('slogan', $this->t('Slogan must not be greater than 10 characters.'));
        }
        if ($site_name == $curent_site_name){
            $form_state->setErrorByName('site_name', $this->t('Site Name was not changed.'));
        }
    }

    /**
     * Implements a form submit handler.
     *
     * The submitForm method is the default method called for any submit elements.
     *
     * @param array $form
     *   The render array of the currently built form.
     * @param FormStateInterface $form_state
     *   Object describing the current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        /*
         * This would normally be replaced by code that actually does something
         * with the title.
         */
        $site_name = $form_state->getValue('site_name');
        \Drupal::configFactory()->getEditable('system.site')->set('name', $site_name)->save();

        $slogan = $form_state->getValue('slogan');
        \Drupal::configFactory()->getEditable('system.site')->set('slogan', $slogan)->save();

        $maintenance = $form_state->getValue('maintenance');
        $this->state->set('system.maintenance_mode', $maintenance);


//        drupal_set_message(t('You specified a Site Name of %site_name.', ['%site_name' => $site_name]));
//
//        $slogan = $form_state->getValue('slogan');
//        drupal_set_message(t('You specified a Slogan of %slogan.', ['%slogan' => $slogan]));
//
//        $maintenance = boolval($form_state->getValue('maintenance'));
//        if ($maintenance == 1){
//            drupal_set_message(t('You specified a maintenance of TRUE'));
//        }
//        else    {
//            drupal_set_message(t('You specified a maintenance of FALSE'));
//        }

    }


}



