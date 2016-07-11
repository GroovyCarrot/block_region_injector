<?php

namespace Drupal\block_region_injector\EventSubscriber;

use Drupal\block\EventSubscriber\BlockPageDisplayVariantSubscriber as OriginalBlockPageDisplayVariantSubscriber;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;

/**
 * Class BlockPageDisplayVariantSubscriber.
 *
 * Selects the block page display variant.
 *
 * @package Drupal\block_region_injector
 */
class BlockPageDisplayVariantSubscriber extends OriginalBlockPageDisplayVariantSubscriber {

  /**
   * {@inheritdoc}
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    $event->setPluginId('block_injected_page');
  }

}
