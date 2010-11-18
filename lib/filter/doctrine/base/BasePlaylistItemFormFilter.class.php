<?php

/**
 * PlaylistItem filter form base class.
 *
 * @package    vkplayer
 * @subpackage filter
 * @author     multifinger
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlaylistItemFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'playlist_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Playlist'), 'add_empty' => true)),
      'title'       => new sfWidgetFormFilterInput(),
      'artist'      => new sfWidgetFormFilterInput(),
      'mp3'         => new sfWidgetFormFilterInput(),
      'time'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'playlist_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Playlist'), 'column' => 'id')),
      'title'       => new sfValidatorPass(array('required' => false)),
      'artist'      => new sfValidatorPass(array('required' => false)),
      'mp3'         => new sfValidatorPass(array('required' => false)),
      'time'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('playlist_item_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlaylistItem';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'playlist_id' => 'ForeignKey',
      'title'       => 'Text',
      'artist'      => 'Text',
      'mp3'         => 'Text',
      'time'        => 'Text',
    );
  }
}
