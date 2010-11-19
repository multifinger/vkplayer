<?php

/**
 * BasePlaylist
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property integer $vk_user_id
 * @property Doctrine_Collection $PlaylistItems
 * 
 * @method string              getName()          Returns the current record's "name" value
 * @method integer             getVkUserId()      Returns the current record's "vk_user_id" value
 * @method Doctrine_Collection getPlaylistItems() Returns the current record's "PlaylistItems" collection
 * @method Playlist            setName()          Sets the current record's "name" value
 * @method Playlist            setVkUserId()      Sets the current record's "vk_user_id" value
 * @method Playlist            setPlaylistItems() Sets the current record's "PlaylistItems" collection
 * 
 * @package    vkplayer
 * @subpackage model
 * @author     multifinger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePlaylist extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('playlist');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('vk_user_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('PlaylistItem as PlaylistItems', array(
             'local' => 'id',
             'foreign' => 'playlist_id'));
    }
}