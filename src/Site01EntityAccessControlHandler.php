<?php

namespace Drupal\site01;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Site01entity entity.
 *
 * @see \Drupal\site01\Entity\Site01Entity.
 */
class Site01EntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\site01\Entity\Site01EntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished site01entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published site01entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit site01entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete site01entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add site01entity entities');
  }


}
