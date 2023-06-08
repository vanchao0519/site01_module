<?php

namespace Drupal\site01;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\site01\Entity\Site01EntityInterface;

/**
 * Defines the storage handler class for Site01entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Site01entity entities.
 *
 * @ingroup site01
 */
class Site01EntityStorage extends SqlContentEntityStorage implements Site01EntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(Site01EntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {site01_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {site01_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(Site01EntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {site01_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('site01_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
