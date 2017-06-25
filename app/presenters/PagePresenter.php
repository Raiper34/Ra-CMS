<?php

namespace App\Presenters;

use Nette;
use App\Model\ArticleModel;
use App\Model\SettingModel;
use App\Model\PageModel;
use App\Model\CategoryModel;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class PagePresenter extends Nette\Application\UI\Presenter
{
    public $database;
    private $modelArticle;
    private $modelSetting;
    private $modelPage;
    private $modelCategory;
    public $colorClass;
    public $colorHexa;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;
    
    public function __construct(Nette\Database\Context $database, ArticleModel $modelArticle, 
            SettingModel $modelSetting, PageModel $modelPage, CategoryModel $modelCategory) {
        parent::__construct();
        
        $this->database = $database;
        $this->modelArticle = $modelArticle;
        $this->modelSetting = $modelSetting;
        $this->modelPage = $modelPage;
        $this->modelCategory = $modelCategory;
        
    }
    
    public function beforeRender()
    {
        //Colors
        $color = $this->modelSetting->getSiteColor();
        $this->template->colorClass = $this->colorClass = $color->class;
        $this->template->colorHexa = $this->colorHexa = $color->hexa;
        
        $this->template->menu = $this->modelPage->getPagesForMenu();
        $this->template->mainSetting = $this->modelSetting->getSiteSettings();
        
        //Change layout
        $this->setLayout('layoutPage');
    }

    public function renderDefault()
    {
        $this->setView('article');
        $this->template->article = $this->modelArticle->getArticle($this->modelSetting->getHomePage());
    }
    
    public function renderArticle($article_id)
    {
        $this->template->article = $this->modelArticle->getArticle($article_id);
    }
    
    public function renderCategory($category_id, $page = 0)
    {
        $this->template->articlesPerPage = $this->template->mainSetting->articles_per_page;
        $this->template->category = $this->modelCategory->getCategory($category_id);
        $this->template->articles = $this->modelArticle->getCategoryArticles($category_id, $page, $this->template->articlesPerPage);
        $this->template->categoryCount = $this->modelArticle->getcategoryArticlesCount($category_id);
        $this->template->dateFormat = $this->modelSetting->getWebsiteDateFormat();
        $this->template->actualPage = $page;
    }
    
    public function renderSitemap()
    {
        $this->template->pages = $this->modelPage->getPagesForMenu();
        $this->template->categories = $this->modelCategory->getCategories();
        $this->template->articles = $this->modelArticle->getPublishedArticles();
    }
    
    public function createComponentComment()
    {
        $form = new Form();
        $form->addText('author', $this->translator->translate('messages.Name'));
        $form->addTextArea('content', $this->translator->translate('messages.Comment'))
                ->setAttribute('class', 'materialize-textarea');
        $form->addSubmit('submit', $this->translator->translate('messages.Send'))
                ->setAttribute('class', 'waves-effect waves-light btn ' . $this->colorClass);
        $form->onSuccess[] = array($this, 'commentSuccess');
        return $form;
    }
    
    public function commentSuccess($form, $values)
    {
        
    }

}
