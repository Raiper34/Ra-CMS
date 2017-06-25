<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\ArticleModel;
use App\Model\FileModel;

class FileAdminPresenter extends BasePresenter
{
    private $modelFile;

    const FILES_PER_PAGE = 5;

    public function __construct(FileModel $modelFile) 
    {
        $this->modelFile = $modelFile;
    }
    
    public function renderDefault($page = 0, $filesPerPage = self::FILES_PER_PAGE)
    {
        $this->template->files = $this->modelFile->getFilesOnPage($filesPerPage, $page);
        $this->template->filesCount = $this->modelFile->getFilesCount();
        $this->template->page = $page;
        $this->template->filesPerPage = $filesPerPage;
    }
    
    public function createComponentFileUpload()
    {
        $form = new Form();
        $form->addUpload('file', 'file')
                ->setAttribute('multiple');
        $form->onSuccess[] = array($this, 'fileUploadSuccess');
        return $form;
    }
    
    public function fileUploadSuccess()
    {
        $this->modelFile->saveFile($_FILES["file"]);
        /*$target_dir = realpath(__DIR__ . '/../../') . '/upload/';
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);*/
    }
    
    public function actionDelete($file_id)
    {
        $this->modelFile->deleteFile($file_id);
        $this->flashMessage($this->translator->translate('messages.FileWasDeleted'), 'green');
        $this->redirect('FileAdmin:default');
    }

}
