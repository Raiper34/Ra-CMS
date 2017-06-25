<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\SettingModel;
use App\Model\ArticleModel;


class WebsiteSettingsPresenter extends BasePresenter
{
    
    private $modelArticle;
    
    public function __construct(ArticleModel $modelArticle)
    {
        $this->modelArticle = $modelArticle;
    }

    public function renderDefault()
    {
        $settings = $this->modelSetting->getSiteSettings();
	    $this['settings']->setDefaults($settings);
        $this['scripts']->setDefaults($settings);
        $this['blogSettings']->setDefaults($settings);
        $this->template->colors = $this->modelSetting->getColors();
        $this->template->siteColor = $this->modelSetting->getSiteColor();
        $this->template->locales = $this->modelSetting->getLocales();
        $this->template->languageWebsite = $this->modelSetting->getWebsiteLocale();
    }
    
    public function createComponentSettings()
    {
        $form = new Form();
        $form->addText('site_name', $this->translator->translate('messages.SiteName'))
                ->setAttribute('class', 'validate');
        $form->addSelect('homepage', $this->translator->translate('messages.Homepage'), $this->modelArticle->getArticlesForSelect())
                ->setAttribute('class', 'validate');
        $form->addCheckbox('registration', $this->translator->translate('messages.Registration'))
                ->setAttribute('class', 'filled-in');
        $form->addSelect('default_role', $this->translator->translate('messages.NewUserRole'), array(0 => 'Editor', 1 => 'Admin'))
                ->setAttribute('class', 'filled-in ');
        $form->addSelect('not_found_page', $this->translator->translate('messages.404'), $this->modelArticle->getArticlesForSelect())
                ->setAttribute('class', 'validate');
        $form->addSubmit('submit', $this->translator->translate('messages.Update'))
                ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onSuccess[] = array($this, 'settingsSuccess');
        return $form;
    }

    public function createComponentBlogSettings()
    {
        $form = new Form();
        $form->addText('articles_per_page', $this->translator->translate('messages.ArticlesPerPage'))
            ->setAttribute('class', 'validate');
        $form->addText('articles_preview_length', $this->translator->translate('messages.ArticlesPreviewLength'))
            ->setAttribute('class', 'validate');
        $form->addSelect('date_id', $this->translator->translate('messages.DateFormat'), $this->modelSetting->getDateFormatsForSelect())
            ->setAttribute('class', 'validate');
        $form->addSubmit('submit', $this->translator->translate('messages.Update'))
            ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onValidate[] = array($this, 'blogSettingsValidation');
        $form->onSuccess[] = array($this, 'blogSettingsSuccess');
        return $form;
    }

    public function blogSettingsValidation($form, $values)
    {
        if(!ctype_digit($values->articles_per_page))
        {
            $form['articles_per_page']->addError($this->translator->translate('messages.NeedToBeInteger'));
        }
        if(!ctype_digit($values->articles_preview_length))
        {
            $form['articles_preview_length']->addError($this->translator->translate('messages.NeedToBeInteger'));
        }
    }
    
    public function createComponentScripts()
    {
        $form = new Form();
        $form->addTextArea('scripts', $this->translator->translate('messages.content'))
                ->setAttribute('id', 'tinyArea');
        $form->addSubmit('submit', $this->translator->translate('messages.Update'))
                ->setAttribute('class', 'btn ' . $this->colorClass);
        $form->onSuccess[] = array($this, 'scriptsSuccess');
        return $form;
    }
    
    public function scriptsSuccess($form, $values)
    {
        $this->modelSetting->updateSiteSettings($values);
        $this->flashMessage($this->translator->translate('messages.SettingsUpdatedSuccessful'), 'green');
        $this->redirect('WebsiteSettings:default');
    }
    
    public function settingsSuccess($form, $values)
    {
        $this->modelSetting->updateSiteSettings($values);
        $this->flashMessage($this->translator->translate('messages.SettingsUpdatedSuccessful'), 'green');
        $this->redirect('WebsiteSettings:default');
    }

    public function blogSettingsSuccess($form, $values)
    {
        $this->modelSetting->updateSiteSettings($values);
        $this->flashMessage($this->translator->translate('messages.SettingsUpdatedSuccessful'), 'green');
        $this->redirect('WebsiteSettings:default');
    }
    
    public function actionChangeColor($color_id)
    {
        $this->modelSetting->changeSiteColor($color_id);
        $this->flashMessage($this->translator->translate('messages.ColorWasChanged'), 'green');
        $this->redirect('WebsiteSettings:default');
    }
    
    public function actionChangeLocale($locale_id)
    {
        $this->modelSetting->changeWebsiteLocale($locale_id);
        $this->flashMessage($this->translator->translate('messages.LanguageWasChanged'), 'green');
        $this->redirect('WebsiteSettings:default');
    }

}
