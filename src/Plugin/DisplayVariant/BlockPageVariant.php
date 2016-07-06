<?php
/**
 * @file
 * block_region_injector
 * BlockPageVariant.php
 *
 * Created by Jake Wise 03/06/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\block_region_injector\Plugin\DisplayVariant;

use Drupal\block_region_injector\BlockPageDisplayManager;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Display\Annotation\PageDisplayVariant;
use Drupal\Core\Display\PageVariantInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Display\VariantBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlockPageVariant
 * @package Drupal\block_region_injector\Plugin\DisplayVariant
 *
 * A block page display variant, which uses a display manager service to store
 * rendered blocks at runtime for re-injection into other templates.
 *
 * @PageDisplayVariant(
 *   id = "block_injected_page",
 *   admin_label = @Translation("Page with injected blocks")
 * )
 */
class BlockPageVariant extends VariantBase implements PageVariantInterface, ContainerFactoryPluginInterface {

  /**
   * The block page display manager service.
   *
   * @var BlockPageDisplayManager
   */
  protected $blockPageDisplayManager;

  /**
   * Constructs a new BlockPageVariant.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param BlockPageDisplayManager $page_display_manager
   *   The block page display manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockPageDisplayManager $page_display_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockPageDisplayManager = $page_display_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('block.page_display_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setMainContent(array $main_content) {
    $this->blockPageDisplayManager->setMainContent($main_content);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->blockPageDisplayManager->setTitle($title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->blockPageDisplayManager->buildFullPageDisplay();
  }

}
