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
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Zadejte prosím své uživatelské jméno.');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte prosím své heslo.');
        $form->addSubmit('send', 'Přihlásit se');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];
        return $form;
    }

    public function loginFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->getUser()->login($values['username'], $values['password']);
            $this->flashMessage('Jste přihlášen.');
            $this->redirect('Home:default');
        } catch (AuthenticationException $e) {
            $form->addError('Neplatné uživatelské jméno nebo heslo.');
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('Byli jste odhlášeni.');
        $this->redirect('Home:default');
    }
}
