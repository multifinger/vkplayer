<?php

class vkplayerActions extends sfActions
{

    public function preExecute()
    {
        $this->user = null;

        $this->vkSession = vkUser::getSession();
        
    }


    public function executeIndex(sfWebRequest $request)
    {
        // render vkplayer layout
    }

    public function executeSavePlaylist(sfWebRequest $request)
    {
        $this->forward404Unless(
               $request->isMethod(sfRequest::POST)
            && $this->vkSession
        );

        $playlistId = $request->getParameter('id', 0);
        $data       = $request->getParameter('data', array());

        $playlist = new Playlist();
        $playlistItem = new PlaylistItem();
        $playlistItem->author = "1";
        $playlistItem->title = "1";
        $playlistItem->mp3 = "1";
        $playlistItem->time = "1";

        $playlist->PlaylistItems[] = $playlistItem;
        $playlist->PlaylistItems[] = $playlistItem;

        $playlist->name       = "q";
        $playlist->vk_user_id = $this->vkSession['id'];
        $playlist->save();

        /*if (count($data)) {
            // add or update DB
            if (!$playlistId) {
                $playlist = new Playlist();
                foreach ($data as $it) {
                    $playlist->PlaylistItems[] = array(
                        "artist"    => $it['data'],
                        "title"     => $it['title'],
                        "mp3"       => $it['mp3'],
                        "time"      => $it['time'],
                    );
                }
            }
        }*/

        return sfView::NONE;
    }

    public function executeRenamePlaylist(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $playlistId = $request->getParameter('id', 0);
        $name       = $request->getParameter('name', false);

        if ($playlistId && $name) {
            // update playlist name
        }
    }
    
}
