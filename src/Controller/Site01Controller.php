<?php

namespace Drupal\site01\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\site01\Entity\Site01Entity;
use Drupal\user\Entity\User;

/**
 * Returns responses for site01 prefix names of routes.
 */
class Site01Controller extends ControllerBase {

  /**
   * @return array
   */
  public function home() {

    $entities = $this->_getBlogEntities(['limit' => 2]);

    $blogs = [];
    foreach ($entities as $key => $entity) {
      /** @var Site01Entity $entity */
      $blogs[$key]['content'] = $entity;
      $user = $entity->getOwner();
      /** @var User $user */
      $blogs[$key]['author_name'] = $user->name->value;
      $blogs[$key]['author_avatar'] = $user->field_avatar;
    }
    $blogs = array_values($blogs);
    $time_line = $this->_getTimeLineEntities();

    return [
      '#theme' => 'home',
      '#blogs' => $blogs,
      '#time_line' => $time_line,
    ];

  }

  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function about() {

    $entities = $this->_getTimeLineEntities();

    return [
      '#theme' => 'about',
      '#time_line' => $entities,
    ];

  }

  public function pricing() {
    return [
      '#theme' => 'pricing',
    ];
  }

  public function team() {
    return [
      '#theme' => 'team',
    ];
  }

  public function testimonials() {
    return [
      '#theme' => 'testimonials',
    ];
  }

  public function faq() {
    return [
      '#theme' => 'faq',
    ];
  }

  public function service_detail() {
    return [
      '#theme' => 'service_detail',
    ];
  }

  public function page01() {
    return [
      '#theme' => 'page01',
    ];
  }

  /**
   * @param null $blog
   * @return array|TrustedRedirectResponse
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function blog_detail($blog = null) {

    $id = intval($blog);
    $id = is_int($id) && $id > 0 ? $id : 0;

    $entity  = \Drupal::entityTypeManager()
      ->getStorage('site01_entity')
      ->load($id);

    /** @var Site01Entity $entity */
    if ( !empty( $entity ) && 'blog' === $entity->getType() ) {
//      $comment_form = $this->_getCommentForm([
//        'entity_type'  => 'site01_entity',
//        'entity_id'    => $id,
//        'field_name'   => 'field_site01_comment',
//        'comment_type' => 'site01',
//      ]);

      $currentTagEntities = $this->_getReferencedTagEntities( $entity );

      $blogTagEntities = $this->_getBlogTagEntities();

      $latestEntities = $this->_getBlogEntities(['limit' => 3]);

      return [
        '#theme' => 'blog_detail',
        '#entity' => $entity,
//        '#comment_form' => $comment_form,
        '#currentTagEntities' => $currentTagEntities,
        '#blogTagEntities' => $blogTagEntities,
        '#latestEntities' => $latestEntities,
        '#userEntity' => $entity->getOwner(),
      ];
    }

    return new TrustedRedirectResponse('/blog-list');
  }

  /**
   * @return string[]
   */
  public function not_found() {
    return [
      '#theme' => 'not_found',
    ];
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function _getTimeLineEntities () {
    $entities = \Drupal::entityTypeManager()->getStorage('site01_entity')
      ->loadByProperties(['type' => 'time_line', 'status' => 1]);

    return $entities;
  }

  /**
   * @param array $config
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function _getCommentForm ( $config = [] ) {
    $values = array(
      'entity_type'  => $config['entity_type'],
      'entity_id'    => $config['entity_id'],
      'field_name'   => $config['field_name'],
      'comment_type' => $config['comment_type'],
      'pid' => NULL,
    );

    $comment = \Drupal::entityTypeManager()->getStorage('comment')->create($values);
    return \Drupal::service('entity.form_builder')->getForm($comment);
  }

  /**
   * @param array $condition
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function _getBlogEntities ( $condition = [] ) {
    $storage = \Drupal::entityTypeManager()->getStorage('site01_entity');

    $query = \Drupal::entityQuery('site01_entity');
    $query = $query->condition('type', 'blog');

    $has_status = isset($condition['status']) && is_int($condition['status']);
    $query = $query->condition('status', $has_status ? $condition['status'] : 1);

    $has_limit = isset($condition['limit']) && is_int($condition['limit']);
    $query = $query->range(0, $has_limit ? $condition['limit'] : 5);

    $has_sort = isset($condition['sort']) && in_array($condition['sort'],['desc', 'asc']);
    $query = $query->sort('id', $has_sort ? $condition['sort'] : 'desc');

    $query = $query->execute();

    $entities = $storage->loadMultiple($query);

    foreach ($entities as $key => $entity) {
      $entities[$key]->view_url = "/blog-list/{$entity->id()}";
    }

    return $entities;
  }

  /**
   * @param Site01Entity $entity
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  private function _getReferencedTagEntities( $entity ) {
    $tags = $entity->field_tags->getValue();
    foreach ($tags as $tag) $tagsId[] = $tag['target_id'];
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $entities = $storage->loadMultiple($tagsId);
    foreach ($entities as $key => $tag) {
      $entities[$key]->view_url =  $tag->toUrl()->toString();
    }
    return $entities;
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  private function _getBlogTagEntities () {
    $entity_type_id = 'taxonomy_term';
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);
    $query = \Drupal::entityQuery($entity_type_id);
    $query = $query->condition('vid', 'blog_tag');
    $query = $query->execute();
    $entities = $storage->loadMultiple($query);
    foreach ($entities as $key => $entity) {
      $entities[$key]->view_url = $entity->toUrl()->toString();
    }
    return $entities;
  }

}
