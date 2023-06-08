<?php

/**
 * @file
 * Contains site01_entity.page.inc.
 *
 * Page callback for Site01entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Site01entity templates.
 *
 * Default template: site01_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_site01_entity(array &$variables) {
  // Fetch Site01Entity Entity Object.
  $site01_entity = $variables['elements']['#site01_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
