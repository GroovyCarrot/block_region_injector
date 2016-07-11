<?php

namespace Drupal\block_region_injector;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

/**
 * Class BlockRegionInjectorServiceProvider.
 *
 * @package Drupal\block_region_injector
 */
class BlockRegionInjectorServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('block.page_display_variant_subscriber');
    $definition->setClass('Drupal\\block_region_injector\\EventSubscriber\\BlockPageDisplayVariantSubscriber');
  }

}
