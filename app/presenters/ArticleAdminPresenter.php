<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\ArticleModel;
use App\Model\FileModel;
use App\Model\CategoryModel;
use Tracy\Debugger;


class ArticleAdminPresenter extends BasePresenter
{
    const ARTICLES_PER_PAGE = 10;
    const FILES_PER_PAGE = 1;

    private $modelArticle;
    private $modelFile;
    private $modelCategory;
    
    public function __construct(ArticleModel $modelArticle, FileModel $modelFile, CategoryModel $modelCategory) 
    {
        $this->modelArticle = $modelArticle;
        $this->modelFile = $modelFile;
        $this->modelCategory = $modelCategory;
    }

    public function renderDefault($page = 0, $articlesPerPage = self::ARTICLES_PER_PAGE)
    {
	    $this->template->articles = $this->modelArticle->getArticlesOnPage($articlesPerPage, $page);
        $this->template->articlesCount = $this->modelArticle->getArticlesCount();
        $this->template->page = $page;
        $this->template->articlesPerPage = $articlesPerPage;
    }
    
    public function renderNew()
    {
        $this->template->itemsCount = $this->modelFile->getFilesCount();
        $this->template->itemsPerPage = self::FILES_PER_PAGE;
        $this->template->activePage = 0;
        $this->template->images = $this->modelFile->getFilesOnPage(self::FILES_PER_PAGE, 0);
    }
    
    public function renderForm($article_id = NULL)
    {
        if($article_id)
        {
            $this->template->article = $this->modelArticle->getArticle($article_id);
            $article = $this->template->article->toArray();
            $article['time'] = date('H:i', strtotime($article['date']));
            $article['date'] = date('Y-m-d', strtotime($article['date']));
            $this['articleForm']->setDefaults($article);
        }
        $this->template->itemsCount = $this->modelFile->getFilesCount();
        $this->template->itemsPerPage = self::FILES_PER_PAGE;
        $this->template->activePage = 0;
        $this->template->images = $this->modelFile->getFilesOnPage(self::FILES_PER_PAGE, 0);
    }
    
    public function actionDelete($article_id)
    {
        $this->modelArticle->deleteArticle($article_id);
        $this->flashMessage($this->translator->translate('messages.DeletionSuccess'), 'green');
        $this->redirect('ArticleAdmin:default');
    }

    public function actionPublishToggle($article_id)
    {
        $this->modelArticle->publishToggleArticle($article_id);
        $this->flashMessage($this->translator->translate('messages.Published'), 'green');
        $this->redirect('ArticleAdmin:default');
    }
    
