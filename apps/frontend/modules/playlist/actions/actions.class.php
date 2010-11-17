<?php

/**
 * playlist actions.
 *
 * @package    sf_sandbox
 * @subpackage playlist
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class playlistActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->playlists = Doctrine_Core::getTable('Playlist')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->playlist = Doctrine_Core::getTable('Playlist')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->playlist);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PlaylistForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new PlaylistForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($playlist = Doctrine_Core::getTable('Playlist')->find(array($request->getParameter('id'))), sprintf('Object playlist does not exist (%s).', $request->getParameter('id')));
    $this->form = new PlaylistForm($playlist);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($playlist = Doctrine_Core::getTable('Playlist')->find(array($request->getParameter('id'))), sprintf('Object playlist does not exist (%s).', $request->getParameter('id')));
    $this->form = new PlaylistForm($playlist);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($playlist = Doctrine_Core::getTable('Playlist')->find(array($request->getParameter('id'))), sprintf('Object playlist does not exist (%s).', $request->getParameter('id')));
    $playlist->delete();

    $this->redirect('playlist/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $playlist = $form->save();

      $this->redirect('playlist/edit?id='.$playlist->getId());
    }
  }
}
