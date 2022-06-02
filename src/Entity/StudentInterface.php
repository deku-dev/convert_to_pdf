<?php

namespace Drupal\student_pdf\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Student entities.
 *
 * @ingroup student_pdf
 */
interface StudentInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Student name.
   *
   * @return string
   *   Name of the Student.
   */
  public function getLastName();

  /**
   * Sets the Student name.
   *
   * @param string $name
   *   The Student name.
   *
   * @return StudentInterface
   *   The called Student entity.
   */
  public function setLastName(string $name);

  /**
   * Gets the Student name.
   *
   * @return string
   *   Name of the Student.
   */
  public function getFirstName();

  /**
   * Sets the Student name.
   *
   * @param string $name
   *   The Student name.
   *
   * @return StudentInterface
   *   The called Student entity.
   */
  public function setFirstName(string $name);

  /**
   * Gets the Student photo.
   *
   * @return object
   *   Id of the Student photo.
   */
  public function getPhoto();

  /**
   * Gets the student bio.
   *
   * @return string
   *   Text student bio.
   */
  public function getBio();

  /**
   * Sets the students bio.
   *
   * @param string $bio
   *   The student bio.
   *
   * @return StudentInterface
   *   The called student entity.
   */
  public function setBio(string $bio);

  /**
   * Sets the Student photo.
   *
   * @param int $photo_id
   *   The Student photo id.
   *
   * @return StudentInterface
   *   The called Student entity.
   */
  public function setPhoto(int $photo_id);

  /**
   * Gets the created timestamp.
   *
   * @return int
   *   Timestamp creating student.
   */
  public function getCreated();

  /**
   * Gets the changed timestamp.
   *
   * @return int
   *   Timestamp changed student.
   */
  public function getChanged();

}
