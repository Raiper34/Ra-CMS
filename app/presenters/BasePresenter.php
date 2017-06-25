<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;



/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;
    
    /** @var \App\Model\SettingModel @inject */
    public $modelSetting;
    
    /** @var \App\Model\Authorizator @inject */
    public $authorizator;
    
    public $colorClass;
    public $colorHexa;
    
    const ROLE_EDITOR = 0;
    const ROLE_ADMIN = 1;

    public function afterRender() {
        parent::afterRender();
        
    }
    
    public function formatLayoutTemplateFiles()
    {
        $layoutFiles = parent::formatLayoutTemplateFiles();
        $dir = dirname($this->getReflection()->getFileName());
        $layoutFiles[] = "$dir/../templates/@layout.latte";
        //Debugger::barDump($layoutFiles);
        return $layoutFiles;
    }
    
    public function beforeRender() 
    {
        //Base permision
        if(!$this->user->isLoggedIn() && $this->getName() != 'SignAdmin')
        {
            $this->redirect('SignAdmin:default');
        }
        else
        {
            if(!$this->authorizator->isAllowed($this->user->roles[0], $this->name, $this->action))
            {
                 $this->redirect('SignAdmin:permisionDenied');
            }
        }
        
        //Colors
        $color = $this->modelSetting->getUserColor();
        $this->template->colorClass = $this->colorClass = $color->class;
        $this->template->colorHexa = $this->colorHexa = $color->hexa;
        //Langs
        $lang = $this->modelSetting->getUserLocale();
        $this->translator->setLocale($lang->shortcut);
        $this->template->language = $lang;
    }
}
