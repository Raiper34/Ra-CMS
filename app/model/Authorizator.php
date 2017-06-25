<?php

namespace App\Model;

use Nette;

class Authorizator extends Nette\Object implements Nette\Security\IAuthorizator
{

    public function isAllowed($role, $resource = NULL, $privilege = NULL)
    {
        switch($role) //editor
        {
            case 0:
                return $this->editorPermisions($resource, $privilege);
            case 1:
                return $this->adminPermisions();
            default:
                return false;
        }
    }
    
    private function editorPermisions($resources, $privilege)
    {
        switch($resources)
        {
            case 'ArticleAdmin':
                return true;
            case 'FileAdmin':
                return true;
            case 'ProfileAdmin':
                return true;
            case 'SignAdmin':
                return true;
            default:
                return false;
        }
    }
    
    private function adminPermisions()
    {
        return true;
    }

}
