<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\UserModel;
use App\Model\SettingModel;


class ProfileAdminPresenter extends BasePresenter
{
    private $modelUser;
    public $modelSetting;
    
    public function __construct(UserModel $modelUser, SettingModel $modelSetting) 
    {
        $this->modelUser = $modelUser;
        $this->modelSetting = $modelSetting;
    }

    
    public function renderDefault()
    {
        $this->template->colors = $this->modelSetting->getColors();
        $this->template->locales = $this->modelSetting->getLocales();
    }
    
    public function actionChangeColor($color_id)
    {
        $this->modelSetting->changeColor($color_id);
        $this->flashMessage($this->translator->translate('messages.ColorWasChanged'), 'green');
        $this->redirect('ProfileAdmin:default');
    }
    
    public function actionChangeLocale($locale_id)
    {
        $this->modelSetting->changeLocale($locale_id);
        $this->flashMessage($this->translator->translate('messages.LanguageWasChanged'), 'green');
        $this->redirect('ProfileAdmin:default');
    }
    

}
