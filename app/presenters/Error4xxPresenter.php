<?php

namespace App\Presenters;

use Nette;

class Error4xxPresenter extends Nette\Application\UI\Presenter {

    /** @var \App\Model\ArticleModel @inject */
    public $modelArticle;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;

    /** @var \App\Model\SettingModel @inject */
    public $modelSetting;

    /** @var \App\Model\Authorizator @inject */
    public $authorizator;

    public function startup() 
    {
        parent::startup();
        if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
            $this->error();
        }
    }

    public function renderDefault(Nette\Application\BadRequestException $exception) 
    {
        // load template 403.latte or 404.latte or ... 4xx.latte
        //$file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        //Get 404 page
        //$notFoundPageId = $this->modelSetting->get404Page();
        //$this->template->
        //$this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
        $article = $this->modelArticle->getArticle($this->modelSetting->get404Page());
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $this->redirectUrl($this->getHttpRequest()->getUrl()->getBasePath() . $article->url);
    }

}
