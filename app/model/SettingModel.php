<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

class SettingModel extends BaseModel
{
    
    private $tableSetting = 'administration_setting';
    private $tableSiteSetting = 'site_setting';
    private $tableArticle = 'article';
    private $tableColor = 'color';
    private $tableLocale = 'locale';
    private $tableDate = 'date';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) 
    {
        parent::__construct($database, $user);
        if($this->user->isLoggedIn() && !$this->database->table($this->tableSetting)->where('user_id', $this->user->id)->fetch())
        {
            $this->database->table($this->tableSetting)->insert(array(
                'color_id' => 1,
                'locale_id' => 1,
                'user_id' => $this->user->id
            ));
        }
        if(!$this->database->table($this->tableSiteSetting)->fetch())
        {
            $this->database->table($this->tableSiteSetting)->insert(array(
                'site_name' => '', 
                'color_id' => 1)
            );
        }
    }
    
    public function getColors()
    {
        return $this->database->table($this->tableColor)->fetchAll();
    }

    public function getWebsiteDateFormat()
    {
        $setting = $this->database->table($this->tableSiteSetting)->fetch();
        return $this->database->table($this->tableDate)->where('id', ($setting)? $setting->date_id : 1)->fetch();
    }

    public function getDateFormatsForSelect()
    {
        $dates = array();
        foreach($this->database->table($this->tableDate)->fetchAll() as $date)
        {
            $dates[$date->id] = strftime($date->string,(new \DateTime())->getTimestamp());
        }
        return $dates;
    }
    
    public function changeColor($color_id)
    {
        $this->database->table($this->tableSetting)->where('user_id', $this->user->id)->update(array(
            'color_id' => $color_id
        ));
    }
    
    public function getUserColor()
    {
        $setting = $this->database->table($this->tableSetting)->where('user_id', $this->user->id)->fetch();
        return $this->database->table($this->tableColor)->where('id', ($setting)? $setting->color_id : 1)->fetch();
    }
    
    public function getUserLocale()
    {
        $setting = $this->database->table($this->tableSetting)->where('user_id', $this->user->id)->fetch();
        return $this->database->table($this->tableLocale)->where('id', ($setting)? $setting->locale_id : 1)->fetch();
    }
    
    public function getWebsiteLocale()
    {
        $setting = $this->database->table($this->tableSiteSetting)->fetch();
        return $this->database->table($this->tableLocale)->where('id', ($setting)? $setting->locale_id : 1)->fetch();
    }

    /*public function getDateFormats()
    {
        $setting = $this->database->table($this->tableSiteSetting)->fetch();
        return $this->database->table($this->tableDate)->where('id', ($setting)? $setting->locale_id : 1)->fetch();
    }*/
    
    public function getLocales()
    {
        return $this->database->table($this->tableLocale)->fetchAll();
    }
    
    public function changeLocale($locale_id)
    {
        $this->database->table($this->tableSetting)->where('user_id', $this->user->id)->update(array(
            'locale_id' => $locale_id
        ));
    }
    
    public function changeWebsiteLocale($locale_id)
    {
        $this->database->table($this->tableSiteSetting)->update(array(
            'locale_id' => $locale_id
        ));
    }
    
    public function updateSiteSettings($values)
    {
        if(!$this->database->table($this->tableSiteSetting)->fetch())
        {
            $this->database->table($this->tableSiteSetting)->insert($values);
        }
        else
        {
            $this->database->table($this->tableSiteSetting)->update($values);
        }
        
    }
    
    public function getSiteSettings()
    {
        if($settings = $this->database->table($this->tableSiteSetting)->fetch())
        {
            return $settings;
        }
        else
        {
            return array('site_name' => '', 'color_id' => 1);
        }
    }
    
    public function getSiteColor()
    {
        $setting = $this->database->table($this->tableSiteSetting)->fetch();
        return $this->database->table($this->tableColor)->where('id', ($setting)? $setting['color_id'] : 1)->fetch();
    }
    
    public function changeSiteColor($color_id)
    {
        $this->database->table($this->tableSiteSetting)->update(array(
            'color_id' => $color_id
        ));
    }
    
    public function getHomePage()
    {
        if(($row = $this->database->table('site_setting')->fetch()) && $row->homepage != NULL)
        {
            return $row->homepage;
        }
        else if(($row = $this->database->table('page')->fetch()) && $row->article_id)
        {
            return $row->article_id;
        }
        else
        {
            return $this->database->table('article')->fetch()->id;
        }
    }
    
    public function get404Page()
    {
        if(($row = $this->database->table('site_setting')->fetch()) && $row->not_found_page != NULL)
        {
            return $row->not_found_page;
        }
        else if(($row = $this->database->table('page')->fetch()))
        {
            return $row->article_id;
        }
        else 
        {
            return $this->database->table('article')->fetch()->id;
        }
        
    }
    
    public function getRegistrationPermision()
    {
        return $this->database->table($this->tableSiteSetting)->fetch()->registration;
    }

}
