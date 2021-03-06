<?php

/**
 * @file
 * contains Drupal\rsvplist\Controller\ReportController
 */

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class ReportController extends ControllerBase
{

    public function report()
    {
        $content = [];

        $content['message'] = [
            '#markup' => $this->t('Below is a list of all Event RSVPs including uesrname, email and event name.')
        ];

        $rows = [];

        foreach ($this->load() as $entry) {
            $rows[] = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $entry);
        }

        $content['table'] = [
            '#type' => 'table',
            '#header' => [
                t('Name'),
                t('Event'),
                t('Email'),
            ],
            '#rows' => $rows,
            '#empty' => t('No entries available')
        ];

        $content['#cache']['max-age'] = 0;

        return $content;
    }

    private function load()
    {
        $select = Database::getConnection()->select('rsvplist', 'r');
        $select->join('users_field_data', 'u', 'r.uid = u.uid');
        $select->join('node_field_data', 'n', 'r.nid = n.nid');
        $select->addField('u', 'name', 'username');
        $select->addField('n', 'title');
        $select->addField('r', 'mail');

        $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
        return $entries;
    }
}