    public function actionPreview($article_id)
    {
        $article = $this->modelArticle->getArticle($article_id);
        if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
            $protocol = 'https://';
        }
        else
        {
            $protocol = 'http://';
        }
        $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $article->url);
    }
    
    public function createComponentArticleForm()
    {
        $form = new Form();
        $form->addText('title', $this->translator->translate('messages.title'));
        $form->addCheckbox('showTitle', $this->translator->translate('messages.ShowTitle'))
            ->setAttribute('class', 'filled-in')
            ->setDefaultValue(true);
        $form->addText('url', $this->translator->translate('messages.url'))
                ->setDefaultValue($this->translator->translate('messages.article') . $this->modelArticle->getMaxArticleId() . '-' . date('Y-m-d'));
        $form->addText('description', $this->translator->translate('messages.description'));
        $form->addText('keywords', $this->translator->translate('messages.keywords'));
        $form->addTextArea('content', $this->translator->translate('messages.content'))
                ->setAttribute('id', 'tinyArea');
        $form->addText('date', $this->translator->translate('messages.date'));
        $form->addText('time', $this->translator->translate('messages.time'));
        $form->addSelect('category_id', $this->translator->translate('messages.Category'), $this->modelCategory->getCategoriesForSelect());
        $form->addSubmit('publish', (isset($this->getParameters()['article_id']) &&
        $this->modelArticle->getArticle($this->getParameters()['article_id'])->published == true)?
            $this->translator->translate('messages.Unpublish') : $this->translator->translate('messages.Publish'));
        $form->addSubmit('preview', $this->translator->translate('messages.Preview'));
        $form->addSubmit('save', $this->translator->translate('messages.Save'));
        $form->addHidden('published', $this->translator->translate('messages.Published'))
            ->setDefaultValue(false);
        $form->addCheckbox('header_footer', $this->translator->translate('messages.HeaderAndFooter'))
            ->setAttribute('class', 'filled-in')
            ->setAttribute('id', 'header_footer')
            ->setDefaultValue(true);
        $form->onValidate[] = array($this, 'articleFormValidation');
        $form->onSuccess[] = array($this, 'articleFormSuccess');
        return $form;
    }
    
    public function articleFormSuccess($form, $values)
    {
        if($form['publish']->isSubmittedBy())
        {
            $values->published = !$values->published;
            if(isset($this->getParameters()['article_id']))
            {
                $article_id = $this->getParameters()['article_id'];
                $this->modelArticle->editArticle($article_id, $values);
            }
            else
            {
                $this->modelArticle->createNewArticle($values);
            }
            $this->flashMessage($this->translator->translate('messages.Published'), 'green');
            $this->redirect('ArticleAdmin:default');
        }
        else if($form['save']->isSubmittedBy())
        {
            if(isset($this->getParameters()['article_id']))
            {
                $article_id = $this->getParameters()['article_id'];
                $this->modelArticle->editArticle($article_id, $values);
                $article = $this->modelArticle->getArticle($article_id);
            }
            else
            {
                $article = $this->modelArticle->createNewArticle($values);
            }
            $this->flashMessage($this->translator->translate('messages.Saved'), 'green');
            $this->redirect('ArticleAdmin:form', $article->id);
        }
        else
        {
            if(isset($this->getParameters()['article_id']))
            {
                $article_id = $this->getParameters()['article_id'];
                $this->modelArticle->editArticle($article_id, $values);
                $article = $this->modelArticle->getArticle($article_id);
            }
            else
            {
                $article = $this->modelArticle->createNewArticle($values);
            }
            $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $article->url);
        }
    }
    
    public function articleFormValidation($form, $values)
    {
        if(isset($this->getParameters()['article_id']))
        {
            $article_id = $this->getParameters()['article_id'];
            $article = $this->modelArticle->getArticle($article_id);
            if($article->url != $values->url)
            {
                if($this->modelCategory->urlExist($values->url) || $this->modelArticle->urlExist($values->url))
                {
                    $form['url']->addError($this->translator->translate('messages.UrlAlreadyExist'));
                    $this->flashMessage($this->translator->translate('messages.UrlAlreadyExist'), 'red');
                }
            }
        }
        else
        {
            if($this->modelCategory->urlExist($values->url) || $this->modelArticle->urlExist($values->url))
            {
                $form['url']->addError($this->translator->translate('messages.UrlAlreadyExist'));
                $this->flashMessage($this->translator->translate('messages.UrlAlreadyExist'), 'red');
            }
        }
        if($values->title == '')
        {
            $form['title']->addError($this->translator->translate('messages.CouldNotBeEmpty'));
        }
    }
    
    public function renderImageModalPage($page = 0, $filesPerPage = self::FILES_PER_PAGE)
    {
        $dir = dirname($this->getReflection()->getFileName());
        $this->template->setFile("$dir/../templates/ArticleAdmin/imageList.latte");
        $this->template->itemsCount = $this->modelFile->getFilesCount();
        $this->template->itemsPerPage = $filesPerPage;
        $this->template->activePage = $page;
        $this->template->colorClass = $this->colorClass;
        $this->template->images = $this->modelFile->getFilesOnPage($filesPerPage, $page);
        $this->sendTemplate();
    }

}
