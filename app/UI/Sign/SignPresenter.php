<?php

namespace App\UI\Sign;

use App\Model\Authenticator;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

final class SignPresenter extends Presenter
{
    public function __construct(
        private Authenticator $authenticator,
    ) {}

    public function renderIn(): void {}

    protected function createComponentLoginForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Username:')
            ->setRequired('Please enter your username.');
        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.');
        $form->addSubmit('send', 'Sign in');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];
        return $form;
    }

    public function loginFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->getUser()->login($values['username'], $values['password']);
            $this->flashMessage('You are now signed in.');
            $this->redirect('Home:default');
        } catch (AuthenticationException $e) {
            $form->addError('Invalid username or password.');
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('You have been signed out.');
        $this->redirect('Home:default');
    }
}
