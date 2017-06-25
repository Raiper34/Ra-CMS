<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use App\Model\UserModel;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;


class SignAdminPresenter extends BasePresenter
{
    private $modelUser;
    
    public function __construct(UserModel $modelUser) 
    {
        $this->modelUser = $modelUser;
    }

    public function renderDefault()
    {
        if($this->user->isLoggedIn())
        {
            $this->redirect('ArticleAdmin:default');
        }
        $this->template->registrationPermision = $this->modelSetting->getRegistrationPermision();
    }

    public function renderForgottenPassword()
    {

    }

    public function renderForgottenPasswordRecovery($mail, $hash)
    {
        if(!$this->modelUser->compareHash($mail, $hash))
        {
            $this->redirect('SignAdmin:login');
        }
    }

    public function createComponentForgottenPasswordRecovery()
    {
        $form = new Form();
        $form->addPassword('password', $this->translator->translate('messages.Password'))
            ->setAttribute('class', 'validate');
        $form->addPassword('passwordAgain', $this->translator->translate('messages.PasswordAgain'))
            ->setAttribute('class', 'validate');
        $form->addSubmit('submit', $this->translator->translate('messages.Change'))
            ->setAttribute('class', 'btn deep-purple');
        $form->onValidate[] = array($this, 'validatePasswordRecoverySuccess');
        $form->onSuccess[] = array($this, 'forgottenPasswordRecoverySuccess');
        return $form;
    }

    public function validatePasswordRecoverySuccess($form, $values)
    {
        if($values->password != $values->passwordAgain)
        {
            $form['passwordAgain']->addError($this->translator->translate('messages.PasswordsDoNotMatch'));
        }
    }
    public function forgottenPasswordRecoverySuccess($form, $values)
    {
        $this->modelUser->changePassword($this->getParameters()['mail'], $values->password);
        $this->flashMessage($this->translator->translate('messages.PasswordChanged'), 'green');
        $this->redirect('SignAdmin:default');
    }

    public function createComponentForgottenPassword()
    {
        $form = new Form();
        $form->addText('mail', $this->translator->translate('messages.Mail'))
            ->setAttribute('class', 'validate');
        $form->addSubmit('submit', $this->translator->translate('messages.SendRecoveryMail'))
            ->setAttribute('class', 'btn deep-purple');
        $form->onValidate[] = array($this, 'validatePasswordSuccess');
        $form->onSuccess[] = array($this, 'forgottenPasswordSuccess');
        return $form;
    }

    public function validatePasswordSuccess($form, $values)
    {
        if(!$this->modelUser->mailExist($values->mail))
        {
            $form['mail']->addError($this->translator->translate('messages.MailDoesNotExist'));
            $this->flashMessage($this->translator->translate('messages.MailDoesNotExist'), 'red');
        }
    }

    public function forgottenPasswordSuccess($form, $values)
    {
        $hash = md5(strftime('%d.%m.%Y %H:%M'));
        $link = $this->link('//SignAdmin:forgottenPasswordRecovery', $values->mail, $hash);
        $this->modelUser->storeHash($values->mail, $hash);
        $mail = new Message;
        $mail->setFrom('RCMS <racms34@gmail.com>')
            ->addTo($values->mail)
            ->setSubject('RCMS Password reset')
            ->setBody("You can reset your password here: {$link}");
        $mailer = new Nette\Mail\SmtpMailer([
            'host' => 'smtp.gmail.com',
            'username' => 'racms34@gmail.com',
            'password' => 'racms3415',
            'secure' => 'ssl',
        ]);
        $mailer->send($mail);
        $this->flashMessage($this->translator->translate('messages.RecoveryMailSendedCheckYourMail'), 'green');
        $this->redirect('SignAdmin:forgottenPassword');
    }
    
    public function createComponentLogin()
    {
        $form = new Form();
        $form->addText('login', $this->translator->translate('messages.Login'))
                ->setAttribute('class', 'validate');
        $form->addPassword('password', $this->translator->translate('messages.Password'))
                ->setAttribute('class', 'validate');
        $form->addSubmit('submit', $this->translator->translate('messages.Login'))
                ->setAttribute('class', 'btn deep-purple');
        $form->onSuccess[] = array($this, 'loginSuccess');
        return $form;
    }
    
    public function loginSuccess($form, $values)
    {
        $user = $form->getPresenter()->getUser();
	try
	{
            $user->login($values->login, $values->password);
            $user->setExpiration('1 hour', FALSE);
            $form->getPresenter()->flashMessage("Login successful", 'green');
            $form->getPresenter()->redirect("ArticleAdmin:default");
        }
	catch(Nette\Security\AuthenticationException $e) //any error
        {
            $form->getPresenter()->flashMessage($e->getMessage(), 'red');
            $form->getPresenter()->redirect("SignAdmin:default");
	}
    }
    
    public function createComponentRegistration()
    {
        $form = new Form();
        $form->addText('login', $this->translator->translate('messages.Login'))
                ->setAttribute('class', 'validate')
            ->setRequired();
        $form->addText('mail', $this->translator->translate('messages.Mail'))
                ->setAttribute('class', 'validate')
            ->setRequired();
        $form->addPassword('password', $this->translator->translate('messages.Password'))
                ->setAttribute('class', 'validate')
            ->setRequired();
        $form->addPassword('passwordAgain', $this->translator->translate('messages.PasswordAgain'))
            ->setAttribute('class', 'validate')
            ->setRequired();
        $form->addSubmit('submit', $this->translator->translate('messages.SignUp'))
                ->setAttribute('class', 'btn deep-purple');
        $form->onSuccess[] = array($this, 'registrationValidation');
        $form->onSuccess[] = array($this, 'registrationSuccess');
        return $form;
    }

    public function registrationValidation($form, $values)
    {
        if($this->modelUser->mailExist($values->mail))
        {
            $form['mail']->addError($this->translator->translate('messages.MailAlreadyExists'));
        }
        if($this->modelUser->userNameExist($values->login))
        {
            $form['login']->addError($this->translator->translate('messages.LoginAlreadyExists'));
        }
        if($values->password != $values->passwordAgain)
        {
            $form['passwordAgain']->addError($this->translator->translate('messages.PasswordsDoNotMatch'));
        }
    }
    
    public function registrationSuccess($form, $values)
    {
        if($this->modelUser->createUser($values))
        {
            $this->flashMessage($this->translator->translate('messages.RegistrationSuccessful'), 'green');
        }
        else
        {
            $this->flashMessage($this->translator->translate('messages.RegistrationFailure'), 'red');
        }
        $this->redirect('SignAdmin:default');
    }
    
    public function actionLogout()
    {
        $user = $this->getUser();
        $user->logout();
        $this->flashMessage($this->translator->translate('messages.LogoutSuccessful'), 'green');
        $this->redirect('SignAdmin:default');
    }
    
    public function renderPermisionDenied()
    {
        
    }

}
