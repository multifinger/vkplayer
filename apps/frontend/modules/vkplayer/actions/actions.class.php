<?php

class vkplayerActions extends sfActions
{

    public function executeIndex(sfWebRequest $request)
    {
        // render vkplayer layout
    }

    public function executeSavePlaylist(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        
    }

}
