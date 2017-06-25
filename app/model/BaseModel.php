<?php

namespace App\Model;

use Nette;
use Nette\Database\Context;
use Nette\Security\User;

class BaseModel 
{
    
    protected $database;
    protected $user;

    public function __construct(Context $database, User $user) 
    {
        $this->database = $database;
        $this->user = $user;
    }

}
