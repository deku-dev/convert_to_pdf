<?php

namespace Drupal\student_pdf\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\student_pdf\Entity\Student;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Drupal\file\Entity\File;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class StudentPdfController.
 */
class StudentPdfController extends ControllerBase {

  /**
   * Module handler.
   *
   * @var Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;
  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Construct.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Current user data.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module handler.
   */
  public function __construct(AccountProxyInterface $currentUser, ModuleHandlerInterface $moduleHandler) {
    $this->currentUser = $currentUser;
    $this->moduleHandler = $moduleHandler;
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
      $container->get('module_handler')
    );
  }

  /**
   * Build pdf version.
   *
   * @param \Drupal\student_pdf\Entity\Student $student
   *   Student entity.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Render array.
   */
  public function build(Student $student) {
    $response = new Response();
    $module_path = $this->moduleHandler->getModule('student_pdf')->getPath();
    $debug = $this->config('students_pdf.settings')->get('debug');
    $response->headers->add($debug ? [] : [
      'Content-Type' => 'application/pdf',
      'Cache-Control' => 'public, must-revalidate, max-age=0',
      'Pragma' => 'no-cache',
      'Expires' => '0',
      'Content-Disposition' => 'inline; filename=' .
        $student->getFirstName() . '-' . $student->id() . '.pdf'
    ]);
    // Using twig to render an html file.
    // Folder module with templates.
    $loader = new FilesystemLoader($module_path . "/templates/");
    $twig = new Environment($loader);
    // Set twig file to render.
    $template = $twig->load('student-pdf.html.twig');
    $rendered = $template->render([
      'firstname' => $student->getFirstName(),
      'lastname' => $student->getLastName(),
      'bio' => $student->getBio(),
      'photo' => $this->getImageUri($student, 'photo'),
      '#show_messages' => FALSE,
    ]
    );

    $options = [
      "binary" => $module_path . "/binary/wkhtmltopdf",
      "ignoreWarnings" => TRUE,
      "tmpDir" => "public://",
    ];
    $pdf = new Pdf($options);
    $pdf->addPage($rendered);
    // Render html or pdf depending on the debug setting.
    $response->setContent($debug ? $rendered : $pdf->toString());
    return $response;
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
