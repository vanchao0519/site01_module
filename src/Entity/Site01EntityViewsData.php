<?php

namespace Drupal\site01\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Site01entity entities.
 */
class Site01EntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
