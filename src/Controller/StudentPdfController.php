<?php

namespace Drupal\student_pdf\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use mikehaertl\wkhtmlto\Pdf;

/**
 * Class StudentPdfController.
 */
class StudentPdfController extends ControllerBase {

  /**
   * Construct.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Current user data.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritDoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container.
   *
   * @return \Drupal\student_pdf\Controller\ExportContentController|static
   *   Static container dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * Build pdf version.
   * @param $student
   *   Student entity.
   * @return array
   *   Render array.
   */
  public function build($student) {
    $render = [
      '#theme' => 'student_pdf',
      '#firstname' => $student->getFirstName(),
      '#lastname' => $student->getLastName(),
      '#content' => $student->getBio(),
    ];
    return $render;
  }

}
