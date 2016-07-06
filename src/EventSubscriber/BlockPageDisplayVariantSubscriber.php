<?php
/**
 * @file
 * block_region_injector
 * BlockPageDisplayVariantSubscriber.php
 *
 * Created by Jake Wise 03/06/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
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
