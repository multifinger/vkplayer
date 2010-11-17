<?php

/**
 * audio actions.
 *
 * @package    sf_sandbox
 * @subpackage audio
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class audioActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->playlist_items = Doctrine_Core::getTable('PlaylistItem')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->playlist_item = Doctrine_Core::getTable('PlaylistItem')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->playlist_item);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PlaylistItemForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new PlaylistItemForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($playlist_item = Doctrine_Core::getTable('PlaylistItem')->find(array($request->getParameter('id'))), sprintf('Object playlist_item does not exist (%s).', $request->getParameter('id')));
    $this->form = new PlaylistItemForm($playlist_item);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($playlist_item = Doctrine_Core::getTable('PlaylistItem')->find(array($request->getParameter('id'))), sprintf('Object playlist_item does not exist (%s).', $request->getParameter('id')));
    $this->form = new PlaylistItemForm($playlist_item);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($playlist_item = Doctrine_Core::getTable('PlaylistItem')->find(array($request->getParameter('id'))), sprintf('Object playlist_item does not exist (%s).', $request->getParameter('id')));
    $playlist_item->delete();

    $this->redirect('audio/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $playlist_item = $form->save();

      $this->redirect('audio/edit?id='.$playlist_item->getId());
    }
  }
}
