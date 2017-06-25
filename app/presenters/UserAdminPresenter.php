<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\UserModel;


class UserAdminPresenter extends BasePresenter
{
    private $modelUser;
    
    public function __construct(UserModel $modelUser) 
    {
        $this->modelUser = $modelUser;
    }

    public function renderDefault()
    {
	$this->template->users = $this->modelUser->getAllUsers();
    }
    
    

}
