<?php

class vkplayerActions extends sfActions
{

    private function isUserPOST(sfWebRequest $request)
    {
        return      $request->isMethod(sfRequest::POST)
                &&  $this->vkSession['id'];
    }


    public function preExecute()
    {
        $this->vkSession = vkUser::getSession();

        parent::preExecute();
    }


    public function executeIndex(sfWebRequest $request)
    {
        // render vkplayer layout
    }

    public function executeLoadPlaylist(sfWebRequest $request)
    {
        $this->forward404Unless($this->isUserPOST($request));

        $q = Doctrine_Query::create()
            ->from('Playlist p')
            ->leftJoin('p.PlaylistItems i')
            ->where('p.vk_user_id = ?', $this->vkSession['id']);
        
        $playlists = $q->fetchArray();

        return $this->renderJSON($playlists[0]);
    }

    public function executeSavePlaylist(sfWebRequest $request)
    {
        $this->forward404Unless($this->isUserPOST($request));

        $id     = $request->getParameter('id', 0);
        $length = $request->getParameter('length', 0);

        if ($length || $id) {
            
            if ($id) {
                //print_r("id = {$id}");
                $playlist = Doctrine::getTable('Playlist')->find($id);
                $playlist->PlaylistItems->delete();
            } else {
                //print_r("new");
                $playlist = new Playlist();
            }
            
            $playlist->name       = "мой первый список";
            $playlist->vk_user_id = $this->vkSession['id'];

            for ($i = 0; $i<$length; $i++) {
                $playlistItem = new PlaylistItem();
                $playlistItem->artist   = $request->getParameter('item_artist_' . $i);
                $playlistItem->title    = $request->getParameter('item_title_' . $i);
                $playlistItem->mp3      = $request->getParameter('item_mp3_' . $i);
                $playlistItem->time     = $request->getParameter('item_time_' . $i);
                $playlist->PlaylistItems[$i] = $playlistItem;
            }

            $playlist->save();
            
            return $this->renderJSON($playlist->toArray());
        }
        
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
