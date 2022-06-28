<?php

namespace Drupal\student_pdf\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Student entity.
 *
 * @ingroup student_pdf
 *
 * @ContentEntityType(
 *   id = "student",
 *   label = @Translation("Student"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\student_pdf\StudentListBuilder",
 *     "views_data" = "Drupal\student_pdf\Entity\StudentViewsData",
 *     "translation" = "Drupal\student_pdf\StudentTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\student_pdf\Form\StudentForm",
 *       "add" = "Drupal\student_pdf\Form\StudentForm",
 *       "edit" = "Drupal\student_pdf\Form\StudentForm",
 *       "delete" = "Drupal\student_pdf\Form\StudentDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\student_pdf\StudentHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\student_pdf\StudentAccessControlHandler",
 *   },
 *   base_table = "student",
 *   data_table = "student_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer student entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "firstname",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/students/{student}",
 *     "add-form" = "/admin/structure/student/add",
 *     "edit-form" = "/admin/structure/student/{student}/edit",
 *     "delete-form" = "/admin/structure/student/{student}/delete",
 *     "collection" = "/admin/structure/students",
 *   },
 *   field_ui_base_route = "students.settings"
 * )
 */
class Student extends ContentEntityBase implements StudentInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLastName(): string {
    return $this->get('lastname')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastName(string $name) {
    $this->set('lastname', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstName(): string {
    return $this->get('firstname')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFirstName(string $name) {
    $this->set('firstname', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPhoto() {
    // Set display options.
    $display_options = [
      'label' => 'hidden',
      'type' => 'responsive_image',
      'settings' => [
        'responsive_image_style' => 'accordion',
      ],
    ];
    $element = $this->get('photo')->view($display_options);
    return render($element);
  }

  /**
   * {@inheritdoc}
   */
  public function setPhoto(int $photo_id) {
    $this->set('photo', $photo_id);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getBio(): string {
    return $this->get('bio')->value;
  }

  /**
   * {@inheritDoc}
   */
  public function setBio(string $bio) {
    $this->set('bio', $bio);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner(): UserInterface {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId(): ?int {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getChanged(): int {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getCreated(): int {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * @throws Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Student entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['firstname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription(t('The First Name of the Student entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['lastname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription(t('The last name of the Student entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['photo'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Photo'))
      ->setDescription(t('Photo student.'))
      ->setSettings([
        'file_directory' => 'students',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'label' => 'hidden',
        'type' => 'image_image',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['bio'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Bio'))
      ->setDescription(t('Bio student.'))
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'text_default',
        'weight' => '6',
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 6,
        'rows' => 6
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setReadOnly(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'))
      ->setReadOnly(TRUE);

    return $fields;
  }

}
