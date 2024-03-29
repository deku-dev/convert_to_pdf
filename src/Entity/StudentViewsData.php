<?php

namespace Drupal\student_pdf\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Student entities.
 */
class StudentViewsData extends EntityViewsData {

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
