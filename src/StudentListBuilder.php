<?php

namespace Drupal\student_pdf;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Student entities.
 *
 * @ingroup student_pdf
 */
class StudentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['firstname'] = $this->t('First Name');
    $header['lastname'] = $this->t('Last Name');
    $header['photo'] = $this->t('Photo');
    $header['bio'] = $this->t('Bio');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Changed');
    $header = $header + parent::buildHeader();
    $header['pdf_version'] = "Pdf Version";
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\student_pdf\Entity\Student $entity */
    $row['id'] = $entity->id();
    $row['firstname'] = Link::createFromRoute(
      $entity->label(),
      'entity.student.edit_form',
      ['student' => $entity->id()]
    );
    $row['lastname'] = $entity->getLastName();
    $row['photo'] = $entity->getPhoto();
    $row['bio'] = $entity->getBio();
    $row['created'] = date("Y-m-d H:i:s", $entity->getCreated());
    $row['changed'] = date("Y-m-d H:i:s", $entity->getChanged());
    $row = $row + parent::buildRow($entity);
    $button = [
      '#type' => 'button',
      '#value' => t('PDF Version'),
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $row['pdf_version'] = "";
    return $row;
  }

}
