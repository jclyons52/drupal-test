<?php
/**
 * @file
 * Containes \Drupal\rsvplist\Form\RSVPForm
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class RSVPForm extends FormBase
{
    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'rsvplist_email_form';
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
        $node = \Drupal::routeMatch()->getParameter('node');
        $nid = $node->nid->value;

        $form['email'] = [
            '#title' => t('Email Address'),
            '#type' => 'textfield',
            '#size' => 25,
            '#description' => t("We'll send you updates to the email address you provide." ),
            '#required' => true,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('RSVP'),

        ];

        $form['nid'] = [
            '#type' => 'hidden',
            '#value' => $nid
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $value = $form_state->getValue('email');
        if ($value == !\Drupal::service('email.validator')->isValid($value)) {
            $form_state->setErrorByName('email', t("the email address %mail is not valid", ['%mail' => $value]));
            return;
        }
        $node = \Drupal::routeMatch()->getParameter('node');

        $select = Database::getConnection()->select('rsvplist', 'r');
        $select->fields('r', ['nid']);
        $select->condition('nid', $node->id());
        $select->condition('mail', $value);
        $results = $select->execute();
        if (!empty($results->fetchCol())) {
            $form_state->setErrorByName('email', t('The email address %mail is already subscribed to this list.', [
                '%mail' => $value
            ]));
        }
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
        $user = User::load(\Drupal::currentUser()->id());
        
        Database::getConnection('default')->insert('rsvplist')->fields([
            'mail' => $form_state->getValue('email'),
            'nid' => $form_state->getValue('nid'),
            'uid' => $user->id(),
            'created' => time()
        ])->execute();

        drupal_set_message(t('Thank you for your RSVP. You are on the list for the event'));
    }
}

