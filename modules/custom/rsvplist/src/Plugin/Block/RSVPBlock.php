<?php

/**
 * @file
 * contains \Drupal\rsvplist\Plugin\Block\RSVPBlock
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvplist\Form\RSVPForm;

/**
 * Class RSVPBlock
 *
 * Provides rsvplist block
 * @package Drupal\rsvplist\Plugin\Block
 * @Block(
 *     id="rsvp_block",
 *     admin_label=@Translation("RSVP Block"),
 * )
 */
class RSVPBlock extends BlockBase
{

    /**
     * Builds and returns the renderable array for this block plugin.
     *
     * If a block should not be rendered because it has no content, then this
     * method must also ensure to return no content: it must then only return an
     * empty array, or an empty array with #cache set (with cacheability metadata
     * indicating the circumstances for it being empty).
     *
     * @return array
     *   A renderable array representing the content of the block.
     *
     * @see \Drupal\block\BlockViewBuilder
     */
    public function build()
    {
        return \Drupal::formBuilder()->getForm(RSVPForm::class);
    }

    public function blockAccess(AccountInterface $account)
    {
        $node = \Drupal::routeMatch()->getParameter('node');

        $nid = $node->nid->value;

        if (is_numeric($nid)) {
            return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
        }
        return AccessResult::forbidden();
    }
}