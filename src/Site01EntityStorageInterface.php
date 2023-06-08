<?php

namespace Drupal\site01;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface Site01EntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Site01entity revision IDs for a specific Site01entity.
   *
   * @param \Drupal\site01\Entity\Site01EntityInterface $entity
   *   The Site01entity entity.
   *
   * @return int[]
   *   Site01entity revision IDs (in ascending order).
   */
  public function revisionIds(Site01EntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Site01entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Site01entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\site01\Entity\Site01EntityInterface $entity
   *   The Site01entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(Site01EntityInterface $entity);

  /**
   * Unsets the language for all Site01entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
