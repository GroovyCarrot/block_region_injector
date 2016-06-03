<?php
/**
 * @file
 * BlockPageDisplayVariantSubscriber implementation.
 */

namespace Drupal\block_region_injector\EventSubscriber;

use Drupal\Core\Render\PageDisplayVariantSelectionEvent;

/**
 * Class BlockPageDisplayVariantSubscriber
 * @package Drupal\block_region_injector
 *
 * Selects the block page display variant.
 */
class BlockPageDisplayVariantSubscriber extends \Drupal\block\EventSubscriber\BlockPageDisplayVariantSubscriber {

  /**
   * {@inheritdoc}
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    $event->setPluginId('block_injected_page');
  }

}
