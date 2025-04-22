<?php

namespace App\UI\Register;

use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Utils\Validators;

final class RegisterPresenter extends Presenter
{
    public function __construct(
        private UserFacade $users,
    ) {}

    public function renderDefault(): void {}

    protected function createComponentRegisterForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Username:') ->setRequired('Please enter your username.');
        $form->addText('email', 'Email:')->setRequired()->addRule($form::EMAIL);
        $form->addPassword('password', 'Password:')->setRequired()->addRule($form::MIN_LENGTH, 'At least 6 characters', 6);
        $form->addSubmit('send', 'Register');
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];
        return $form;
    }

    public function registerFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->users->register($values ['username'], $values['email'], $values['password']);
            $this->flashMessage('Registration successful. You can now sign in.');
            $this->redirect('Sign:in');
        } catch (\Exception $e) {
            $form->addError($e->getMessage());
        }
    }
}
