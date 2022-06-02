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
   */
  public function build($student) {
    return $student;
  }

}
