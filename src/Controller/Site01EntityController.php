<?php

namespace Drupal\site01\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\site01\Entity\Site01EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Site01EntityController.
 *
 *  Returns responses for Site01entity routes.
 */
class Site01EntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Site01entity revision.
   *
   * @param int $site01_entity_revision
   *   The Site01entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($site01_entity_revision) {
    $site01_entity = $this->entityTypeManager()->getStorage('site01_entity')
      ->loadRevision($site01_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('site01_entity');

    return $view_builder->view($site01_entity);
  }

  /**
   * Page title callback for a Site01entity revision.
   *
   * @param int $site01_entity_revision
   *   The Site01entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($site01_entity_revision) {
    $site01_entity = $this->entityTypeManager()->getStorage('site01_entity')
      ->loadRevision($site01_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $site01_entity->label(),
      '%date' => $this->dateFormatter->format($site01_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Site01entity.
   *
   * @param \Drupal\site01\Entity\Site01EntityInterface $site01_entity
   *   A Site01entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(Site01EntityInterface $site01_entity) {
    $account = $this->currentUser();
    $site01_entity_storage = $this->entityTypeManager()->getStorage('site01_entity');

    $langcode = $site01_entity->language()->getId();
    $langname = $site01_entity->language()->getName();
    $languages = $site01_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $site01_entity->label()]) : $this->t('Revisions for %title', ['%title' => $site01_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all site01entity revisions") || $account->hasPermission('administer site01entity entities')));
    $delete_permission = (($account->hasPermission("delete all site01entity revisions") || $account->hasPermission('administer site01entity entities')));

    $rows = [];

    $vids = $site01_entity_storage->revisionIds($site01_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\site01\Entity\Site01EntityInterface $revision */
      $revision = $site01_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $site01_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.site01_entity.revision', [
            'site01_entity' => $site01_entity->id(),
            'site01_entity_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $site01_entity->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.site01_entity.translation_revert', [
                'site01_entity' => $site01_entity->id(),
                'site01_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.site01_entity.revision_revert', [
                'site01_entity' => $site01_entity->id(),
                'site01_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.site01_entity.revision_delete', [
                'site01_entity' => $site01_entity->id(),
                'site01_entity_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['site01_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
