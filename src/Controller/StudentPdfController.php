<?php

namespace Drupal\student_pdf\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\student_pdf\Entity\Student;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Drupal\file\Entity\File;
use Drupal\Core\ProxyClass\Render\BareHtmlPageRenderer;

/**
 * Class StudentPdfController.
 */
class StudentPdfController extends ControllerBase {

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Render bare page.
   *
   * @var \Drupal\Core\ProxyClass\Render\BareHtmlPageRenderer
   */
  protected $renderPage;

  /**
   * Construct.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Current user data.
   * @param \Drupal\Core\ProxyClass\Render\BareHtmlPageRenderer $renderPage
   *   Render bare page service.
   */
  public function __construct(AccountProxyInterface $currentUser, BareHtmlPageRenderer $renderPage) {
    $this->currentUser = $currentUser;
    $this->renderPage = $renderPage;
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
      $container->get('current_user'),
      $container->get('bare_html_page_renderer')
    );
  }

  /**
   * Build pdf version.
   * @param $student
   *   Student entity.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Render array.
   */
  public function build(Student $student) {
    $renderedPage = $this->renderPage
      ->renderBarePage([], $this->t('Student pdf'), 'student_pdf', [
        '#firstname' => $student->getFirstName(),
        '#lastname' => $student->getLastName(),
        '#bio' => ['#markup' => $student->getBio()],
        '#photo' => $this->getImageUri($student, 'photo'),
        '#show_messages' => FALSE,
      ])
      ->getContent();
    $pdf = new Pdf;
    $pdf->addPage($renderedPage);
    if (!$pdf->saveAs('./page.pdf')) {
      $error = $pdf->getError();
      // ... handle error here
    }
    return new Response($pdf);
  }

  /**
   * Get image url.
   *
   * @param \Drupal\student_pdf\Entity\Student $entity
   *   Entity Student.
   * @param string $fieldName
   *   Field name.
   *
   * @return false|string
   *   Field image url.
   */
  public function getImageUri(Student $entity, string $fieldName) {

    $imageField = $entity->get($fieldName)->getValue();
    if (!empty($imageField[0]['target_id'])) {
      $file = File::load($imageField[0]['target_id']);
      // Original URI.
      return file_create_url($file->getFileUri());
    }
    return FALSE;
  }
}
