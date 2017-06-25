<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\ArticleModel;
use App\Model\CategoryModel;


class CategoryAdminPresenter extends BasePresenter
{
    private $modelCategory;
    private $modelArticle;
    
    public function __construct(CategoryModel $modelCategory, ArticleModel $modelArticle) 
    {
        $this->modelCategory = $modelCategory;
        $this->modelArticle = $modelArticle;
    }
    
    public function renderDefault()
    {
        $this->template->categories = $this->modelCategory->getCategories();
    }
    
    public function renderForm($category_id = NULL)
    {
        if($category_id)
        {
            $this->template->category = $this->modelCategory->getCategory($category_id);
            $this['categoryForm']->setDefaults($this->template->category);
        }
    }
    
    public function actionDelete($category_id)
    {
        $this->modelCategory->deleteCategory($category_id);
        $this->flashMessage($this->translator->translate('messages.DeletionSuccess'), 'green');
        $this->redirect('CategoryAdmin:default');
    }
    
    public function createComponentCategoryForm()
    {
        $form = new Form();
        $form->addText('name', $this->translator->translate('messages.Name'));
        $form->addCheckbox('showTitle', $this->translator->translate('messages.ShowTitle'))
            ->setAttribute('class', 'filled-in')
            ->setAttribute('id', 'header_footer')
            ->setDefaultValue(true);
        $form->addText('url', $this->translator->translate('messages.url'))
                ->setDefaultValue($this->translator->translate('messages.category') . $this->modelCategory->getMaxCategoryId());
        $form->addTextArea('description', $this->translator->translate('messages.Description'))
                ->setAttribute('class', 'materialize-textarea');
        $form->addSubmit('submit', (isset($this->getParameters()['category_id']))? $this->translator->translate('messages.Edit') : $this->translator->translate('messages.Create'))
                ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onValidate[] = array($this, 'categoryFormValidation');
        $form->onSuccess[] = array($this, 'categoryFormSuccess');
        return $form;
    }
    
    public function categoryFormValidation($form, $values)
    {
        if(isset($this->getParameters()['category_id']))
        {
            $category_id = $this->getParameters()['category_id'];
            $category = $this->modelCategory->getCategory($category_id);
            if($category->url != $values->url)
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
        if($values->name == '')
        {
            $form['name']->addError($this->translator->translate('messages.CouldNotBeEmpty'));
        }
    }
    
    public function categoryFormSuccess($form, $values)
    {
        if(isset($this->getParameters()['category_id']))
        {
            $category_id = $this->getParameters()['category_id'];
            $this->modelCategory->editCategory($category_id, $values);
            $this->flashMessage($this->translator->translate('messages.EditationSuccess'), 'green');
        }
        else 
        {
            $this->modelCategory->createCategory($values);
            $this->flashMessage($this->translator->translate('messages.CreationSuccess'), 'green');
        }
        $this->redirect('CategoryAdmin:default');
    }

    public function actionPreview($category_id)
    {
        $category = $this->modelCategory->getCategory($category_id);
        if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
            $protocol = 'https://';
        }
        else
        {
            $protocol = 'http://';
        }
        $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $category->url);
    }

}
