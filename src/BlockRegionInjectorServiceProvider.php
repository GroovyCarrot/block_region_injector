<?php
/**
 * @file
 * block_region_injector
 * BlockRegionInjectorServiceProvider.php
 *
 * Created by Jake Wise 03/06/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\block_region_injector;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

/**
 * Class BlockRegionInjectorServiceProvider
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
