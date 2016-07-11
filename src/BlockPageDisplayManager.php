<?php

namespace Drupal\block_region_injector;

use Drupal\block\BlockInterface;
use Drupal\block\BlockRepositoryInterface;
use Drupal\Core\Block\MainContentBlockPluginInterface;
use Drupal\Core\Block\MessagesBlockPluginInterface;
use Drupal\Core\Block\TitleBlockPluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;

/**
 * Class BlockPageDisplayManager.
 *
 * @package Drupal\block_region_injector
 */
class BlockPageDisplayManager {

  /**
   * Internal block cache, keyed by region.
   *
   * @var array
   */
  protected $blockCache = [];

  /**
   * Internal block view cache, keyed by region.
   *
   * @var array
   */
  protected $blockViewCache;

  /**
   * Block view builder service.
   *
   * @var EntityViewBuilderInterface
   */
  protected $blockViewBuilder;

  /**
   * Block repository service.
   *
   * @var BlockRepositoryInterface
   */
  protected $blockRepository;

  /**
   * The Block entity type list cache tags.
   *
   * @var string[]
   */
  protected $blockListCacheTags;

  /**
   * The main content block plugin.
   *
   * @var BlockInterface
   */
  protected $mainContentBlock;

  /**
   * Main content for the page.
   *
   * @var array
   */
  protected $mainContent;

  /**
   * The title block plugin.
   *
   * @var BlockInterface
   */
  protected $titleBlock;

  /**
   * Cacheable metadata list for blocks.
   *
   * @var array
   */
  protected $cacheableMetadataList = [];

  /**
   * BlockRegionInjector constructor.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The block entity manager.
   * @param BlockRepositoryInterface $block_repository
   *   The block entity repository.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, BlockRepositoryInterface $block_repository) {
    $this->blockViewBuilder = $entity_type_manager->getViewBuilder('block');
    $this->blockRepository = $block_repository;
    $this->blockListCacheTags = $entity_type_manager->getDefinition('block')->getListCacheTags();

    $this->buildBlockCache();
  }

  /**
   * Set the page title.
   *
   * @param string $title
   *   The title of the page to set to the title block.
   */
  public function setTitle($title) {
    if ($this->titleBlock) {
      $this->titleBlock->getPlugin()->setTitle($title);
      $this->blockViewCache[$this->titleBlock->getRegion()][$this->titleBlock->getOriginalId()] = $this->blockViewBuilder->view($this->titleBlock);
    }
  }

  /**
   * Set the content of the main content block.
   *
   * @param array $main_content
   *   The main content renderable array to set to the main content block.
   */
  public function setMainContent(array $main_content) {
    $this->mainContent = $main_content;
    if ($this->mainContentBlock) {
      $this->mainContentBlock->getPlugin()->setMainContent($this->mainContent);
      $this->blockViewCache[$this->mainContentBlock->getRegion()][$this->mainContentBlock->getOriginalId()] = $this->blockViewBuilder->view($this->mainContentBlock);
    }
  }

  /**
   * Build the full page display, with content and title.
   *
   * @return array
   *   Renderable array.
   */
  public function buildFullPageDisplay() {
    // Track whether blocks showing the main content and messages are displayed.
    $main_content_block_displayed = FALSE;
    $messages_block_displayed = FALSE;

    $build = [
      '#cache' => [
        'tags' => $this->blockListCacheTags,
      ],
    ];

    /** @var BlockInterface[] $blocks */
    foreach ($this->blockCache as $region => $blocks) {
      foreach ($blocks as $key => $block) {
        if (!isset($this->blockViewCache[$region][$key])) {
          continue;
        }

        $build[$region][$key] = $this->blockViewCache[$region][$key];

        // The main content block cannot be cached: it is a placeholder for the
        // render array returned by the controller. It should be rendered as-is,
        // with other placed blocks "decorating" it. Analogous reasoning for the
        // title block.
        $block_plugin = $block->getPlugin();
        if ($block_plugin instanceof MainContentBlockPluginInterface) {
          $main_content_block_displayed = TRUE;
          unset($build[$region][$key]['#cache']['keys']);
        }
        elseif ($block_plugin instanceof TitleBlockPluginInterface) {
          unset($build[$region][$key]['#cache']['keys']);
        }
        elseif ($block_plugin instanceof MessagesBlockPluginInterface) {
          $messages_block_displayed = TRUE;
        }
      }

      if (!empty($build[$region])) {
        // \Drupal\block\BlockRepositoryInterface::getVisibleBlocksPerRegion()
        // returns the blocks in sorted order.
        $build[$region]['#sorted'] = TRUE;
      }
    }

    // If no block that shows the main content is displayed, still show the main
    // content. Otherwise the end user will see all displayed blocks, but not
    // the main content they came for.
    if (!$main_content_block_displayed) {
      $build['content']['system_main'] = $this->mainContent;
    }

    // If no block displays status messages, still render them.
    if (!$messages_block_displayed) {
      $build['content']['messages'] = [
        '#weight' => -1000,
        '#type' => 'status_messages',
      ];
    }

    // If any render arrays are manually placed, render arrays and blocks must
    // be sorted.
    if (!$main_content_block_displayed || !$messages_block_displayed) {
      unset($build['content']['#sorted']);
    }

    // The access results' cacheability is currently added to the top level of the
    // render array. This is done to prevent issues with empty regions being
    // displayed.
    // This would need to be changed to allow caching of block regions, as each
    // region must then have the relevant cacheable metadata.
    $merged_cacheable_metadata = CacheableMetadata::createFromRenderArray($build);
    foreach ($this->cacheableMetadataList as $cacheable_metadata) {
      $merged_cacheable_metadata = $merged_cacheable_metadata->merge($cacheable_metadata);
    }
    $merged_cacheable_metadata->applyTo($build);

    return $build;
  }

  /**
   * Retrieve the list of regions containing block views.
   *
   * @return array
   *   An array of renderable block views, keyed by their respective regions.
   */
  public function getBlockViewsByRegion() {
    return $this->blockViewCache;
  }

  /**
   * Build the internal cache of block views, keyed by region.
   */
  protected function buildBlockCache() {
    // Load all region content assigned via blocks.
    foreach ($this->blockRepository->getVisibleBlocksPerRegion($this->cacheableMetadataList) as $region => $blocks) {
      /** @var BlockInterface[] $blocks */
      foreach ($blocks as $key => $block) {
        $block_plugin = $block->getPlugin();
        if ($block_plugin instanceof MainContentBlockPluginInterface) {
          $this->mainContentBlock = $block;
        }
        elseif ($block_plugin instanceof TitleBlockPluginInterface) {
          $this->titleBlock = $block;
        }
        else {
          $this->blockViewCache[$region][$key] = $this->blockViewBuilder->view($block);
        }

        $this->blockCache[$region][$key] = $block;
      }
    }
  }

}
