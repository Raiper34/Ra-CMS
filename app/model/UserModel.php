<?php

namespace App\Model;

use Nette;

class UserModel extends BaseModel
{
    
    private $tableUser = 'user';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) {
        parent::__construct($database, $user);
    }
    
    public function createUser($values)
    {
        $values->password = md5($values->password);
        if(!$this->userExists($values->mail, $values->login))
        {
            $this->database->table($this->tableUser)->insert($values);
            return true;
        }
        return false;
    }
    
    public function userExists($mail, $login)
    {
        if($this->database->query('SELECT * FROM user WHERE login=? OR mail=?', $login, $mail)->fetchAll())
        {
            return TRUE;
        }
        return FALSE;   
    }

    public function mailExist($mail)
    {
        if($this->database->query('SELECT * FROM user WHERE mail=?', $mail)->fetchAll())
        {
            return TRUE;
        }
        return FALSE;
    }

    public function userNameExist($login)
    {
        if($this->database->query('SELECT * FROM user WHERE login=?', $login)->fetchAll())
        {
            return TRUE;
        }
        return FALSE;
    }

    public function storeHash($mail, $hash)
    {
        $this->database->table($this->tableUser)->where('mail', $mail)->update(array('hash' => $hash));
    }

    public function changePassword($mail, $password)
    {
        $this->database->table($this->tableUser)->where('mail', $mail)->update(array(
            'password' => md5($password),
            'hash' => ''
        ));
    }
    
    public function getAllUsers()
    {
        return $this->database->table('user')->fetchAll();
    }

    public function compareHash($mail, $hash)
    {
        if(($row = $this->database->table($this->tableUser)->where('mail', $mail)->fetch()))
        {
            if($row->hash != '' && $row->hash == $hash)
            {
                return true;
            }
            return false;
        }
        return false;
    }

}
