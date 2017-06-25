<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


class Authenticator implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	private $database;
        private $tableUser = 'user';


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
            $login = $credentials[0];
            $password = $credentials[1];
            $row = $this->database->table($this->tableUser)->where('login', $login)->fetch();
            if ($row == null || md5($password) != $row->password) //user is not in database or password is not equal
            {
                throw new Nette\Security\AuthenticationException('Invalid login or password!');
            }
            else //successful authenticate - login
            {
                return new Nette\Security\Identity($row->id, $row->role, array('login' => $row->login));
        }
	}

}



class DuplicateNameException extends \Exception
{}
