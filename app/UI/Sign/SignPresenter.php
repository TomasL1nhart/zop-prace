<?php

namespace App\UI\Sign;

use App\Model\Authenticator;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\Passwords;
use Nette\Database\Explorer;

class SignPresenter extends Presenter
{
    public function __construct(
        private Explorer $db,
        private Authenticator $authenticator,
        private Passwords $passwords,
    ) {}

    public function renderIn(): void {}

    public function renderUp(): void {}

    protected function createComponentLoginForm(): Form
    {
        $form = new Form;
        $form->addText('email', 'Email:')->setRequired();
        $form->addPassword('password', 'Password:')->setRequired();
        $form->addSubmit('send', 'Sign in');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];
        return $form;
    }

    public function loginFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->getUser()->login($values['email'], $values['password']);
            $this->redirect('Home:default');
        } catch (\Nette\Security\AuthenticationException $e) {
            $form->addError('Incorrect credentials.');
        }
    }

    protected function createComponentRegisterForm(): Form
    {
        $form = new Form;
        $form->addText('email', 'Email:')->setRequired();
        $form->addPassword('password', 'Password:')->setRequired()->addRule($form::MIN_LENGTH, 'Min. 6 characters', 6);
        $form->addSubmit('send', 'Sign up');
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];
        return $form;
    }

    public function registerFormSucceeded(Form $form, array $values): void
    {
        $exists = $this->db->table('users')->where('email', $values['email'])->fetch();
        if ($exists) {
            $form->addError('Email is already taken.');
            return;
        }
        $this->db->table('users')->insert([
            'email' => $values['email'],
            'password' => $this->passwords->hash($values['password']),
        ]);
        $this->flashMessage('Registration successful. You can now log in.');
        $this->redirect('Sign:in');
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('You have been signed out.');
        $this->redirect('Home:default');
    }
}
