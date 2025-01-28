<?php
declare(strict_types = 1);

namespace Drupal\hbkcolissimochrono\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\hbkcolissimochrono\EtiquetteColissimoInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the etiquette colissimo entity class.
 *
 * @ContentEntityType(
 *   id = "hbketiquetecolisimo",
 *   label = @Translation("Etiquette colissimo"),
 *   label_collection = @Translation("Etiquette colissimos"),
 *   label_singular = @Translation("etiquette colissimo"),
 *   label_plural = @Translation("etiquette colissimos"),
 *   label_count = @PluralTranslation(
 *     singular = "@count etiquette colissimos",
 *     plural = "@count etiquette colissimos",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\hbkcolissimochrono\EtiquetteColissimoListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\hbkcolissimochrono\Form\EtiquetteColissimoForm",
 *       "edit" = "Drupal\hbkcolissimochrono\Form\EtiquetteColissimoForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *   },
 *   base_table = "hbketiquetecolisimo",
 *   data_table = "hbketiquetecolisimo_field_data",
 *   revision_table = "hbketiquetecolisimo_revision",
 *   revision_data_table = "hbketiquetecolisimo_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer hbketiquetecolisimo",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/hbketiquetecolisimo",
 *     "add-form" = "/hbketiquetecolisimo/add",
 *     "canonical" = "/hbketiquetecolisimo/{hbketiquetecolisimo}",
 *     "edit-form" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/edit",
 *     "delete-form" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/delete",
 *     "delete-multiple-form" = "/admin/content/hbketiquetecolisimo/delete-multiple",
 *     "revision" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/revision/{hbketiquetecolisimo_revision}/view",
 *     "revision-delete-form" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/revision/{hbketiquetecolisimo_revision}/delete",
 *     "revision-revert-form" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/revision/{hbketiquetecolisimo_revision}/revert",
 *     "version-history" = "/hbketiquetecolisimo/{hbketiquetecolisimo}/revisions",
 *   },
 *   field_ui_base_route = "entity.hbketiquetecolisimo.settings",
 * )
 */
final class EtiquetteColissimo extends RevisionableContentEntityBase implements EtiquetteColissimoInterface {
  
  use EntityChangedTrait;
  use EntityOwnerTrait;
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    $fields['label'] = BaseFieldDefinition::create('string')->setRevisionable(TRUE)->setTranslatable(TRUE)->setLabel(t('Label'))->setRequired(TRUE)->setSetting('max_length', 255)->setDisplayOptions(
      'form', [
        'type' => 'string_textfield',
        'weight' => -5
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'string',
      'weight' => -5
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields["order"] = BaseFieldDefinition::create('entity_reference')->setLabel(t(" Order "))->setTranslatable(false)->setDisplayOptions('form',
      [
        'type' => 'entity_reference_autocomplete',
        'weight' => -4
      ])->setSetting("handler_settings", [
      "auto_create" => true
    ])->setSetting("handler", "default")->setSetting("target_type", "commerce_order")->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    // Champ pour le fichier.
    $fields['files'] = BaseFieldDefinition::create('file')->setLabel(t('Contains the generated Colissimo labels'))->setSettings(
      [
        'file_directory' => 'colissimo_labels',
        'file_extensions' => 'pdf txt png'
        // 'max_filesize' => '10MB'
      ])->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)->setDisplayOptions('form', [
      'type' => 'file_generic',
      'weight' => -3
    ])->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'file_default',
      'weight' => 0
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    $fields['metadatas'] = BaseFieldDefinition::create('text_long')->setLabel(" Description ")->setSettings([
      'text_processing' => 0
      // 'html_format' => "text_code"
    ])->setDisplayConfigurable('form', true)->setDisplayConfigurable('view', TRUE)->setDisplayOptions('form', [
      'type' => 'text_textarea',
      'weight' => 0
    ])->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0
    ])->setTranslatable(TRUE);
    
    $fields['status'] = BaseFieldDefinition::create('boolean')->setRevisionable(TRUE)->setLabel(t('Status'))->setDefaultValue(TRUE)->setSetting('on_label', 'Enabled')->setDisplayOptions('form',
      [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE
        ],
        'weight' => 0
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'type' => 'boolean',
      'label' => 'above',
      'weight' => 0,
      'settings' => [
        'format' => 'enabled-disabled'
      ]
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')->setRevisionable(TRUE)->setTranslatable(TRUE)->setLabel(t('Author'))->setSetting('target_type', 'user')->setDefaultValueCallback(
      self::class . '::getDefaultEntityOwner')->setDisplayOptions('form',
      [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => ''
        ],
        'weight' => 15
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'author',
      'weight' => 15
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Authored on'))->setTranslatable(TRUE)->setDescription(t('The time that the etiquette colissimo was created.'))->setDisplayOptions(
      'view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20
      ])->setDisplayConfigurable('form', TRUE)->setDisplayOptions('form', [
      'type' => 'datetime_timestamp',
      'weight' => 20
    ])->setDisplayConfigurable('view', TRUE);
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setTranslatable(TRUE)->setDescription(t('The time that the etiquette colissimo was last edited.'));
    
    return $fields;
  }
}
