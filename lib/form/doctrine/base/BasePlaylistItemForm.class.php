<?php

/**
 * PlaylistItem form base class.
 *
 * @method PlaylistItem getObject() Returns the current form's model object
 *
 * @package    vkplayer
 * @subpackage form
 * @author     multifinger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlaylistItemForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'playlist_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Playlist'), 'add_empty' => false)),
      'title'       => new sfWidgetFormInputText(),
      'author'      => new sfWidgetFormInputText(),
      'mp3'         => new sfWidgetFormInputText(),
      'time'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'playlist_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Playlist'))),
      'title'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'author'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mp3'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'time'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('playlist_item[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlaylistItem';
  }

}
