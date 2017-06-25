<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\PageModel;
use App\Model\ArticleModel;
use App\Model\CategoryModel;


class PageAdminPresenter extends BasePresenter
{

    private $modelPage;
    private $modelArticle;
    private $modelCategory;
    
    public function __construct(PageModel $modelPage, ArticleModel $modelArticle, CategoryModel $modelCategory) 
    {
        $this->modelPage = $modelPage;
        $this->modelArticle = $modelArticle;
        $this->modelCategory = $modelCategory;
    }

    public function renderDefault()
    {
        $this->template->pages = $this->modelPage->getPages();
    }
    
    public function renderForm($page_id = NULL)
    {
        if($page_id)
        {
            $this->template->page = $this->modelPage->getPage($page_id);
            $this['pageForm']->setDefaults($this->template->page);
        }
    }
    
    public function renderCategoryForm($page_id = NULL)
    {
        if($page_id)
        {
            $this->template->page = $this->modelPage->getPage($page_id);
            $this['categoryPageForm']->setDefaults($this->template->page);
        }
    }
    
    public function actionDelete($page_id)
    {
        $this->modelPage->deletePage($page_id);
        $this->flashMessage($this->translator->translate('messages.PageDeletionSuccess'), 'green');
        $this->redirect('PageAdmin:default');
    }
    
    public function actionPreview($page_id)
    {
        $page = $this->modelPage->getPage($page_id);
        if($page->article_id)
        {
            $article = $this->modelArticle->getArticle($page->article_id);
            $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $article->url);
        }
        else
        {
            $category = $this->modelCategory->getCategory($page->category_id);
            $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $category->url);
        }
    }
    
    public function createComponentPageForm()
    {
        $form = new Form();
        $form->addText('name', $this->translator->translate('messages.Name'));
        $form->addSelect('article_id', $this->translator->translate('messages.Article'), $this->modelArticle->getArticlesForSelect());
        $form->addSubmit('submit', (isset($this->getParameters()['page_id']))? $this->translator->translate('messages.Edit') : $this->translator->translate('messages.Create'))
                ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onValidate[] = array($this, 'pageFormValidation');
        $form->onSuccess[] = array($this, 'pageFormSuccess');
        return $form;
    }
    
    public function pageFormValidation($form, $values)
    {
        if($values->name == '')
        {
            $form['name']->addError($this->translator->translate('messages.CouldNotBeEmpty'));
        }
    }
    
    public function pageFormSuccess($form, $values)
    {
        if(isset($this->getParameters()['page_id']))
        {
            $page_id = $this->getParameters()['page_id'];
            $this->modelPage->editPage($page_id, $values);
            $this->flashMessage($this->translator->translate('messages.PageEditationSuccess'), 'green');
        }
        else 
        {
            $this->modelPage->createPage($values);
            $this->flashMessage($this->translator->translate('messages.NewPageCreationSuccess'), 'green');
        }
        $this->redirect('PageAdmin:default');
    }
    
    public function createComponentCategoryPageForm()
    {
        $form = new Form();
        $form->addText('name', $this->translator->translate('messages.Name'))
                ->setAttribute('class', 'validate');
        $form->addSelect('category_id', $this->translator->translate('messages.Category'), $this->modelCategory->getCategoriesForSelect());
        $form->addSubmit('submit', (isset($this->getParameters()['page_id']))? $this->translator->translate('messages.Edit') : $this->translator->translate('messages.Create'))
                ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onValidate[] = array($this, 'pageFormValidate');
        $form->onSuccess[] = array($this, 'pageFormSuccess');
        return $form;
    }
    public function pageFormValidate($form, $values)
    {
        if($values->name == '')
        {
            $form['name']->addError($this->translator->translate('messages.CouldNotBeEmpty'));
        }
        if($values->category_id == '')
        {
            $form['category_id']->addError($this->translator->translate('messages.SelectCategory'));
        }
    }
    
    public function actionChangeOrder()
    {
        $data = $this->getHttpRequest()->getPost()['data'];
        foreach($data as $page)
        {
            $this->modelPage->editPage($page['id'], array('page_order' => $page['order']));
        }
        $this->terminate();
    }

}
