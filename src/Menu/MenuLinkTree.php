<?php

namespace Drupal\ex_icons_menu\Menu;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Wraps menu link tree service to add icon data to menu items.
 */
class MenuLinkTree implements MenuLinkTreeInterface {

  /**
   * The original menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $inner;

  /**
   * The menu link storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|null
   */
  protected $menuLinkContentStorage;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface|null
   */
  protected $entityRepository;

  /**
   * Constructs a MenuLinkTree object.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $inner
   *   The original menu link tree service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   Optional. The entity repository.
   */
  public function __construct(MenuLinkTreeInterface $inner, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository = NULL) {
    $this->inner = $inner;
    $this->menuLinkContentStorage = $entity_type_manager->hasHandler('menu_link_content', 'storage')
      ? $entity_type_manager->getStorage('menu_link_content')
      : NULL;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentRouteMenuTreeParameters($menu_name) {
    return $this->inner->getCurrentRouteMenuTreeParameters($menu_name);
  }

  /**
   * {@inheritdoc}
   */
  public function load($menu_name, MenuTreeParameters $parameters) {
    return $this->inner->load($menu_name, $parameters);
  }

  /**
   * {@inheritdoc}
   */
  public function transform(array $tree, array $manipulators) {
    return $this->inner->transform($tree, $manipulators);
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $tree) {
    $build = $this->inner->build($tree);

    if (isset($build['#items'])) {
      $this->setIcons($build['#items']);
    }

    return $build;
  }

  /**
   * Sets icon data on to a list of menu links.
   *
   * @param array &$items
   *   The list of menu link items.
   */
  protected function setIcons(array &$items) {
    foreach ($items as $id => $item) {
      /** @var \Drupal\Core\Menu\MenuLinkInterface $link */
      $link = $item['original_link'];
      $meta = $link->getMetadata();

      $items[$id]['icon'] = NULL;

      if (isset($meta['ex_icons_menu_icon'])) {
        $items[$id]['icon'] = $meta['ex_icons_menu_icon'];
      }
      elseif ($link->getProvider() == 'menu_link_content') {
        $items[$id]['icon'] = $this->getMenuLinkContentIcon($link);
      }

      if ($item['below']) {
        $this->setIcons($items[$id]['below']);
      }
    }
  }

  /**
   * Attempts to get the icon value from a menu link content plugin derivative.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface $link
   *   The link plugin derivative.
   *
   * @return string|null
   *   The icon value of the menu link content entity associated with the given
   *   plugin derivative or NULL if no value was available.
   */
  protected function getMenuLinkContentIcon(MenuLinkInterface $link) {
    $entity   = NULL;
    $metadata = $link->getMetaData();

    if ($this->menuLinkContentStorage && !empty($metadata['entity_id'])) {
      $entity = $this->menuLinkContentStorage->load($metadata['entity_id']);
    }

    if (!$entity && $this->entityRepository) {
      $entity = $this->entityRepository->loadEntityByUuid('menu_link_content', $link->getDerivativeId());
    }

    return $entity ? $entity->get('ex_icons_menu_icon')->first()->value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function maxDepth() {
    return $this->inner->maxDepth();
  }

  /**
   * {@inheritdoc}
   */
  public function getSubtreeHeight($id) {
    return $this->inner->getSubtreeHeight($id);
  }

  /**
   * {@inheritdoc}
   */
  public function getExpanded($menu_name, array $parents) {
    return $this->inner->getExpanded($menu_name, $parents);
  }

}
