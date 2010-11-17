<?php

/**
 * BasePlaylistItem
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $playlist_id
 * @property string $title
 * @property string $author
 * @property string $mp3
 * @property int $time
 * @property Playlist $Playlist
 * 
 * @method integer      getPlaylistId()  Returns the current record's "playlist_id" value
 * @method string       getTitle()       Returns the current record's "title" value
 * @method string       getAuthor()      Returns the current record's "author" value
 * @method string       getMp3()         Returns the current record's "mp3" value
 * @method int          getTime()        Returns the current record's "time" value
 * @method Playlist     getPlaylist()    Returns the current record's "Playlist" value
 * @method PlaylistItem setPlaylistId()  Sets the current record's "playlist_id" value
 * @method PlaylistItem setTitle()       Sets the current record's "title" value
 * @method PlaylistItem setAuthor()      Sets the current record's "author" value
 * @method PlaylistItem setMp3()         Sets the current record's "mp3" value
 * @method PlaylistItem setTime()        Sets the current record's "time" value
 * @method PlaylistItem setPlaylist()    Sets the current record's "Playlist" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePlaylistItem extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('playlist_item');
        $this->hasColumn('playlist_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('author', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('mp3', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('time', 'int', 5, array(
             'type' => 'int',
             'length' => 5,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Playlist', array(
             'local' => 'playlist_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}