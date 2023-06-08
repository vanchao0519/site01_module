<?php

namespace Drupal\site01\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Site01entity entities.
 *
 * @ingroup site01
 */
interface Site01EntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Site01entity name.
   *
   * @return string
   *   Name of the Site01entity.
   */
  public function getName();

  /**
   * Sets the Site01entity name.
   *
   * @param string $name
   *   The Site01entity name.
   *
   * @return \Drupal\site01\Entity\Site01EntityInterface
   *   The called Site01entity entity.
   */
  public function setName($name);

  /**
   * Gets the Site01entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Site01entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Site01entity creation timestamp.
   *
   * @param int $timestamp
   *   The Site01entity creation timestamp.
   *
   * @return \Drupal\site01\Entity\Site01EntityInterface
   *   The called Site01entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Site01entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Site01entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\site01\Entity\Site01EntityInterface
   *   The called Site01entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Site01entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Site01entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\site01\Entity\Site01EntityInterface
   *   The called Site01entity entity.
   */
  public function setRevisionUserId($uid);

}
